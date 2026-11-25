@php
    $uid = $this->getId();

    $etiquette = 'mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700 font-sans';
    $champ = 'w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm text-slate-900 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15';
    $champAuto = 'w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-base sm:text-sm font-medium text-slate-800 outline-none transition-all hover:border-slate-300 focus:border-[#10203E] focus:bg-white focus:ring-2 focus:ring-[#10203E]/15';
    $boiteSuggestions = 'absolute left-0 right-0 z-50 max-h-40 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-card';
    $optionSuggestion = 'block w-full px-3.5 py-2.5 text-left text-sm text-slate-700 transition-colors sm:py-2 hover:bg-slate-50 hover:text-[#10203E]';
    $aide = 'mt-1 text-xs text-rose-600';

@endphp

<div
    x-data="{
        active: null,

        job: $wire.$entangle('job'),
        saisie: { machine: '', projet: '', client: '', operateur: '', categorie: '', conception: '' },

        ouvrir() {
            this.active = null;
            if (!this.job.date_debut) this.job.date_debut = this.versLocal(new Date());
            this.open = true;
        },
        fermer() {
            this.active = null;
            this.open = false;
        },
        reinitialiser() {
            this.saisie = { machine: '', projet: '', client: '', operateur: '', categorie: '', conception: '' };
        },
        async soumettre() {
            const ok = await this.$wire.creer();
            if (ok) this.reinitialiser();
        },

        liste(nom) {
            const brut = this.$wire.suggestions?.[nom] ?? [];
            if (Array.isArray(brut)) return brut;
            return Object.entries(brut).map(([id, libelle]) => ({
                id: /^\d+$/.test(id) ? Number(id) : id,
                nom: libelle,
            }));
        },
        options(nom, valeur) {
            const q = (valeur || '').trim().toLocaleLowerCase();
            return this.liste(nom).filter((o) => o.nom.toLocaleLowerCase().includes(q));
        },
        correspondance(nom, valeur) {
            const q = (valeur || '').trim().toLocaleLowerCase();
            return this.liste(nom).find((o) => o.nom.toLocaleLowerCase() === q) ?? null;
        },

        saisir(champ, nom, valeur) {
            this.active = champ;
            this.job[champ + '_id'] = this.correspondance(nom, valeur)?.id ?? null;
        },
        async choisir(champ, option) {
            this.saisie[champ] = option.nom;
            this.job[champ + '_id'] = option.id;
            this.active = null;

            if (champ === 'machine') {
                const categorie = await this.$wire.machineChoisie(option.id);
                this.saisie.categorie = categorie?.nom ?? '';
                this.job.categorie_id = categorie?.id ?? null;
                this.saisie.conception = '';
                this.job.conception_id = null;
            } else if (champ === 'client') {
                const projetConserve = await this.$wire.clientChoisi(option.id);
                if (!projetConserve) {
                    this.saisie.projet = '';
                    this.job.projet_id = null;
                }
            } else if (champ === 'projet') {
                const client = await this.$wire.projetChoisi(option.id);
                this.saisie.client = client?.nom ?? '';
                this.job.client_id = client?.id ?? null;
            }
        },
        choisirPremier(event, champ, nom) {
            const premier = this.options(nom, this.saisie[champ])[0];
            if (!premier) return;
            event.preventDefault();
            this.choisir(champ, premier);
        },

        ajouterLigne() {
            this.job.consommables = [...this.job.consommables, { id: null, nom: '', quantite: '', unite: '' }];
        },
        supprimerLigne(i) {
            this.job.consommables = this.job.consommables.filter((_, index) => index !== i);
        },
        saisirLigne(i, valeur) {
            this.active = 'c' + i;
            const consommable = this.correspondance('consommables', valeur);
            this.job.consommables[i].id = consommable?.id ?? null;
            this.job.consommables[i].unite = consommable?.unite ?? '';
        },
        choisirLigne(i, option) {
            this.job.consommables[i].nom = option.nom;
            this.job.consommables[i].id = option.id;
            this.job.consommables[i].unite = option.unite;
            this.active = null;
        },
        choisirPremierLigne(event, i) {
            const premier = this.options('consommables', this.job.consommables[i].nom)[0];
            if (!premier) return;
            event.preventDefault();
            this.choisirLigne(i, premier);
        },
        versLocal(date) {
            const p = (v) => String(v).padStart(2, '0');
            return `${date.getFullYear()}-${p(date.getMonth() + 1)}-${p(date.getDate())}T${p(date.getHours())}:${p(date.getMinutes())}`;
        },
        majFin() {
            this.$nextTick(() => {
                const h = Number(this.job.duree_heures || 0);
                const m = Number(this.job.duree_minutes || 0);
                if (!this.job.date_debut || (h === 0 && m === 0)) {
                    this.job.date_fin = '';
                    return;
                }
                const debut = new Date(this.job.date_debut);
                if (Number.isNaN(debut.getTime())) {
                    this.job.date_fin = '';
                    return;
                }
                debut.setMinutes(debut.getMinutes() + h * 60 + m);
                this.job.date_fin = this.versLocal(debut);
            });
        },
    }"
    x-init="if (!job.date_debut) job.date_debut = versLocal(new Date())"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    @ouvrir-ajout.window="ouvrir()"
    @fermer-ajout.window="fermer()"
