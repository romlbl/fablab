<div class="w-[80vw] max-w-[620px] sm:w-full filter drop-shadow-xl" data-purpose="login-modal">

    <div class="flex w-full" >
        <button type="button" id="members-tab" wire:click="changerOnglet('membres')" class="w-1/2 px-2 py-4 text-center font-display text-sm min-[380px]:text-base sm:text-lg leading-tight justify-center transition-colors relative rounded-tl-3xl rounded-tr-2xl touch-manipulation cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-cyan {{ $onglet === 'membres' ? 'font-bold bg-white text-brand-navy border-t border-l border-brand-border z-10' : 'font-medium bg-[#EBF0F6] text-slate-500 border-t border-r border-l border-brand-border/60 hover:bg-[#E2E8F0] hover:text-brand-navy z-0' }}">
            Membres
        </button>

        <button type="button" id="admins-tab" wire:click="changerOnglet('admins')" class="w-1/2 px-2 py-4 text-center font-display text-sm min-[380px]:text-base sm:text-lg leading-tight justify-center transition-colors relative rounded-tr-3xl rounded-tl-2xl touch-manipulation cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-cyan {{ $onglet === 'admins' ? 'font-bold bg-white text-brand-navy border-t border-r border-l border-brand-border z-10' : 'font-medium bg-[#EBF0F6] text-slate-500 border-t border-r border-l border-brand-border/60 hover:bg-[#E2E8F0] hover:text-brand-navy z-0' }}">
            Administrateurs
        </button>
    </div>

    <section class="bg-white rounded-b-3xl border-x border-b border-brand-border shadow-xl p-6 sm:p-8 md:p-12 relative z-10 -mt-px">

        {{-- panneau membre --}}
        <div id="members-panel" class="{{ $onglet === 'membres' ? '' : 'hidden' }}">
            <form wire:submit="connecterMembre" class="space-y-6 md:space-y-8" x-data="{ voir: false }">
                <div class="space-y-3">
                    <label class="block font-display text-base sm:text-lg font-semibold text-brand-navy" for="user-access-code">
                        Code de connexion
                    </label>
                    <div class="relative">
                        <input id="user-access-code" name="access_code" :type="voir ? 'text' : 'password'" wire:model="codeConnexion" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" required class="block w-full min-w-0 h-14 pl-4 sm:pl-5 pr-12 rounded-2xl bg-brand-input border border-brand-border text-brand-navy font-mono text-base sm:text-lg tracking-wider placeholder:text-slate-400 placeholder:font-sans focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-cyan focus:border-brand-cyan transition-all">
                        <button type="button" @click="voir = !voir" class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-xl text-[#64748B] hover:text-brand-navy focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan transition-colors cursor-pointer touch-manipulation">       
                            <span x-show="!voir" x-cloak>
                                <x-dynamic-component component="icones.oeil-ouvert" class="w-5 h-5" />
                            </span>
                            <span x-show="voir" x-cloak>
                                <x-dynamic-component component="icones.oeil-ferme" class="w-5 h-5" />
                            </span>
                        </button>
                    </div>
                    @error('codeConnexion')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 md:pt-4 flex items-center justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto justify-center font-display font-semibold text-base px-8 py-3 bg-brand-navy hover:bg-brand-navy-light text-white shadow-md hover:shadow-lg transition-all duration-200 transform active:scale-95 flex items-center gap-2 border border-brand-navy rounded-2xl touch-manipulation cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan focus-visible:ring-offset-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="connecterMembre">Connexion</span>
                        <span wire:loading wire:target="connecterMembre">Connexion...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- panneau admin --}}
        <div id="admins-panel" class="{{ $onglet === 'admins' ? '' : 'hidden' }}">
            <form wire:submit="connecterAdmin" class="space-y-5 md:space-y-6" x-data="{ voir: false }">
                <div class="space-y-3">
                    <label class="block font-display text-base sm:text-lg font-semibold text-brand-navy" for="admin-username-input">
                        Nom
                    </label>
                    <input id="admin-username-input" name="admin_username" type="text" wire:model="nomAdmin" autocomplete="off" required class="block w-full min-w-0 h-14 px-4 sm:px-5 rounded-2xl bg-brand-input border border-brand-border text-brand-navy text-base placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-cyan focus:border-brand-cyan transition-all" >
                    @error('nomAdmin')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="block font-display text-base sm:text-lg font-semibold text-brand-navy" for="admin-password-input">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <input id="admin-password-input" name="admin_password" :type="voir ? 'text' : 'password'" wire:model="motDePasseAdmin" required  class="block w-full min-w-0 h-14 pl-4 sm:pl-5 pr-12 rounded-2xl bg-brand-input border border-brand-border text-brand-navy text-base placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-cyan focus:border-brand-cyan transition-all">
                        <button type="button" @click="voir = !voir"  class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-xl text-[#64748B] hover:text-brand-navy focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan transition-colors cursor-pointer touch-manipulation">
                            <span x-show="!voir" x-cloak>
                                <x-dynamic-component component="icones.oeil-ouvert" class="w-5 h-5" />
                            </span>
                            <span x-show="voir" x-cloak>
                                <x-dynamic-component component="icones.oeil-ferme" class="w-5 h-5" />
                            </span>
                        </button>
                    </div>
                    @error('motDePasseAdmin')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 md:pt-4 flex items-center justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto justify-center font-display font-semibold text-base px-8 py-3 bg-brand-navy hover:bg-brand-navy-light text-white shadow-md hover:shadow-lg transition-all duration-200 transform active:scale-95 flex items-center gap-2 border border-brand-navy rounded-2xl touch-manipulation cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-cyan focus-visible:ring-offset-2 disabled:opacity-50" >
                        <span wire:loading.remove wire:target="connecterAdmin">Connexion</span>
                        <span wire:loading wire:target="connecterAdmin">Connexion...</span>
                    </button>
                </div>
            </form>
        </div>

    </section>
</div>