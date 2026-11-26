@props(['prefixe'])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5 w-full']) }}>
    <span class="text-xs font-semibold text-slate-600">Période</span>
    <div class="relative w-full"
        x-data="{
            ouvert: false,
            dateDebut: $wire.entangle('{{ $prefixe }}.dateDebut').live,
            dateFin: $wire.entangle('{{ $prefixe }}.dateFin').live,
            curseur: new Date(),
            dateSurvolee: null,

            versIso(d) {
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const j = String(d.getDate()).padStart(2, '0');
                return `${d.getFullYear()}-${m}-${j}`;
            },
            formaterCourt(iso) {
                const [a, m, j] = iso.split('-');
                return `${j}/${m}/${a}`;
            },
            get libelleAffichage() {
                if (!this.dateDebut) return '';
                const fin = this.dateFin ? this.formaterCourt(this.dateFin) : '...';
                return `${this.formaterCourt(this.dateDebut)} → ${fin}`;
            },
            get titreMois() {
                return this.curseur.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
            },
            get jours() {
                const a = this.curseur.getFullYear();
                const m = this.curseur.getMonth();
                const decalage = (new Date(a, m, 1).getDay() + 6) % 7;
                const nbJours = new Date(a, m + 1, 0).getDate();
                const cases = Array.from({ length: decalage }, () => ({ iso: null, jour: null }));
                for (let j = 1; j <= nbJours; j++) {
                    cases.push({ iso: this.versIso(new Date(a, m, j)), jour: j });
                }
                return cases;
            },
            changerMois(delta) {
                this.curseur = new Date(this.curseur.getFullYear(), this.curseur.getMonth() + delta, 1);
            },
            choisir(iso) {
                if (!this.dateDebut || this.dateFin) {
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
            appliquerPredefinie(valeur) {
                const auj = new Date();
                let debut = new Date(auj);
                let fin = new Date(auj);
                if (valeur === 'week') {
                    debut.setDate(auj.getDate() - ((auj.getDay() + 6) % 7));
                    fin = new Date(debut);
                    fin.setDate(debut.getDate() + 6);
                } else if (valeur === 'month') {
                    debut = new Date(auj.getFullYear(), auj.getMonth(), 1);
                    fin = new Date(auj.getFullYear(), auj.getMonth() + 1, 0);
                }
                this.dateDebut = this.versIso(debut);
                this.dateFin = this.versIso(fin);
                this.curseur = new Date(debut.getFullYear(), debut.getMonth(), 1);
                this.ouvert = false;
            },
            dansPlage(iso) {
                const fin = this.dateFin || this.dateSurvolee;
                if (!iso || !this.dateDebut || !fin) return false;
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

        <div class="relative flex items-center">
            <x-icones.calendrier class="pointer-events-none absolute left-3 h-4 w-4 text-slate-400" />
            <input type="text" readonly :value="libelleAffichage" @click="ouvert = !ouvert" placeholder="Plage de dates" class="h-10 w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50/70 py-2 pl-9 pr-8 text-sm leading-5 font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-[#10203E]/40 focus:bg-white focus:ring-4 focus:ring-[#10203E]/10 placeholder:font-normal placeholder:text-slate-400">
            <button type="button" x-show="dateDebut || dateFin" x-cloak @click.stop="effacer()" class="absolute right-2 flex h-5 w-5 items-center justify-center rounded-full text-slate-400 transition hover:bg-red-100 hover:text-red-600">×</button>
        </div>

        <div x-show="ouvert" x-cloak class="absolute bottom-full left-0 z-50 mb-1.5 w-72 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
            <div class="mb-3 flex items-center justify-between">
                <button type="button" @click="changerMois(-1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-base font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-[#10203E]">‹</button>
                <span x-text="titreMois" class="text-sm font-semibold capitalize text-slate-800"></span>
                <button type="button" @click="changerMois(1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-base font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-[#10203E]">›</button>
            </div>

            <div class="grid grid-cols-7 gap-0.5 border-b border-slate-100 pb-1.5 text-center text-[11px] font-medium text-slate-400">
                @foreach (['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'] as $jour)
                    <span>{{ $jour }}</span>
                @endforeach
            </div>

            <div class="mt-1.5 grid grid-cols-7 gap-y-0.5 text-center text-xs" @mouseleave="dateSurvolee = null">
                <template x-for="(caseJour, idx) in jours" :key="idx">
                    <div class="flex items-center justify-center">
                        <template x-if="caseJour.iso">
                            <button type="button" class="h-8 w-8 rounded-lg text-xs font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]/40"
                                @click="choisir(caseJour.iso)"
                                @mouseenter="dateSurvolee = caseJour.iso"
                                x-text="caseJour.jour"
                                :class="{
                                    'bg-[#10203E] font-bold text-white': estBorne(caseJour.iso),
                                    'bg-[#10203E]/15 text-[#10203E]': dansPlage(caseJour.iso),
                                    'text-slate-700 hover:bg-slate-100': !estBorne(caseJour.iso) && !dansPlage(caseJour.iso)
                                }"></button>
                        </template>
                        <template x-if="!caseJour.iso">
                            <span class="block h-8 w-8"></span>
                        </template>
                    </div>
                </template>
            </div>

            <div class="mt-2 flex gap-1 border-t border-slate-100 pt-2">
                @foreach (['today' => "Aujourd'hui", 'week' => 'Cette semaine', 'month' => 'Ce mois-ci'] as $valeur => $libelle)
                    <button type="button" @click="appliquerPredefinie('{{ $valeur }}')" class="flex-1 rounded-lg px-2 py-1.5 text-[11px] font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-[#10203E]">{{ $libelle }}</button>
                @endforeach
            </div>
        </div>
    </div>
</div>