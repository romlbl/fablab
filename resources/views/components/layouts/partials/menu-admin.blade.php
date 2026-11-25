@php
    $liensAdmin = [
        ['label' => 'Accueil',        'route' => 'admin.accueil',       'pattern' => 'admin.accueil'],
        ['label' => 'Jobs',           'route' => 'admin.jobs',          'pattern' => 'admin.jobs'],
        ['label' => 'Projets',        'route' => 'admin.projets.index', 'pattern' => 'admin.projets.*'],
        ['label' => 'Machines',       'route' => 'admin.machines',      'pattern' => 'admin.machines'],
        ['label' => 'Consommables',   'route' => 'admin.consos',        'pattern' => 'admin.consos'],
        ['label' => 'Collaborateurs', 'route' => 'admin.collabs',       'pattern' => 'admin.collabs'],
        ['label' => 'Clients',        'route' => 'admin.clients',       'pattern' => 'admin.clients']
    ];
@endphp

<div class="flex flex-col h-full justify-between">
    <div>
        {{-- en-tête user --}}
        <div class="h-16 px-5 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="min-w-0">
                    <h2 class="min-w-0 text-lg font-bold leading-tight text-slate-900">
                        {{ $nom ?? 'Nom Admin' }}
                    </h2>
                    <a class="text-xs text-slate-500">Modifier le mot de passe</a>
                </div>
            </div>

            <button type="button" @click="menuOuvert = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700">
                <x-icones.close class="w-5 h-5" />
            </button>
        </div>

        {{-- nav--}}
        <nav class="p-3 space-y-1">
            @foreach ($liensAdmin as $lien)
                @php $actif = request()->routeIs($lien['pattern']); @endphp
                <a href="{{ route($lien['route']) }}" class="flex items-center rounded-2xl px-4 py-3 text-sm font-semibold {{ $actif ? 'font-bold  bg-[#10203E] text-white shadow-sm transition-all' : ' text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900' }}">
                    <span>{{ $lien['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>
    
    <div class="p-3 border-t border-slate-200">
        <a href="{{ route('connexion') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-rose-50 hover:text-rose-600">
            <x-icones.logout class="w-6 h-6" />
            <span>Déconnexion</span>
        </a>
    </div>
</div>