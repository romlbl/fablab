<form method="POST" action="{{ route('deconnexion') }}">
    @csrf
    <button type="submit" class="flex w-full cursor-pointer items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-600 transition-colors hover:bg-rose-50 hover:text-rose-600">
        <x-icones.logout class="w-6 h-6" />
        <span>Déconnexion</span>
    </button>
</form>