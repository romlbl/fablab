@props([
    'titre' => 'Répartition',
    'data' => []
])

<article class="sm:col-span-2 xl:col-span-1 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-card flex flex-col justify-start hover:shadow-card-hover transition-all duration-300">
<div class="flex items-center justify-between mb-4">
    <h3 class="text-base font-bold text-slate-900">{{$titre}}</h3>
</div>

<div class="mt-2 space-y-3">
    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 pb-1.5 border-b border-slate-100">
        <span>Intitulé</span>
        <span class="text-right">Quantité</span>
    </div>
    <div class="flex flex-col space-y-1.5 max-h-60 overflow-y-auto">
        @foreach ( $data as $consommable )
            <div class="grid grid-cols-[minmax(0,1fr)_auto] items-start gap-x-3 gap-y-1.5 py-2.5 border-b border-slate-100/70 font-body text-xs hover:bg-slate-50/80 rounded-lg px-1 transition-colors">
                <div class="min-w-0">
                    <span class="block text-slate-900 font-semibold break-words">{{ $consommable['nom'] }}</span>
                    <span class="mt-1 block text-slate-500">
                        {{ $consommable['catégorie'] }}
                    </span>
                </div>
                <span class="whitespace-nowrap text-right text-slate-900 text-sm font-sans">{{$consommable['quantité']}} {{$consommable['unité']}}</span>
            </div>
        @endforeach
    </div>
</div>
</article>