<header class="lg:hidden sticky top-0 z-20 bg-white border-b border-slate-200 px-4 h-16 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <button type="button" @click="menuOuvert = true" class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none" >
            <x-icones.menu class="w-6 h-6" />
        </button>

        <span class="font-display font-bold text-lg text-slate-900 tracking-tight">
            {{ $titre ?? '' }}
        </span> 
    </div>

</header>