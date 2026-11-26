<div>
<section class="w-full flex items-center justify-between p-2 ">
    <div class="w-xl flex items-center bg-surface-container-lowest rounded-full shadow-card border border-slate-200/80 px-4 py-3 sm:py-2.5">
        <x-icones.rechercher class="w-5 h-5 text-on-surface-variant material-symbols-outlined text-outline text-[20px] pointer-events-none mr-3" />
        <input wire:model.live.debounce.300ms="search" class="w-full min-w-0 text-body-md font-body-md text-on-surface placeholder:text-outline-variant bg-transparent border-0 focus:ring-0 focus:outline-none p-0" placeholder="Rechercher un projet..." type="text">
    </div>

</section>

<section class="w-full h-130 overflow-y-auto flex flex-col md:flex-row items-center md:items-start md:flex-wrap gap-6 p-2">
    @foreach ($infosMachines as $machine)
        <div class="flex w-full max-w-[280px] flex-col items-center gap-3 px-6 py-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow group relative">
            <div class="w-full">
                <img class="block w-full aspect-[2/3] object-cover rounded-2xl shadow-sm" loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($machine['chemin_image'])}}" alt="Image non disponible">
            </div>
            <div class="w-full text-center">
                <span class="text-base text-slate-800"> {{$machine['nom']}} </span>
            </div>
            <div class="w-full text-center">
                <span class="text-base text-slate-800">{{ sprintf('%dh%02dm', intdiv((int) $machine['temps_total'], 60), (int) $machine['temps_total'] % 60) }}</span>
            </div>
            <div class="w-full text-center">
                <span class="text-xs text-slate-400">{{$machine['categorie']}}</span>
            </div>
        </div>


    @endforeach
</section>
 
</div>