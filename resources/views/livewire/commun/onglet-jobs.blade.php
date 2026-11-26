<section wire:key="section-jobs" 
x-data="{ modalFiltres: false, modalExport : false, modalAjout : false }" 
@fermer-filtres.window="modalFiltres = false" 
@fermer-export.window="modalExport = false" 
@fermer-ajout.window="modalAjout = false" 
@keydown.escape.window="modalFiltres = false, modalExport = false, modalAjout = false">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <button type="button" @click="modalFiltres = true" class="flex-1 m-2 md:flex-none inline-flex items-center justify-center space-x-1.5 px-3.5 py-2.5 md:py-2 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]">
            <x-icones.filtrer class=" h-4 w-4" />
            <span>Trier</span>
        </button>
        <div class="flex items-center gap-2.5 w-full md:w-auto">
            <button type="button" @click="modalExport = true" class="flex-1 md:flex-none inline-flex items-center justify-center space-x-1.5 px-3.5 py-2.5 md:py-2 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]">
                <x-icones.telecharger class="w-4 h-4" />
                <span>Exporter</span>
            </button>
            <button type="button" @click="modalAjout = true"  class="flex-1 md:flex-none inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 md:py-2 rounded-xl text-sm font-semibold text-white bg-[#10203E] hover:bg-[#004e60] shadow-sm transition-all active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E] focus-visible:ring-offset-2">
                <x-icones.ajouter class="w-4 h-4" />
                <span>Ajouter un job</span>
            </button>
        </div>
    </div>

    <div x-show="modalFiltres" x-cloak @click.self="modalFiltres = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 ">
        <x-filtre :items="$items" />
    </div>

    <div x-show="modalExport" x-cloak x-transition.opacity @click.self="modalExport = false" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">           
        <livewire:commun.export-modal :filtres="$filtres" :items="$items" :key="'export-' . md5(json_encode($filtres))" />
    </div>

    <div x-show="modalAjout" x-cloak @click.self="modalAjout = false" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
        <livewire:commun.job-modal :prerempli="$valeursDefaults" />
    </div>

    <livewire:commun.liste-jobs :filtres="$filtres" :key="'liste-jobs-' . md5(json_encode($filtres))" />
</section>