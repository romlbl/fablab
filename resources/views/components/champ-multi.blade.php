@props(['modele', 'libelle', 'choix' => []])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-1.5 w-full']) }}>
    <span class="text-xs font-semibold text-slate-600">{{ $libelle }}</span>
    <div class="relative w-full"
        x-data="{
            ouvert: false,
            recherche: '',
            choix: @js($choix),
            selectionnes: $wire.entangle('{{ $modele }}').live,
            get ids() {
                return Array.isArray(this.selectionnes) ? this.selectionnes.map(String) : [];
            },
            get resultats() {
                const q = this.recherche.trim().toLowerCase();
                return Object.entries(this.choix).filter(([id, nom]) => {
                    return !this.ids.includes(String(id)) && String(nom).toLowerCase().includes(q);
                });
            },
            ajouter(id) {
                this.selectionnes = [...this.ids, String(id)];
                this.recherche = '';
                this.ouvert = false;
                this.$refs.champ.focus();
            },
            retirer(id) {
                this.selectionnes = this.ids.filter(elt => elt !== String(id));
            }
        }"
        @click.outside="ouvert = false">

        <div @click="$refs.champ.focus()" class="flex min-h-[40px] cursor-text flex-col items-stretch gap-1 rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 transition focus-within:border-[#10203E]/40 focus-within:bg-white focus-within:ring-4 focus-within:ring-[#10203E]/10 hover:border-slate-300">
            <div class="flex items-center gap-2">
                <x-icones.rechercher class="h-4 w-4 shrink-0 text-slate-400" />
                <input x-ref="champ" type="text" x-model="recherche" @focus="ouvert = true" @click.stop="ouvert = true" @keydown.escape="ouvert = false" @keydown.backspace="if (!recherche && ids.length) retirer(ids[ids.length - 1])" placeholder="{{ $libelle }}" autocomplete="off" class="min-w-0 w-full border-0 bg-transparent p-0 text-sm leading-5 font-medium text-slate-700 outline-none focus:ring-0 placeholder:font-normal placeholder:text-slate-400">
            </div>

            <template x-if="ids.length">
                <div class="flex max-h-[68px] w-full flex-wrap content-start items-start gap-1.5 overflow-y-auto border-t border-slate-200/70 pt-2">
                    <template x-for="id in ids" :key="id">
                        <span class="inline-flex max-w-full items-center gap-1 rounded-full bg-[#10203E]/10 py-0.5 pl-2.5 pr-1 text-[11px] font-semibold leading-4 text-[#10203E] ring-1 ring-inset ring-[#10203E]/10">
                            <span x-text="choix[id] ?? id" class="min-w-0 break-words"></span>
                            <button type="button" @click.stop="retirer(id)" class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full leading-none text-[#10203E]/60 transition hover:bg-red-100 hover:text-red-600">×</button>
                        </span>
                    </template>
                </div>
            </template>
        </div>

        <ul x-show="ouvert && resultats.length > 0" x-cloak class="absolute left-0 right-0 top-full z-50 mt-1.5 max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-white p-1 shadow-xl shadow-slate-900/10 ring-1 ring-slate-900/5">
            <template x-for="[id, nom] in resultats" :key="id">
                <li>
                    <button type="button" @click="ajouter(id)" x-text="nom" class="block w-full rounded-lg px-3 py-1.5 text-left text-xs font-medium text-slate-700 transition hover:bg-[#10203E]/5 hover:text-[#10203E] focus:bg-[#10203E]/5 focus:outline-none"></button>
                </li>
            </template>
        </ul>
    </div>
</div>