<?php

namespace App\Livewire\Commun;

use Livewire\Component;
use App\Models\Machines;
use App\Models\Projets;
use App\Models\Clients;
use App\Models\Collaborateurs;
use App\Models\Categories;
use App\Models\Consommables;
use App\Models\Type_travail;
use App\Models\Jobs;
use Illuminate\Support\Carbon;


class JobModal extends Component 
{

    public array $suggestions = [];
    public String $role = '';
    public ?int $idUser = null;
    public array $job = [
        'description'    => '',
        'machine_id'     => null,
        'projet_id'      => null,
        'client_id'      => null,
        'operateur_id'   => null,
        'categorie_id'   => null,
        'conception_id'  => null,
        'date_debut'     => '',
        'duree_heures'   => '',
        'duree_minutes'  => '',
        'date_fin'       => '',
        'consommables'   => [],  
    ];

    public function mount(): void
    {   
        $this->role = session('auth_role');
        $this->idUser = session('auth_user_id');
        $this->suggestions = [
            'machines'   => Machines::all()->pluck('nom', 'id')->toArray(),
            'categories' => Categories::all()->pluck('nom', 'id')->toArray(),
            'conceptions' => Type_travail::all()->pluck('nom', 'id')->toArray(),
        ];

        if ($this->role === 'admin') {
            $this->suggestions['operateurs'] = Collaborateurs::all()->pluck('nom', 'id')->toArray();
            $this->suggestions['clients'] = Clients::all()->pluck('nom', 'id')->toArray();
        }

        $this->chargerSuggestionsProjets($this->role === 'client' ? $this->idUser : null);
        $this->chargerSuggestionsConsommables();
    }

    public function machineChoisie(int $id): ?array
    {
        $machine = Machines::with('categorie:id,nom')->findOrFail($id);
        $this->job['machine_id'] = $machine->id;
        $categorie = $machine->categorie;
        $this->job['categorie_id'] = $categorie?->id;
        $this->job['conception_id'] = null;

        $this->chargerSuggestionsConsommables($machine->id_categorie);

        return $categorie
            ? ['id' => $categorie->id, 'nom' => $categorie->nom]
            : null;
    }

    public function clientChoisi(int $id): bool
    {
        $client = Clients::findOrFail($id);
        $this->job['client_id'] = $client->id;

        $this->chargerSuggestionsProjets($client->id);

        $projetId = $this->job['projet_id'];
        if ($projetId !== null && ! Projets::whereKey($projetId)
            ->where('id_client', $client->id)
            ->exists()) {
            $this->job['projet_id'] = null;
        }

        return $this->job['projet_id'] !== null;
    }

    public function projetChoisi(int $id): ?array
    {
        $projet = Projets::with('client:id,nom')->findOrFail($id);
        $client = $projet->client;

        $this->job['projet_id'] = $projet->id;
        $this->job['client_id'] = $client?->id;
        $this->chargerSuggestionsProjets($client?->id);

        return $client
            ? ['id' => $client->id, 'nom' => $client->nom]
            : null;
    }

    private function chargerSuggestionsProjets(?int $idClient = null): void
    {
        $this->suggestions['projets'] = Projets::query()
            ->when($idClient !== null, fn ($query) => $query->where('id_client', $idClient))
            ->pluck('nom', 'id')
            ->toArray();
    }

    private function chargerSuggestionsConsommables(?int $idCategorie = null): void
    {
        $this->suggestions['consommables'] = Consommables::query()
            ->with('uniteObjet')
            ->when($idCategorie !== null, fn ($query) => $query->where('id_categorie', $idCategorie))
            ->get()
            ->map(fn (Consommables $consommable) => [
                'id' => $consommable->id,
                'nom' => $consommable->nom,
                'unite' => $consommable->uniteObjet?->unite,
            ])
            ->toArray();
    }

    public function creer(): bool
    {
        dump($this->job);

        return true;
    }

    public function render()
    {
        return view('livewire.commun.job-modal');
    }
}
