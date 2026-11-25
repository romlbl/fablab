@props([
    'titre' => 'Répartition',
    'total' => 0,
    'segments' => [],
    'selecteurs' => [],
    'modele' => null,
])

@php
    $somme = collect($segments)->sum('value');
    $rayon = 48;
    $circonference = 2 * pi() * $rayon;
    $decalageCumule = 0;
@endphp

<article class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-card flex flex-col justify-between hover:shadow-card-hover transition-all duration-300">
    <div>
        <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-bold text-slate-900">{{ $titre }}</h3>
        </div>
        <div class="relative flex items-center justify-center my-4">
            <svg class="w-44 h-44 max-w-full flex-shrink-0 transform -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="{{ $rayon }}" fill="none" stroke="#F1F5F9" stroke-width="12" />
                @if ($somme > 0)
                    @foreach ($segments as $segment)
                        @continue(($segment['value'] ?? 0) <= 0)
                        @php
                            $part = ($segment['value'] ?? 0) / $somme;
                            $longueur = $part * $circonference;
                            $dashOffset = -$decalageCumule;
                            $decalageCumule += $longueur;
                        @endphp
                        <circle class="donut-segment" cx="60"  cy="60" r="{{ $rayon }}" fill="none" stroke="{{ $segment['color'] }}" stroke-width="12" stroke-dasharray="{{ $longueur }} {{ $circonference }}" stroke-dashoffset="{{ $dashOffset }}"/>
                    @endforeach
                @endif
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                <span class="text-xl text-slate-900">{{ $total ? sprintf('%02dh %02dm', intdiv($total, 60), $total % 60) : '00h 00m' }}</span>
            </div>
        </div>
        <ul class="h-30 overflow-y-auto space-y-2 pr-1 text-sm flex-1">
            @foreach ($segments as $segment)
                @continue(($segment['value'] ?? 0) <= 0)
                <li class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-2 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $segment['color'] }}"></span>
                        {{ $segment['label'] ?? '' }}
                    </span>
                    <span class="text-slate-900 whitespace-nowrap">{{ ($segment['value'] ?? 0) ? sprintf('%02dh %02dm', intdiv($segment['value'], 60), $segment['value'] % 60) : '00h 00m' }} ( {{ $somme > 0 ? round((($segment['value'] ?? 0) / $somme) * 100) : 0 }}% )</span>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-100">
        @if (!empty($selecteurs))
            <select
                @if ($modele)
                    wire:model.live="{{ $modele }}"
                @endif
                class="w-full text-base sm:text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl py-2 px-3 text-slate-700 hover:bg-slate-100/80 focus:border-[#10203E] focus:ring-1 focus:ring-[#10203E] transition-colors cursor-pointer">
                @foreach ($selecteurs as $cle => $libelle)
                    <option value="{{ $cle }}">{{ $libelle }}</option>
                @endforeach
            </select>
        @endif
    </div>
</article>
