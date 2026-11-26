<main x-data="{ ongletActif: 'jobs' }" class="flex flex-col gap-6">

  <!-- info projets -->
    <section class="flex flex-col md:flex-row gap-5 md:gap-6 lg:gap-10 items-start overflow-hidden rounded-3xl bg-white border border-slate-200/80 shadow-card p-4 md:p-6">
        <img class="w-full md:w-2/5 lg:w-1/3 h-48 sm:h-56 md:h-64 flex-shrink-0 object-cover rounded-2xl shadow-sm" loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($projet->chemin_image)}}" alt="Image non disponible">
        <div class="min-w-0 flex flex-col gap-3 md:pr-4">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 break-words"> {{$projet->nom}} </h1>
            @if (!empty($projet->description))
            <p class="text-sm text-slate-500 leading-relaxed font-body">{{$projet->nom}}</p>
            @endif
        </div>
    </section>

    <!-- indicateurs -->
    <section>
    <div class="mb-5">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight" >Indicateurs</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">

        <x-stats.donut
            wire:key="donut-machines"
            titre="Temps machines"
            :total="$statsMachines['total']"
            :segments="$statsMachines['segments']"
        />

        <x-stats.donut
            wire:key="donut-conception"
            titre="Temps conception"
            :total="$statsConception['total']"
            :segments="$statsConception['segments']"
        />

        <x-stats.resume-consos
            wire:key="resume-consos"
            titre="Consommables utilisés"
            :data="$dataConsommables"
        />

    </div> 
    </section>

    <section>
        <div class="flex justify-around items-center border-b border-slate-100">
            <button id="tab-jobs" type="button" @click="ongletActif = 'jobs'" class="tab-button flex flex-col items-center gap-1 px-2 sm:px-4 pb-3 text-sm min-[380px]:text-base font-semibold rounded-t-lg touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]" :class="ongletActif === 'jobs' ? 'text-slate-900' : 'text-slate-500 hover:text-slate-900' hover:cursor-pointer">
                <span>Jobs</span>
                <span x-show="ongletActif === 'jobs'" x-cloak class="tab-indicator w-16 h-1 rounded-full bg-[#10203E]"></span>
            </button>
            <button id="tab-times" type="button" @click="ongletActif = 'times'" class="tab-button flex flex-col items-center gap-1 px-2 sm:px-4 pb-3 text-sm min-[380px]:text-base font-semibold rounded-t-lg touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]" :class="ongletActif === 'times' ? 'text-slate-900' : 'text-slate-500 hover:text-slate-900' hover:cursor-pointer">
                <span>Temps</span>
                <span x-show="ongletActif === 'times'" x-cloak class="tab-indicator w-16 h-1 rounded-full bg-[#10203E]"></span>
            </button>
            <button id="tab-consumables" type="button" @click="ongletActif = 'consumables'" class="tab-button flex flex-col items-center gap-1 px-2 sm:px-4 pb-3 text-sm min-[380px]:text-base font-semibold rounded-t-lg touch-manipulation focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#10203E]" :class="ongletActif === 'consumables' ? 'text-slate-900' : 'text-slate-500 hover:text-slate-900' hover:cursor-pointer">
                <span>Consommables</span>
                <span x-show="ongletActif === 'consumables'" x-cloak class="tab-indicator w-16 h-1 rounded-full bg-[#10203E]"></span>
            </button>
        </div>
    </section>

    <section class="h-100 items-start p-3">
        <div id="tab-content-jobs" x-show="ongletActif === 'jobs'" x-cloak class="tab-content" >
            <livewire:commun.onglet-jobs :projet="$projet" />
        </div>
        <div id="tab-content-times" x-show="ongletActif === 'times'" x-cloak class="tab-content">
            <livewire:commun.onglet-temps :projet="$projet" />
        </div>
        <div id="tab-content-consumables" x-show="ongletActif === 'consumables'" x-cloak class="tab-content">
            <livewire:commun.onglet-consos :projet="$projet" />
        </div>
    </section>

</main>
