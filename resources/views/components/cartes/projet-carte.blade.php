@props(['projet'])

<article class="self-stretch sm:self-start flex-shrink-0 snap-start w-[78%] sm:w-full max-w-xs aspect-auto sm:aspect-[2/3] min-h-[26rem] sm:min-h-0 bg-white rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover hover:scale-105 hover:cursor-pointer transition-all duration-500 overflow-hidden flex flex-col group">
    @if (!empty($projet->chemin_image))
        <div class="relative h-44 bg-slate-100 overflow-hidden">
            <img class="w-full h-full object-cover" loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($projet->chemin_image)}}" alt="Image non disponible">
        </div>    
    @endif
    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between">
    <div>
        <h3 class="text-base font-bold text-slate-900"> {{$projet->nom}} </h3>
        @if (!empty($projet->description))
            <p class="text-xs text-slate-500 mt-2 leading-relaxed font-body"> {{$projet->description}} </p>
        @endif
    </div>
    </div>
</article>