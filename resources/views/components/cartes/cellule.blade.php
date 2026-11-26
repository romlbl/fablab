@props(['label', 'valeur' => null, 'taille' => 'text-xs'])

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <span class="block xl:hidden mb-0.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</span>
    <span class="{{ $taille }} break-normal {{ $valeur ? 'text-slate-800' : 'text-slate-400' }}">{{ $valeur ?: 'Aucun' }}</span>
</div>