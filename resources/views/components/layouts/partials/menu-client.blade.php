@php
    $liensClient = [
        ['label' => 'Accueil',      'route' => 'client.accueil',       'pattern' => 'client.accueil'],
        ['label' => 'Projets',      'route' => 'client.projets.index', 'pattern' => 'client.projets.*'],
        ['label' => 'Machines',     'route' => 'client.machines',      'pattern' => 'client.machines'],
        ['label' => 'Consommables', 'route' => 'client.consos',        'pattern' => 'client.consos']
    ];
@endphp

<div class="flex flex-col h-full  justify-between">
    <div class="space-y-8"> 
        {{-- en-tête user --}}
        <div class="border-b border-slate-100 pb-2 pt-2">
            <div class="flex items-start justify-between gap-3">
                <h2 class="min-w-0 text-lg font-bold leading-tight text-slate-900">
                    {{ $nom ?? 'Nom Client' }}
                </h2>

                <button id="close-sidebar" type="button" aria-label="Fermer le menu" class="tap-square -mr-2 -mt-1 inline-flex h-10 w-10 flex-none items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700 lg:hidden">
                    <x-icones.close class="w-5 h-5 hover:cursor-pointer" />
                </button>

            </div>
        </div>

        {{-- nav--}}
        <nav class="space-y-1.5">
            @foreach ($liensClient as $lien)
                @php $actif = request()->routeIs($lien['pattern']); @endphp
                <a href="{{ route($lien['route']) }}" class="flex items-center rounded-2xl px-4 py-3 text-sm font-semibold {{ $actif ? 'font-bold  bg-[#10203E] text-white shadow-sm transition-all' : ' text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900' }}">
                    <span>{{ $lien['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="mt-6 border-t border-slate-100 pt-6 lg:mt-0">
        <x-layouts.partials.deconnexion />
    </div>
</div>