<div>
<section class="w-full flex flex-col md:flex-row gap-4 items-center justify-between p-2 ">
    <div class="w-full md:w-xl flex items-center bg-surface-container-lowest rounded-full shadow-card border border-slate-200/80 px-4 py-3 sm:py-2.5">
        <x-icones.rechercher class="w-5 h-5 text-on-surface-variant material-symbols-outlined text-outline text-[20px] pointer-events-none mr-3" />
        <input wire:model.live.debounce.300ms="search" class="w-full min-w-0 text-body-md font-body-md text-on-surface placeholder:text-outline-variant bg-transparent border-0 focus:ring-0 focus:outline-none p-0" placeholder="Rechercher un consommable..." type="text">
    </div>

    <div class="w-full flex gap-2 items-center justify-start md:max-w-70">
        <span class="whitespace-nowrap">Trier : </span>
        <x-champ-multi modele="triCategories" placeholder="Toutes les catégories" :choix="$choixCate" />
    </div>

</section>

<section class="w-full h-130 overflow-y-auto flex flex-col md:flex-row items-center md:items-start md:flex-wrap gap-6 p-2">
    @foreach ($this->resultats as $element)
        <div class="flex w-full h-full max-w-[280px]  flex-col items-center justify-between gap-3 px-6 py-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow group relative">
            @if ($element['chemin_image'])
                <div class="w-full">
                    <img class="block w-full aspect-[2/3] object-cover rounded-2xl shadow-sm" loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($element['chemin_image'])}}" alt="Image non disponible">
                </div>
            @endif
            <div class="w-full text-center">
                <span class="text-base text-slate-800"> {{$element['nom']}} </span>
            </div>
            <div class="w-full text-center">
                <span class="text-base text-slate-800"> {{$element['quantite']}} {{$element['unite']}}</span>
            </div>
            <div class="w-full text-center">
                <span class="text-xs text-slate-400">{{$element['categorie']}}</span>
            </div>
        </div>
    @endforeach
</section>
 
</div>