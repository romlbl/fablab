@props([
    'items' => [],
    'filtres' => [],
])

<div {{ $attributes->merge(['class' => 'my-auto w-full max-w-xl space-y-5 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl shadow-slate-900/15 ring-1 ring-slate-900/5 sm:space-y-6 sm:p-8']) }}>
    <div class="flex items-start justify-between gap-3">
        <h2 class="text-xl font-bold tracking-tight text-slate-900" data-title>
            Exporter les jobs
        </h2>
        <button type="button" @click="$dispatch('fermer-export')" class="-mr-2 -mt-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 sm:m-0 sm:h-auto sm:w-auto sm:p-1.5">
            <span class="text-2xl font-light leading-none">×</span>
        </button>
    </div>

    <form wire:submit.prevent="exporter" class="space-y-5">
        <div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach ($items as $cle => $item)
                    @php
                        $libelle = $item[0];
                        $choix = $item[1] ?? [];
                    @endphp

                    <div class="flex flex-col gap-1.5 w-full" wire:key="export-champ-{{ $cle }}">
                        <label for="export-champ-{{ $cle }}" class="text-xs font-semibold text-slate-700">
                            {{ $libelle }}
                        </label>

                        <div
                            class="relative w-full"
                            x-data="{
                                ouvert: false,
                                recherche: '',
                                choix: @js($choix),
                                selectionnes: $wire.entangle('export.{{ $cle }}').live,
                                get idsSelectionnes() {
                                    return Array.isArray(this.selectionnes) ? this.selectionnes.map(String) : [];
                                },
                                get resultats() {
                                    const q = this.recherche.trim().toLowerCase();
                                    return Object.entries(this.choix).filter(([id, nom]) => {
                                        if (this.idsSelectionnes.includes(String(id))) return false;
                                        if (!q) return true;
                                        return String(nom).toLowerCase().includes(q);
                                    });
                                },
                                ajouter(id) {
                                    const val = String(id);
                                    if (!this.idsSelectionnes.includes(val)) {
                                        this.selectionnes = [...this.idsSelectionnes, val];
                                    }
                                    this.recherche = '';
                                    this.ouvert = false;
                                    this.$refs.champ.focus();
                                },
                                retirer(id) {
                                    const val = String(id);
                                    this.selectionnes = this.idsSelectionnes.filter(elt => elt !== val);
                                },
                                libelleDe(id) {
                                    return this.choix[id] ?? id;
                                }
                            }"
                            @click.outside="ouvert = false">
                            <div @click="$refs.champ.focus()" class="flex min-h-[42px] cursor-text flex-col items-stretch gap-1.5 rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 transition focus-within:border-[#10203E]/40 focus-within:bg-white focus-within:ring-4 focus-within:ring-[#10203E]/10 hover:border-slate-300">
                                <div class="flex items-center gap-2">
                                    <x-icones.rechercher class="h-4 w-4 shrink-0 text-slate-400" />
                                    <input
                                        id="export-champ-{{ $cle }}"
                                        x-ref="champ"
                                        type="text"
                                        x-model="recherche"
                                        @focus="ouvert = true"
                                        @click.stop="ouvert = true"
                                        @keydown.escape="ouvert = false"
                                        @keydown.backspace="if (!recherche && idsSelectionnes.length) retirer(idsSelectionnes[idsSelectionnes.length - 1])"
                                        placeholder="{{ $libelle }}"
                                        autocomplete="off"
                                        class="min-w-0 w-full border-0 bg-transparent p-0 text-sm leading-5 font-medium text-slate-700 outline-none focus:ring-0 placeholder:font-normal placeholder:text-slate-400 sm:text-xs">
                                </div>

                                <template x-if="idsSelectionnes.length">
                                    <div class="flex max-h-[68px] w-full flex-wrap content-start items-start gap-1.5 overflow-y-auto border-t border-slate-200/70 pt-2">
                                        <template x-for="id in idsSelectionnes" :key="id">
                                            <span class="inline-flex max-w-full items-center gap-1 rounded-full bg-[#10203E]/10 py-0.5 pl-2.5 pr-1 text-[11px] font-semibold leading-4 text-[#10203E] ring-1 ring-inset ring-[#10203E]/10">
                                                <span x-text="libelleDe(id)" class="min-w-0 break-words"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="retirer(id)"
                                                    class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full leading-none text-[#10203E]/60 transition hover:bg-red-100 hover:text-red-600"
                                                >×</button>
                                            </span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <ul x-show="ouvert && resultats.length > 0" x-cloak class="absolute left-0 right-0 top-full z-50 mt-1.5 max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
                                <template x-for="[id, nom] in resultats" :key="id">
                                    <li>
                                        <button
                                            type="button"
                                            @click="ajouter(id)"
                                            x-text="nom"
                                            class="block w-full rounded-lg px-3 py-1.5 text-left text-xs font-medium text-slate-700 transition hover:bg-[#10203E]/5 hover:text-[#10203E] focus:bg-[#10203E]/5 focus:outline-none"
                                        ></button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- calendrier -->
        <div
            class="space-y-2.5 pt-1"
            wire:key="export-periode"
            x-data="{
                ouvert: false,
                dateDebut: $wire.entangle('export.dateDebut').live,
                dateFin: $wire.entangle('export.dateFin').live,
                curseur: new Date(),
                dateSurvolee: null,

                versIso(d) {
                    const a = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const j = String(d.getDate()).padStart(2, '0');
                    return `${a}-${m}-${j}`;
                },

                formaterCourt(iso) {
                    if (!iso) return '';
                    const [a, m, j] = iso.split('-');
                    return `${j}/${m}/${a}`;
                },

                get libelleAffichage() {
                    if (this.dateDebut && this.dateFin) {
                        return `${this.formaterCourt(this.dateDebut)} → ${this.formaterCourt(this.dateFin)}`;
                    }
                    if (this.dateDebut) {
                        return `${this.formaterCourt(this.dateDebut)} → ...`;
                    }
                    return '';
                },

                get titreMois() {
                    return this.curseur.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
                },

                get jours() {
                    const a = this.curseur.getFullYear();
                    const m = this.curseur.getMonth();
                    const premierJour = new Date(a, m, 1);
                    const dernierJour = new Date(a, m + 1, 0);

                    const decalageDebut = (premierJour.getDay() + 6) % 7;
                    const cases = [];

                    for (let i = 0; i < decalageDebut; i++) {
                        cases.push({ iso: null, jour: null });
                    }
                    for (let j = 1; j <= dernierJour.getDate(); j++) {
                        const dateObj = new Date(a, m, j);
                        cases.push({ iso: this.versIso(dateObj), jour: j });
                    }
                    return cases;
                },

                appliquerPredefinie(valeur) {
                    const aujourdHui = new Date();
                    const isoAuj = this.versIso(aujourdHui);

                    if (valeur === 'all') {
                        this.dateDebut = null;
                        this.dateFin = null;
                    } else if (valeur === 'today') {
                        this.dateDebut = isoAuj;
                        this.dateFin = isoAuj;
                        this.curseur = new Date(aujourdHui.getFullYear(), aujourdHui.getMonth(), 1);
                    } else if (valeur === 'week') {
                        const decalage = (aujourdHui.getDay() + 6) % 7;
                        const lundi = new Date(aujourdHui);
                        lundi.setDate(aujourdHui.getDate() - decalage);
                        const dimanche = new Date(lundi);
                        dimanche.setDate(lundi.getDate() + 6);
                        this.dateDebut = this.versIso(lundi);
                        this.dateFin = this.versIso(dimanche);
                        this.curseur = new Date(lundi.getFullYear(), lundi.getMonth(), 1);
                    } else if (valeur === 'month') {
                        const premier = new Date(aujourdHui.getFullYear(), aujourdHui.getMonth(), 1);
                        const dernier = new Date(aujourdHui.getFullYear(), aujourdHui.getMonth() + 1, 0);
                        this.dateDebut = this.versIso(premier);
                        this.dateFin = this.versIso(dernier);
                        this.curseur = premier;
                    }
                },

                moisPrecedent() {
                    this.curseur = new Date(this.curseur.getFullYear(), this.curseur.getMonth() - 1, 1);
                },

                moisSuivant() {
                    this.curseur = new Date(this.curseur.getFullYear(), this.curseur.getMonth() + 1, 1);
                },

                choisir(iso) {
                    if (!iso) return;

                    if (!this.dateDebut || (this.dateDebut && this.dateFin)) {
                        this.dateDebut = iso;
                        this.dateFin = null;
                        return;
                    }

                    if (iso < this.dateDebut) {
                        this.dateFin = this.dateDebut;
                        this.dateDebut = iso;
                    } else {
                        this.dateFin = iso;
                    }
                    this.ouvert = false;
                },

                dansPlage(iso) {
                    if (!iso) return false;
                    const fin = this.dateFin || this.dateSurvolee;
                    if (!this.dateDebut || !fin) return false;
                    const min = this.dateDebut < fin ? this.dateDebut : fin;
                    const max = this.dateDebut > fin ? this.dateDebut : fin;
                    return iso > min && iso < max;
                },

                estBorne(iso) {
                    return iso && (iso === this.dateDebut || iso === this.dateFin);
                },

                effacer() {
                    this.dateDebut = null;
                    this.dateFin = null;
                    this.dateSurvolee = null;
                }
            }"
            @click.outside="ouvert = false">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Période
            </span>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="flex flex-col gap-1.5 text-xs font-semibold text-slate-700">
                    <span>Plage prédéfinie</span>
                    <select name="period"  @change="appliquerPredefinie($event.target.value)" class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-sm font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-[#10203E]/40 focus:bg-white focus:ring-4 focus:ring-[#10203E]/10 sm:text-xs">
                        <option value="all">Toutes les dates</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois-ci</option>
                        <option value="custom">Période personnalisée</option>
                    </select>
                </label>

                <div class="flex flex-col gap-1.5">
                    <span class="text-xs font-semibold text-slate-700">Dates (Du → Au)</span>
                    <div class="relative w-full">
                        <div class="relative flex items-center">
                            <x-icones.calendrier class="pointer-events-none absolute left-3 h-4 w-4 text-slate-400" />
                            <input type="text" readonly :value="libelleAffichage" @click="ouvert = !ouvert" placeholder="Plage de dates" class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50/70 py-2 pl-9 pr-8 text-sm leading-5 font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-[#10203E]/40 focus:bg-white focus:ring-4 focus:ring-[#10203E]/10 placeholder:font-normal placeholder:text-slate-400 sm:text-xs">
                            <button type="button" x-show="dateDebut || dateFin" x-cloak @click.stop="effacer()" class="absolute right-2 flex h-5 w-5 items-center justify-center rounded-full text-slate-400 transition hover:bg-red-100 hover:text-red-600">×</button>
                        </div>

                        <div x-show="ouvert" x-cloak class="absolute bottom-full right-0 z-50 mb-1.5 w-72 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5 sm:left-0 sm:right-auto lg:bottom-auto lg:top-full lg:mb-0 lg:mt-1.5">
                            <div class="mb-3 flex items-center justify-between">
                                <button type="button" @click="moisPrecedent()" class="flex h-8 w-8 items-center justify-center rounded-lg text-base font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-[#10203E]">‹</button>
                                <span x-text="titreMois" class="text-sm font-semibold capitalize text-slate-800"></span>
                                <button type="button" @click="moisSuivant()" class="flex h-8 w-8 items-center justify-center rounded-lg text-base font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-[#10203E]">›</button>
                            </div>

                            <div class="grid grid-cols-7 gap-0.5 border-b border-slate-100 pb-1.5 text-center text-[11px] font-medium text-slate-400">
                                <span>Lu</span><span>Ma</span><span>Me</span><span>Je</span><span>Ve</span><span>Sa</span><span>Di</span>
                            </div>

                            <div class="mt-1.5 grid grid-cols-7 gap-y-0.5 text-center text-xs" @mouseleave="dateSurvolee = null">
                                <template x-for="(caseJour, idx) in jours" :key="idx">
                                    <div class="flex items-center justify-center">
                                        <template x-if="caseJour.iso">
                                            <button
                                                type="button"
                                                class="h-8 w-8 rounded-lg text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]/40"
                                                @click="choisir(caseJour.iso)"
                                                @mouseenter="dateSurvolee = caseJour.iso"
                                                x-text="caseJour.jour"
                                                :class="{
                                                    'bg-[#10203E] font-bold text-white': estBorne(caseJour.iso),
                                                    'bg-[#10203E]/15 text-[#10203E]': dansPlage(caseJour.iso),
                                                    'text-slate-700 hover:bg-slate-100': !estBorne(caseJour.iso) && !dansPlage(caseJour.iso)
                                                }"
                                            ></button>
                                        </template>
                                        <template x-if="!caseJour.iso">
                                            <span class="block h-8 w-8"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- format -->
        <div class="space-y-2.5 pt-1" x-data="{ format: $wire.entangle('export.format').live ?? 'xlsx' }">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                Format du fichier
            </span>
            <div class="grid grid-cols-2 gap-3">
                <label @click="format = 'xlsx'" :class="format === 'xlsx' ? 'border-2 border-[#10203E] bg-[#10203E]/5' : 'border border-slate-200 hover:border-slate-300'"  class="flex cursor-pointer items-center gap-2 rounded-2xl p-3 transition">
                    <input type="radio" name="format" value="xlsx" x-model="format" class="text-[#10203E] focus:ring-[#10203E]">
                    <span class="text-xs font-bold text-slate-800">Excel (.xlsx)</span>
                </label>
                <label @click="format = 'csv'" :class="format === 'csv' ? 'border-2 border-[#10203E] bg-[#10203E]/5' : 'border border-slate-200 hover:border-slate-300'" class="flex cursor-pointer items-center gap-2 rounded-2xl p-3 transition">
                    <input type="radio" name="format" value="csv" x-model="format" class="text-[#10203E] focus:ring-[#10203E]">
                    <span class="text-xs font-semibold text-slate-800">CSV (.csv)</span>
                </label>
            </div>
        </div>

        <!-- actions -->
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
            <button type="button" @click="$dispatch('fermer-export')" data-close class="flex-1 rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 sm:flex-none sm:py-2.5">
                Annuler
            </button>
            <button type="submit" wire:click="exporter()" class="flex-1 rounded-xl bg-[#10203E] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#004e60] sm:flex-none sm:py-2.5">
                Exporter
            </button>
        </div>
    </form>
</div>