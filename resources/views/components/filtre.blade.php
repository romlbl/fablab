@props(['items' => []])

<div {{ $attributes->merge(['class' => 'relative w-full max-w-sm flex flex-col gap-4 p-4 rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/15 ring-1 ring-slate-900/5']) }}>

    <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100">
        <h3 class="text-sm font-semibold tracking-tight text-slate-800">Filtres</h3>
        <button type="button" @click="$dispatch('fermer-filtres')" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">×</button>
    </div>

    @foreach ($items as $cle => $item)
        <x-champ-multi wire:key="filtre-{{ $cle }}" :modele="'filtres.' . $cle" :libelle="$item[0]" :choix="$item[1] ?? []" />
    @endforeach

    <x-champ-periode prefixe="filtres" wire:key="filtre-periode" />
</div>