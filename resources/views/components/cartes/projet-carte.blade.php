@props(['projet'])

<a href="{{ route('client.projets.show', $projet) }}" class=" w-[78%] sm:w-full max-w-xs aspect-auto sm:aspect-[2/3] min-h-[26rem] sm:min-h-0 bg-white rounded-3xl border border-slate-200/80 shadow-card hover:shadow-card-hover hover:scale-105 hover:cursor-pointer transition-all duration-500 overflow-hidden flex flex-col group">
    @if (session('auth_role') === 'admin' )
    <div class="absolute top-3 right-3 z-10 flex gap-2">
        <button type="button" class="inline-flex h-10 w-10 sm:h-9 sm:w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-[#E2912C] hover:text-[#ffbe00] shadow-sm transition-colors hover:bg-slate-50 ">
            <x-icones.crayon class="w-6 h-6 hover:cursor-pointer "  />
        </button>
        <button type="button" class="inline-flex h-10 w-10 sm:h-9 sm:w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-red-500  hover:text-red-400  shadow-sm transition-colors ">
            <x-icones.poubelle  class="w-6 h-6 hover:cursor-pointer "  />
        </button>
    </div>       
    @endif
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
    @if (session('auth_role') === 'admin' )
    <div class="flex justify-between items-center gap-2">
        <span>Valorisation : {{ $projet->valorisation() }}€</span>
        <span class="text-xs text-slate-500"> {{$projet->date_creation}} </span>
    </div>
    @endif
    </div>
</a>