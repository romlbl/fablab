<main class="flex flex-col gap-6">

    <section class="mb-6 sm:mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="relative flex items-center bg-surface-container-lowest rounded-full shadow-card border border-slate-200/80 px-4 py-3 sm:py-2.5">
                <x-icones.rechercher class="w-5 h-5 text-on-surface-variant material-symbols-outlined text-outline text-[20px] pointer-events-none mr-3" />
                <input wire:model.live.debounce.300ms="search" class="w-full min-w-0 text-body-md font-body-md text-on-surface placeholder:text-outline-variant bg-transparent border-0 focus:ring-0 focus:outline-none p-0" placeholder="Rechercher un projet..." type="text">
            </div>
        </div>
    </section>

    <section class="w-full flex flex-col items-center md:items-start md:flex-row md:flex-wrap gap-6 p-2">
        @foreach ($this->resultats as $projet)
            <x-cartes.projet-carte :projet="$projet" :key="'results-' . md5($search)" />
        @endforeach
    </section>

</main>
