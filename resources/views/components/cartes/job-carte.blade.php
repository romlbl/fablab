@props(['job', 'visible' => []])

@php
    $voir = fn (string $col) => in_array($col, $visible, true);
    $groupe = fn (array $cols) => count(array_intersect($cols, $visible)) > 0;
    $categorie = $job->categorie?->nom;
@endphp

<div class="job-card-grid flex flex-col gap-3 xl:grid xl:[grid-template-columns:var(--jobs-cols)] xl:gap-x-4 xl:items-start px-4 sm:px-6 py-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow group relative">

    @if ($voir('description'))
        <x-cartes.cellule label="Description" :valeur="$job->description" taille="text-sm" class="xl:order-1" />
    @endif

    @if ($groupe(['machine', 'catégorie', 'consommables']))
        <div class="grid grid-cols-2 gap-x-4 gap-y-3 xl:contents">
            @if ($voir('machine'))
                <x-cartes.cellule label="Machine" :valeur="$job->machine?->nom" class="xl:order-2" />
            @endif
            @if ($voir('catégorie'))
                <x-cartes.cellule label="Catégorie" :valeur="$categorie" class="xl:order-9" />
            @endif
            @if ($voir('consommables'))
                <div class="col-span-2 xl:col-span-1 min-w-0 space-y-1 font-body text-xs xl:order-3">
                    <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Consommable &amp; Quantité</span>

                    @forelse ($job->jobConsommables as $jobConsommable)
                        <div class="flex items-center justify-between gap-2 w-fit">
                            <div class="flex items-center space-x-1.5 min-w-0">
                                <span class="{{ $jobConsommable->consommable?->nom ? 'text-slate-800' : 'text-slate-400' }} font-medium break-normal"> {{$jobConsommable->consommable?->nom ?: 'Aucun'}}</span>
                                <span class="text-slate-800">·</span>
                                <span class="{{ $jobConsommable->quantite ? 'text-slate-800' : 'text-slate-400' }} whitespace-nowrap"> {{$jobConsommable->quantite ? $jobConsommable->quantite . 'g' : 'Aucun'}}</span>
                            </div>
                            @if ($voir('prix'))
                                <span class="{{ $jobConsommable->cout_tot ? 'text-slate-800' : 'text-slate-400' }} whitespace-nowrap ml-5"> {{$jobConsommable->cout_tot ? $jobConsommable->cout_tot . '€' : 'Aucun'}}</span>
                            @endif
                        </div>
                    @empty
                        <span class="text-slate-400">Aucun</span>
                    @endforelse
                </div>
            @endif
        </div>
    @endif

    @if ($groupe(['debut', 'fin']))
        <div class="grid grid-cols-2 gap-x-4 gap-y-3 xl:contents">
            @if ($voir('debut'))
                <x-cartes.cellule label="Début" :valeur="$job->debut?->format('d/m H:i')" taille="text-xs whitespace-nowrap" class="xl:order-6" />
            @endif
            @if ($voir('fin'))
                <x-cartes.cellule label="Fin" :valeur="$job->fin?->format('d/m H:i')" taille="text-xs whitespace-nowrap" class="xl:order-7" />
            @endif
        </div>
    @endif

    @if ($groupe(['client', 'projet', 'operateur']))
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3 xl:contents">
            @if ($voir('client'))
                <x-cartes.cellule label="Client" :valeur="$job->client?->nom" class="xl:order-4" />
            @endif
            @if ($voir('projet'))
                <x-cartes.cellule label="Projet" :valeur="$job->projet?->nom" class="xl:order-5" />
            @endif
            @if ($voir('operateur'))
                <x-cartes.cellule label="Opérateur" :valeur="$job->operateur?->nom" class="xl:order-8" />
            @endif
        </div>
    @endif

    @if ($groupe(['statut', 'action']))
        <div class="order-first flex items-center justify-between gap-3 pb-3 border-b border-slate-100 xl:contents">

            @if ($voir('statut'))
            <div class="xl:order-10 xl:sticky {{ $voir('action') ? 'xl:right-20' : 'xl:right-0' }} xl:z-30 xl:w-full xl:min-w-0 bg-white xl:pl-2 flex flex-col items-start xl:items-center justify-center xl:shadow-[-6px_0_10px_-4px_rgba(0,0,0,0.06)] self-stretch h-full py-1">
                <div class="flex flex-col items-center xl:items-center justify-center">
                    @if ($job->status === 'en cours')
                        <span class="inline-flex items-center whitespace-nowrap space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800"> {{$job->status}} </span>
                        <span
                            x-data="{
                                fin: @js($job->fin),
                                secondesRestantes: 0,
                                intervalle: null,
                                init() {
                                    this.calculerTempsRestant();
                                    this.intervalle = setInterval(() => this.calculerTempsRestant(), 1000);
                                },
                                calculerTempsRestant() {
                                    this.secondesRestantes = Math.max(
                                        0,
                                        Math.floor((Date.parse(this.fin) - Date.now()) / 1000)
                                    );
                                },
                                get tempsRestant() {
                                    const heures = Math.floor(this.secondesRestantes / 3600);
                                    const minutes = Math.floor((this.secondesRestantes % 3600) / 60);
                                    return heures > 0
                                        ? `${heures}h ${minutes.toString().padStart(2, '0')}m`
                                        : `${minutes}m`;
                                }
                            }"
                            x-text="tempsRestant"
                            class="text-[10px] font-semibold text-[#10203E] mt-1 font-body whitespace-nowrap"
                        ></span>
                    @endif
                    @if ($job->status === 'en attente')
                        <span class="inline-flex items-center px-2.5 py-1 whitespace-nowrap rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#ffbe00]/50 text-amber-800">En attente</span>
                    @endif
                    @if ($job->status === 'terminé')
                        <span class="inline-flex items-center px-2.5 py-1 whitespace-nowrap rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">Terminé</span>
                    @endif
                    @if ($job->status === 'validé')
                        <span class="inline-flex items-center px-2.5 py-1 whitespace-nowrap rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#E2912C]/50 text-[#E2912C]">Validé</span>        
                    @endif
                </div>
            </div>
            @endif

            @if ($voir('action'))
            <div class="xl:order-11 xl:sticky xl:right-0 xl:z-30 xl:w-full xl:min-w-0 bg-white xl:pl-2 flex items-center justify-end gap-2 xl:justify-center xl:gap-0 self-stretch h-full">
                <button type="button" data-action="stop-job" class="flex items-center justify-center text-[#E2912C] hover:text-[#ffbe00] transition-colors">
                    <span class="material-symbols-outlined text-2xl">o</span>
                </button>
                <button type="button" data-action="stop-job" class="flex items-center justify-center text-[#E2912C] hover:text-[#ffbe00] transition-colors">
                    <span class="material-symbols-outlined text-2xl">o</span>
                </button>
            </div>
            @endif

        </div>
    @endif

</div>

