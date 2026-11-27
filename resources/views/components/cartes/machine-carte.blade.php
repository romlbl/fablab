@props(['machine'])

@php($aImage = filled($machine->chemin_image))

<article @class(['group relative flex h-[520px] w-full max-w-[280px] flex-col items-center gap-2.5 overflow-hidden rounded-2xl border border-slate-200/80 bg-white px-6 py-4 shadow-sm transition-shadow hover:shadow-md', 'justify-between' => ! $aImage,])>
    @if ($aImage)
        <img class="aspect-[2/3] h-auto w-full shrink-0 rounded-2xl object-cover object-center shadow-sm" loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($machine->chemin_image) }}" alt="{{ $machine->nom ?? 'Image non disponible' }}">
    @endif

    <div class="w-full shrink-0 text-center">
        <h3 class="line-clamp-1 text-base font-medium text-slate-800">
            {{ $machine->nom }}
        </h3>
    </div>

    <div
        @class([ 'min-h-0 w-full overflow-y-auto pr-1 text-center', 'flex-1' => $aImage,])>
        <p class="text-sm text-slate-500">
            {{ $machine->description }}
        </p>
    </div>

    <div @class(['w-full shrink-0 pt-1 text-center', 'mt-auto' => $aImage])>
        <span class="line-clamp-1 text-xs text-slate-400">
            {{ $machine->categorie->nom }}
        </span>
    </div>
</article>