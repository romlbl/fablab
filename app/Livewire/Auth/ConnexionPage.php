<?php

namespace App\Livewire\Auth;

use App\Models\Clients;
use App\Models\Collaborateurs;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;

class ConnexionPage extends Component
{
    public string $onglet = 'membres'; 

    // form membre
    public string $codeConnexion = '';

    // form admin
    public string $nomAdmin = '';
    public string $motDePasseAdmin = '';

    public function changerOnglet(string $onglet): void
    {
        if (! in_array($onglet, ['membres', 'admins'], true)) {
            return;
        }

        $this->onglet = $onglet;
        $this->resetErrorBag();
    }

// 5 essais par minute et par ip
    private function cleLimite(): string
    {
        return 'connexion|' . request()->ip();
    }

    private function bloque(string $champ): bool
    {
        if (RateLimiter::tooManyAttempts($this->cleLimite(), 5)) {
            $this->addError($champ, 'Trop de tentatives, réessayer dans ' . RateLimiter::availableIn($this->cleLimite()) . ' s.');
            return true;
        }

        RateLimiter::hit($this->cleLimite(), 60);

        return false;
    }

    public function connecterMembre()
    {
        //vérrifiaction des règles sinon message d'erreur
        $this->validate([
            'codeConnexion' => ['required', 'string', 'max:255'],
        ], [
            'codeConnexion.required' => 'Le code de connexion est obligatoire.',
        ]);

        if ($this->bloque('codeConnexion')) {
            return;
        }

        //récupère client actifs
        $clientsActifs = Clients::query()->whereNull('date_archive')->get();

        //trouve le client correspondant au code de connexion
        $client = $clientsActifs->first(function (Clients $item) {
            return Hash::check($this->codeConnexion, $item->code_acces_hash);
        });

        if (! $client) {
            $this->addError('codeConnexion', 'Code de connexion invalide ou compte archivé.');
            return;
        }

        //génère un session et enregistre les informations utiles
        RateLimiter::clear($this->cleLimite());
        session()->regenerate();
        session()->put([
            'auth_role'     => 'client',
            'auth_user_id'  => $client->id,
            'auth_user_nom' => $client->nom,
        ]);

        return redirect()->route('client.accueil');
    }

    public function connecterAdmin()
    {
        $this->validate([
            'nomAdmin'        => ['required', 'string', 'max:255'],
            'motDePasseAdmin' => ['required', 'string', 'max:255'],
        ], [
            'nomAdmin.required'        => 'Le nom est obligatoire.',
            'motDePasseAdmin.required' => 'Le mot de passe est obligatoire.',
        ]);

        if ($this->bloque('motDePasseAdmin')) {
            return;
        }

        $collab = Collaborateurs::query()->where('nom', $this->nomAdmin)->whereNull('date_archive')->first();

        //si pas trouvé ou mauvais mot de passe alors message d'erreur
        if (! $collab || ! Hash::check($this->motDePasseAdmin, $collab->mdp_hash)) {
            $this->addError('motDePasseAdmin', 'Identifiants administrateur invalides ou compte archivé.');
            return;
        }

        RateLimiter::clear($this->cleLimite());
        session()->regenerate();
        session()->put([
            'auth_role'     => 'admin',
            'auth_user_id'  => $collab->id,
            'auth_user_nom' => $collab->nom,
        ]);

        return redirect()->route('admin.accueil');
    }

    public function render()
    {
        return view('livewire.auth.connexion-page');
    }
}