>
    <div @click.self="$dispatch('fermer-ajout')" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overscroll-contains sm:p-6">
        <div class="my-auto flex max-h-[calc(100dvh-2rem)] w-full max-w-2xl flex-col overflow-hidden sm:max-h-[calc(100dvh-3rem)] rounded-3xl border border-slate-200/90 bg-white shadow-modal">

            <div class="flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4 sm:px-8 sm:py-5">
                <h2 id="{{ $uid }}-titre" class="text-lg font-bold tracking-tight text-slate-900">Ajouter un job</h2>
                <button type="button" @click="$dispatch('fermer-ajout')" class="-mr-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 sm:mr-0 sm:h-9 sm:w-9">
                    <x-icones.close class="w-5 h-5" />
                </button>
            </div>

            <form id="{{ $uid }}-form" @submit.prevent="soumettre()" @keydown.enter.prevent class="custom-scroll space-y-5 overflow-y-auto overscroll-contain p-5 font-body sm:space-y-6 sm:p-8">
                <div>
                    <label for="{{ $uid }}-description" class="{{ $etiquette }}">Description du job</label>
                    <input type="text" id="{{ $uid }}-description" x-model="job.description" class="{{ $champ }} placeholder:text-slate-400">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="relative" @click.outside="active === 'machine' && (active = null)">
                        <label for="{{ $uid }}-machine" class="{{ $etiquette }}">Machine</label>
                        <input
                            type="text"
                            id="{{ $uid }}-machine"
                            autocomplete="off"
                            x-model="saisie.machine"
                            @focus="active = 'machine'"
                            @input="saisir('machine', 'machines', $event.target.value)"
                            @keydown.enter="choisirPremier($event, 'machine', 'machines')"
                            @keydown.escape="active = null"
                            class="{{ $champAuto }}"
                        >
                        <p
                            x-show="saisie.machine.trim() && job.machine_id === null && active !== 'machine'"
                            x-cloak
                            style="display: none;"
                            class="{{ $aide }}"
                        >Choisis une valeur dans la liste.</p>
                        <div
                            x-show="active === 'machine' && options('machines', saisie.machine).length"
                            x-cloak
                            style="display: none;"
                            class="{{ $boiteSuggestions }} top-full mt-1.5"
                        >
                            <template x-for="option in options('machines', saisie.machine)" :key="option.id">
                                <button
                                    type="button"
                                    x-text="option.nom"
                                    @click="choisir('machine', option)"
                                    class="{{ $optionSuggestion }}"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <div class="relative" @click.outside="active === 'projet' && (active = null)">
                        <label for="{{ $uid }}-projet" class="{{ $etiquette }}">Projet</label>
                        <input
                            type="text"
                            id="{{ $uid }}-projet"
                            autocomplete="off"
                            x-model="saisie.projet"
                            @focus="active = 'projet'"
                            @input="saisir('projet', 'projets', $event.target.value)"
                            @keydown.enter="choisirPremier($event, 'projet', 'projets')"
                            @keydown.escape="active = null"
                            class="{{ $champAuto }}"
                        >
                        <p
                            x-show="saisie.projet.trim() && job.projet_id === null && active !== 'projet'"
                            x-cloak
                            style="display: none;"
                            class="{{ $aide }}"
                        >Choisis une valeur dans la liste.</p>
                        <div
                            x-show="active === 'projet' && options('projets', saisie.projet).length"
                            x-cloak
                            style="display: none;"
                            class="{{ $boiteSuggestions }} top-full mt-1.5"
                        >
                            <template x-for="option in options('projets', saisie.projet)" :key="option.id">
                                <button
                                    type="button"
                                    x-text="option.nom"
                                    @click="choisir('projet', option)"
                                    class="{{ $optionSuggestion }}"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <div class="relative" @click.outside="active === 'categorie' && (active = null)">
                        <label for="{{ $uid }}-categorie" class="{{ $etiquette }}">Catégorie</label>
                        <input
                            type="text"
                            id="{{ $uid }}-categorie"
                            autocomplete="off"
                            x-model="saisie.categorie"
                            @focus="active = 'categorie'"
                            @input="saisir('categorie', 'categories', $event.target.value)"
                            @keydown.enter="choisirPremier($event, 'categorie', 'categories')"
                            @keydown.escape="active = null"
                            class="{{ $champAuto }}"
                        >
                        <p
                            x-show="saisie.categorie.trim() && job.categorie_id === null && active !== 'categorie'"
                            x-cloak
                            style="display: none;"
                            class="{{ $aide }}"
                        >Choisis une valeur dans la liste.</p>
                        <div
                            x-show="active === 'categorie' && options('categories', saisie.categorie).length"
                            x-cloak
                            style="display: none;"
                            class="{{ $boiteSuggestions }} bottom-full mb-1.5"
                        >
                            <template x-for="option in options('categories', saisie.categorie)" :key="option.id">
                                <button
                                    type="button"
                                    x-text="option.nom"
                                    @click="choisir('categorie', option)"
                                    class="{{ $optionSuggestion }}"
                                ></button>
                            </template>
                        </div>
                    </div>

                    @if ($role === 'admin')
                    <div id="chp_client" class="relative" @click.outside="active === 'client' && (active = null)">
                        <label for="{{ $uid }}-client" class="{{ $etiquette }}">Client</label>
                        <input
                            type="text"
                            id="{{ $uid }}-client"
                            autocomplete="off"
                            x-model="saisie.client"
                            @focus="active = 'client'"
                            @input="saisir('client', 'clients', $event.target.value)"
                            @keydown.enter="choisirPremier($event, 'client', 'clients')"
                            @keydown.escape="active = null"
                            class="{{ $champAuto }}"
                        >
                        <p
                            x-show="saisie.client.trim() && job.client_id === null && active !== 'client'"
                            x-cloak
                            style="display: none;"
                            class="{{ $aide }}"
                        >Choisis une valeur dans la liste.</p>
                        <div
                            x-show="active === 'client' && options('clients', saisie.client).length"
                            x-cloak
                            style="display: none;"
                            class="{{ $boiteSuggestions }} top-full mt-1.5"
                        >
                            <template x-for="option in options('clients', saisie.client)" :key="option.id">
                                <button
                                    type="button"
                                    x-text="option.nom"
                                    @click="choisir('client', option)"
                                    class="{{ $optionSuggestion }}"
                                ></button>
                            </template>
                        </div>
                    </div>
                    @endif
                
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @if ($role ==='admin')
                        <div id="champ_debut">
                            <label for="{{ $uid }}-debut" class="{{ $etiquette }}">Début</label>
                            <input type="datetime-local" id="{{ $uid }}-debut" x-model="job.date_debut" @input="majFin()" class="min-w-0 {{ $champ }}">
                        </div>                       
                    @endif
                    <div>
                        <span class="{{ $etiquette }}">Durée</span>
                        <div class="flex gap-2">
                            <input type="number" x-model="job.duree_heures" @input="majFin()" min="0" step="1" placeholder="h" class="{{ $champ }}">
                            <input type="number" x-model="job.duree_minutes" @input="majFin()" min="0" max="59" step="1" placeholder="min" class="{{ $champ }}">
                        </div>
                    </div>
                    @if ($role === 'admin')
                        <div id="champ_fin">
                            <label for="{{ $uid }}-fin" class="{{ $etiquette }}">Fin</label>
                            <input type="datetime-local" id="{{ $uid }}-fin" x-model="job.date_fin" class="min-w-0 {{ $champ }}">
                        </div>                        
                    @endif
                </div>

                {{-- conso --}}
                <div class="space-y-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 sm:p-5">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-800 font-sans">Consommables &amp; Quantités</span>

                    <div class="space-y-2.5">
                        <template x-for="(ligne, i) in job.consommables" :key="i">
                            <div class="flex flex-wrap items-center gap-2.5 sm:flex-nowrap rounded-xl border border-slate-200/90 bg-white p-2.5 shadow-sm">
                                <div class="relative min-w-0 flex-1 basis-full sm:basis-0" @click.outside="active === 'c' + i && (active = null)">
                                    <input
                                        type="text"
                                        autocomplete="off"
                                        x-model="ligne.nom"
                                        @focus="active = 'c' + i"
                                        @input="saisirLigne(i, $event.target.value)"
                                        @keydown.enter="choisirPremierLigne($event, i)"
                                        @keydown.escape="active = null"
                                        class="w-full border-0 bg-transparent p-0 text-base text-slate-800 outline-none focus:ring-0 sm:text-sm"
                                    >
                                    <div
                                        x-show="active === 'c' + i && options('consommables', ligne.nom).length"
                                        x-cloak
                                        style="display: none;"
                                        class="{{ $boiteSuggestions }} top-full mt-2"
                                    >
                                        <template x-for="option in options('consommables', ligne.nom)" :key="option.id">
                                            <button
                                                type="button"
                                                x-text="option.nom"
                                                @click="choisirLigne(i, option)"
                                                class="{{ $optionSuggestion }}"
                                            ></button>
                                        </template>
                                    </div>
                                </div>

                                <label class="flex w-28 min-w-0 flex-1 items-center rounded-lg bg-slate-50/60 px-2.5 py-1.5 sm:flex-none">
                                    <input
                                        type="number"
                                        min="0"
                                        step="any"
                                        placeholder="0"
                                        x-model="ligne.quantite"
                                        class="w-full border-0 bg-transparent p-0 text-right text-base text-slate-900 outline-none focus:ring-0 sm:text-sm"
                                    >
                                    <span class="ml-1 text-sm text-slate-500" x-text="ligne.unite"></span>
                                </label>

                                <button
                                    type="button"
                                    @click="supprimerLigne(i)"
                                    class="flex h-10 w-10 items-center justify-center text-slate-400 hover:text-rose-600 sm:h-6 sm:w-6"
                                >×</button>

                                <p
                                    x-show="ligne.nom.trim() && ligne.id === null && active !== 'c' + i"
                                    x-cloak
                                    style="display: none;"
                                    class="basis-full text-xs text-rose-600"
                                >Choisis un consommable dans la liste.</p>
                            </div>
                        </template>
                    </div>

                    <button
                        type="button"
                        @click="ajouterLigne()"
                        class="inline-flex items-center space-x-1.5 rounded-xl border border-dashed border-[#10203E]/40 bg-white px-3 py-2.5 text-sm font-semibold text-[#10203E] sm:py-1.5 sm:text-xs transition-all hover:border-[#10203E] hover:bg-slate-100/80"
                    >
                        <x-icones.ajouter class="w-4 h-4" /><span>Ajouter un consommable</span>
                    </button>
                </div>

                {{-- opé et conception --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @if ($role ==='admin')
                    <div class="relative" @click.outside="active === 'operateur' && (active = null)">
                        <label for="{{ $uid }}-operateur" class="{{ $etiquette }}">Opérateur</label>
                        <input
                            type="text"
                            id="{{ $uid }}-operateur"
                            autocomplete="off"
                            x-model="saisie.operateur"
                            @focus="active = 'operateur'"
                            @input="saisir('operateur', 'operateurs', $event.target.value)"
                            @keydown.enter="choisirPremier($event, 'operateur', 'operateurs')"
                            @keydown.escape="active = null"
                            class="{{ $champAuto }}"
                        >
                        <p
                            x-show="saisie.operateur.trim() && job.operateur_id === null && active !== 'operateur'"
                            x-cloak
                            style="display: none;"
                            class="{{ $aide }}"
                        >Choisis une valeur dans la liste.</p>
                        <div
                            x-show="active === 'operateur' && options('operateurs', saisie.operateur).length"
                            x-cloak
                            style="display: none;"
                            class="{{ $boiteSuggestions }} bottom-full mb-1.5"
                        >
                            <template x-for="option in options('operateurs', saisie.operateur)" :key="option.id">
                                <button
                                    type="button"
                                    x-text="option.nom"
                                    @click="choisir('operateur', option)"
                                    class="{{ $optionSuggestion }}"
                                ></button>
                            </template>
                        </div>
                    </div>
                    @endif

                    <div
                        x-show="!job.machine_id"
                        class="relative"
                        @click.outside="active === 'conception' && (active = null)"
                    >
                        <label for="{{ $uid }}-conception" class="{{ $etiquette }}">Conception</label>
                        <input
                            type="text"
                            id="{{ $uid }}-conception"
                            autocomplete="off"
                            x-model="saisie.conception"
                            @focus="active = 'conception'"
                            @input="saisir('conception', 'conceptions', $event.target.value)"
                            @keydown.enter="choisirPremier($event, 'conception', 'conceptions')"
                            @keydown.escape="active = null"
                            class="{{ $champAuto }}"
                        >
                        <p
                            x-show="saisie.conception.trim() && job.conception_id === null && active !== 'conception'"
                            x-cloak
                            style="display: none;"
                            class="{{ $aide }}"
                        >Choisis une valeur dans la liste.</p>
                        <div
                            x-show="active === 'conception' && options('conceptions', saisie.conception).length"
                            x-cloak
                            style="display: none;"
                            class="{{ $boiteSuggestions }} bottom-full mb-1.5"
                        >
                            <template x-for="option in options('conceptions', saisie.conception)" :key="option.id">
                                <button
                                    type="button"
                                    x-text="option.nom"
                                    @click="choisir('conception', option)"
                                    class="{{ $optionSuggestion }}"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>
            </form>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/80 px-6 py-4 sm:px-8">
                <button
                    type="button"
                    @click="$dispatch('fermer-ajout')"
                    class="flex-1 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-200/60 hover:text-slate-900 sm:flex-none sm:py-2.5 sm:text-xs"
                >Annuler</button>
                <button
                    type="submit"
                    form="{{ $uid }}-form"
                    wire:loading.attr="disabled"
                    wire:target="creer"
                    class="flex-1 rounded-xl bg-[#10203E] px-5 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-[#004e60] active:scale-95 disabled:opacity-60 sm:flex-none sm:py-2.5 sm:text-xs"
                >Créer le job</button>
            </div>
        </div>
    </div>
</div>