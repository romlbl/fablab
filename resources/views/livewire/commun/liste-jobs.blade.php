@props(['jobs' => [], 'visible' => []])

@php
    // min = largeur minimale (px) ; fr = poids de répartition (0 = largeur fixe)
    $colonnes = [
        'description'  => ['label' => 'Description',            'min' => 200, 'fr' => 3],
        'machine'      => ['label' => 'Machine',                'min' => 110, 'fr' => 1.5],
        'consommables' => ['label' => 'Consommable & Quantité', 'min' => 230, 'fr' => 3],
        'client'       => ['label' => 'Client',                 'min' => 110, 'fr' => 1.5],
        'projet'       => ['label' => 'Projet',                 'min' => 110, 'fr' => 1.5],
        'debut'        => ['label' => 'Début',                  'min' => 90,  'fr' => 1],
        'fin'          => ['label' => 'Fin',                    'min' => 90,  'fr' => 1],
        'operateur'    => ['label' => 'Opérateur',              'min' => 110, 'fr' => 1.5],
        'catégorie'    => ['label' => 'Catégorie',              'min' => 110, 'fr' => 1.5],
        'statut'       => ['label' => 'Statut',                 'min' => 112, 'fr' => 0],
        'action'       => ['label' => 'Action',                 'min' => 80,  'fr' => 0],
    ];
 
    $affichees = array_filter(
        $colonnes,
        fn ($col, $cle) => in_array($cle, $visible, true),
        ARRAY_FILTER_USE_BOTH
    );

    $gridCols = collect($affichees)
        ->map(fn ($c) => $c['fr'] > 0 ? "minmax({$c['min']}px, {$c['fr']}fr)" : "{$c['min']}px")
        ->implode(' ');

    // somme des min + padding (px-6 = 48) + bordures (2) + gaps (gap-x-4 = 16)
    $minWidth = collect($affichees)->sum('min') + 50 + 16 * max(count($affichees) - 1, 0);

    $actionVisible = isset($affichees['action']);
@endphp



<div class="w-full">
    <div class="h-90 overflow-auto scrollbar-none xl:[scrollbar-width:thin]" style="--jobs-cols: {{ $gridCols }}; --jobs-min: {{ $minWidth }}px">
        <div class="xl:min-w-[var(--jobs-min)]">

            <!-- en-tête tableau (ordi) -->
            <div class="hidden xl:grid xl:[grid-template-columns:var(--jobs-cols)] xl:gap-x-4 sticky top-0 z-40 bg-[#FAF8FF] items-center whitespace-nowrap px-6 py-2.5 border-x border-x-transparent border-b border-b-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                @foreach ($affichees as $cle => $col)
                    <div @class([
                        'min-w-0',
                        'sticky z-30 bg-[#FAF8FF] pl-2 text-center shadow-[-6px_0_10px_-4px_rgba(0,0,0,0.04)]' => in_array($cle, ['statut', 'action'], true),
                        'right-20' => $cle === 'statut' && $actionVisible,
                        'right-0'  => ($cle === 'statut' && ! $actionVisible) || $cle === 'action',
                    ])>{{ $col['label'] }}</div>
                @endforeach
            </div>

            <div class="flex w-full flex-col gap-3 xl:pt-3">
                @foreach ($jobs as $job)
                    <x-cartes.job-carte :job="$job" :visible="$visible" />
                @endforeach
            </div>

        </div>
    </div>
</div>