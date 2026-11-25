@props([
    'titre' => '',
    'espace' => 'client', 
    'nomUtilisateur' => null,
])

@php
    $nomAffiche = $nomUtilisateur ?? session('auth_user_nom') ?? ($espace === 'admin' ? 'Nom Admin' : 'Nom Client');
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }} - Fablab</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ menuOuvert: false }" class="min-h-full bg-[#FAF8FF] text-slate-800 antialiased font-sans selection:bg-[#00b1d8]/20 selection:text-[#10203E]">

    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- en-tête mobile --}} 
        @include('layouts.partials.entete-mobile', [
            'titre' => $titre,
            'nom' => $nomAffiche,
            'espace' => $espace,
        ])

        {{-- fond sombre mobile --}}
        <div x-show="menuOuvert" x-transition.opacity @click="menuOuvert = false" class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" style="display: none;"></div>

        {{-- sidebar --}}
        <aside :class="menuOuvert ? 'translate-x-0' : '-translate-x-full'" 
        class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] overflow-y-auto border-r border-slate-200/80 bg-white p-6 pt-[max(1.5rem,env(safe-area-inset-top))] flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 shrink-0">
            @if ($espace === 'admin')
                @include('layouts.partials.menu-admin', ['nom' => $nomAffiche])
            @else
                @include('layouts.partials.menu-client', ['nom' => $nomAffiche])
            @endif
        </aside>

        {{-- contenu principal --}}
        <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 lg:h-screen lg:overflow-hidden lg:overflow-y-auto ">
            {{ $slot }}
        </main>

    </div>

    @livewireScripts
</body>
</html>