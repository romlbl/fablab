@props([
    'job',
    'visible' => [],
])

@php
    $actionVisible = in_array('action', $visible, true);
    // vrai si au moins une des colonnes du groupe est visible (évite les groupes vides sur mobile)
    $groupe = fn (array $cols) => count(array_intersect($cols, $visible)) > 0;
@endphp

<div class="job-card-grid flex flex-col gap-3 xl:grid xl:[grid-template-columns:var(--jobs-cols)] xl:gap-x-4 xl:items-start px-4 sm:px-6 py-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow group relative">

    @if (in_array('description', $visible, true))
    <div class="min-w-0 xl:order-1">
        <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Description</span>
        <h4 class="text-sm {{ $job->description ? 'text-slate-900' : 'text-slate-400' }} break-normal">  {{$job->description ?: 'Aucune'}}</h4>
    </div>
    @endif

    @if ($groupe(['machine', 'catégorie', 'consommables']))
    <div class="grid grid-cols-2 gap-x-4 gap-y-3 xl:contents">

        @if (in_array('machine', $visible, true))
        <div class="min-w-0 xl:order-2">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Machine</span>
            <span class="text-xs break-normal {{ $job->machine?->nom ? 'text-slate-800' : 'text-slate-400' }}"> {{$job->machine?->nom ?: 'Aucune'}}</span>
        </div>
        @endif
        

        @if (in_array('catégorie', $visible, true))
        <div class="min-w-0 xl:order-9">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Type</span>
            <span class="text-xs block {{ ($job->id_type == 1 ? $job->machine?->categorie?->nom : ($job->id_type == 2 ? $job->typeTravail?->nom : null)) ? 'text-slate-800' : 'text-slate-400' }}">
                @if ($job->id_type == 1)
                    {{$job->machine?->categorie?->nom ?: 'Aucun'}}
                @elseif ($job->id_type == 2)
                    {{$job->typeTravail?->nom ?: 'Aucun'}}
                @else
                    Aucun
                @endif
            </span>
        </div>
        @endif

        @if (in_array('consommables', $visible, true))
        <div class="col-span-2 xl:col-span-1 min-w-0 space-y-1 font-body text-xs xl:order-3">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Consommable &amp; Quantité</span>

            @forelse ($job->jobConsommables as $jobConsommable)
                <div class="flex items-center justify-between gap-2 w-fit">
                    <div class="flex items-center space-x-1.5 min-w-0">
                        <span class="{{ $jobConsommable->consommable?->nom ? 'text-slate-800' : 'text-slate-400' }} font-medium break-normal"> {{$jobConsommable->consommable?->nom ?: 'Aucun'}}</span>
                        <span class="text-slate-800">·</span>
                        <span class="{{ $jobConsommable->quantite ? 'text-slate-800' : 'text-slate-400' }} whitespace-nowrap"> {{$jobConsommable->quantite ? $jobConsommable->quantite . 'g' : 'Aucun'}}</span>
                    </div>
                    @if (in_array('prix', $visible, true))
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

        @if (in_array('debut', $visible, true))
        <div class="min-w-0 xl:order-6">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Début</span>
            @if ($job->debut)
                <span class="text-xs text-slate-800 whitespace-nowrap">{{ \Carbon\Carbon::parse($job->debut)->format('d/m H:i') }}</span>
            @else
                <span class="text-xs text-slate-400 whitespace-nowrap">Aucun</span>
            @endif
        </div>
        @endif

        @if (in_array('fin', $visible, true))
        <div class="min-w-0 xl:order-7">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Fin</span>
            @if ($job->fin)
                <span class="text-xs text-slate-800 whitespace-nowrap">{{ \Carbon\Carbon::parse($job->fin)->format('d/m H:i') }}</span>
            @else
                <span class="text-xs text-slate-400 whitespace-nowrap">Aucun</span>
            @endif
        </div>
        @endif

    </div>
    @endif

    @if ($groupe(['client', 'projet', 'operateur']))
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3 xl:contents">

        @if (in_array('client', $visible, true))
        <div class="min-w-0 xl:order-4">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Client</span>
            <span class="text-xs break-normal {{ $job->client?->nom ? 'text-slate-800' : 'text-slate-400' }}"> {{$job->client?->nom ?: 'Aucun'}}</span>
        </div>
        @endif

        @if (in_array('projet', $visible, true))
        <div class="min-w-0 xl:order-5">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Projet</span>
            <span class="text-xs break-normal {{ $job->projet?->nom ? 'text-slate-800' : 'text-slate-400' }}"> {{$job->projet?->nom ?: 'Aucun'}}</span>
        </div>
        @endif

        @if (in_array('operateur', $visible, true))
        <div class="min-w-0 xl:order-8">
            <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Opérateur</span>
            <span class="text-xs break-normal {{ $job->operateur?->nom ? 'text-slate-800' : 'text-slate-400' }}"> {{$job->operateur?->nom ?: 'Aucun'}}</span>
        </div>
        @endif

    </div>
    @endif

    @if ($groupe(['statut', 'action']))
    <div class="order-first flex items-center justify-between gap-3 pb-3 border-b border-slate-100 xl:contents">

        @if (in_array('statut', $visible, true))
        <div class="xl:order-10 xl:sticky {{ $actionVisible ? 'xl:right-20' : 'xl:right-0' }} xl:z-30 xl:w-full xl:min-w-0 bg-white xl:pl-2 flex flex-col items-start xl:items-center justify-center xl:shadow-[-6px_0_10px_-4px_rgba(0,0,0,0.06)] self-stretch h-full py-1">
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
            </div>
        </div>
        @endif

        @if ($actionVisible)
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