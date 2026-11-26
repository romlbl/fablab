<div class="my-auto w-full max-w-xl space-y-5 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xl shadow-slate-900/15 ring-1 ring-slate-900/5 sm:space-y-6 sm:p-8">
    <div class="flex items-start justify-between gap-3">
        <h2 class="text-xl font-bold tracking-tight text-slate-900">Exporter les jobs</h2>
        <button type="button" @click="$dispatch('fermer-export')" class="-mr-2 -mt-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 sm:m-0 sm:h-auto sm:w-auto sm:p-1.5">
            <span class="text-2xl font-light leading-none">×</span>
        </button>
    </div>

    <form wire:submit.prevent="exporter" class="space-y-5">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($items as $cle => $item)
                <x-champ-multi wire:key="export-{{ $cle }}" :modele="'export.' . $cle" :libelle="$item[0]" :choix="$item[1] ?? []" />
            @endforeach
        </div>

        <x-champ-periode prefixe="export" wire:key="export-periode" />

        <div class="space-y-2.5 pt-1">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Format du fichier</span>
            <div class="grid grid-cols-2 gap-3">
                @foreach (['xlsx' => 'Excel (.xlsx)', 'csv' => 'CSV (.csv)'] as $valeur => $libelle)
                    <label class="flex cursor-pointer items-center gap-2 rounded-2xl border border-slate-200 p-3 transition hover:border-slate-300 has-checked:border-[#10203E] has-checked:bg-[#10203E]/5">
                        <input type="radio" value="{{ $valeur }}" wire:model="export.format" class="text-[#10203E] focus:ring-[#10203E]">
                        <span class="text-xs font-semibold text-slate-800">{{ $libelle }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
            <button type="button" @click="$dispatch('fermer-export')" class="flex-1 rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 sm:flex-none sm:py-2.5">Annuler</button>
            <button type="submit" class="flex-1 rounded-xl bg-[#10203E] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#004e60] sm:flex-none sm:py-2.5">Exporter</button>
        </div>
    </form>
</div>