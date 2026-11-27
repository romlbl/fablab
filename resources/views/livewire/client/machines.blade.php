<main class="flex flex-col gap-6">

    <section class="w-full flex flex-col md:flex-row gap-4 items-center justify-between p-2 ">
        <div class="w-full md:w-xl flex items-center bg-surface-container-lowest rounded-full shadow-card border border-slate-200/80 px-4 py-3 sm:py-2.5">
            <x-icones.rechercher class="w-5 h-5 text-on-surface-variant material-symbols-outlined text-outline text-[20px] pointer-events-none mr-3" />
            <input wire:model.live.debounce.300ms="search" class="w-full min-w-0 text-body-md font-body-md text-on-surface placeholder:text-outline-variant bg-transparent border-0 focus:ring-0 focus:outline-none p-0" placeholder="Rechercher une machine.." type="text">
        </div>

        <div class="w-full flex gap-2 items-center justify-start md:max-w-70">
            <span class="whitespace-nowrap">Trier : </span>
            <x-champ-multi modele="filtreCategories" placeholder="Toutes les catégories" :choix="$choixCate" />
        </div>

    </section>

    <section class="w-full flex flex-col items-center md:items-start md:flex-row md:flex-wrap gap-6 p-2">
        @foreach ($this->resultats as $machine)
            <x-cartes.machine-carte :machine="$machine" :key="'results-' . md5($search)" />
        @endforeach
    </section>

</main>
