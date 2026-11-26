<?php

namespace App\Livewire\Commun;

use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Machines;
use App\Models\Projets;
use App\Models\Clients;
use App\Models\Collaborateurs;
use App\Models\Categories;
use App\Models\Consommables;
use App\Models\Type_travail;
use App\Models\Jobs;
use App\Models\Jobs_consommables;
use Illuminate\Support\Carbon;


class JobModal extends Component 
{

    #[Locked]
    public string $role = '';

    #[Locked]
    public ?int $idUser = null;

    public array $suggestions = [];
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

    public function mount() {   
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
        $this->job['date_debut'] = now()->format('Y-m-d\TH:i');
    }

    public function machineChoisie(int $id){
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

    public function clientChoisi(int $id){
        abort_unless($this->role === 'admin', 403);
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

    public function projetChoisi(int $id){
        $projet = Projets::with('client:id,nom')->findOrFail($id);
        if ($this->role === 'client' && $projet->id_client !== $this->idUser) {
            abort(403);
        }
        $client = $projet->client;

        $this->job['projet_id'] = $projet->id;
        $this->job['client_id'] = $client?->id;
        $this->chargerSuggestionsProjets($client?->id);

        return $client
            ? ['id' => $client->id, 'nom' => $client->nom]
            : null;
    }

    private function chargerSuggestionsProjets(?int $idClient = null) {
        $this->suggestions['projets'] = Projets::query()
            ->when($idClient !== null, fn ($query) => $query->where('id_client', $idClient))
            ->pluck('nom', 'id')
            ->toArray();
    }

    private function chargerSuggestionsConsommables(?int $idCategorie = null) {
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

    public function creer(){
        $this->validate([
            'job.description'             => ['nullable', 'string', 'max:500'],
            'job.machine_id'              => ['nullable', 'exists:machines,id'],
            'job.conception_id'           => ['nullable', 'exists:type_travails,id'],
            'job.projet_id'               => ['nullable', 'exists:projets,id'],
            'job.client_id'               => ['nullable', 'exists:clients,id'],
            'job.operateur_id'            => ['nullable', 'exists:collaborateurs,id'],
            'job.date_debut'              => ['required', 'date'],
            'job.duree_heures'            => ['nullable', 'integer', 'min:0'],
            'job.duree_minutes'           => ['nullable', 'integer', 'min:0', 'max:59'],
            'job.date_fin'                => ['nullable', 'date'],
            'job.consommables.*.id'       => ['required', 'exists:consommables,id'],
            'job.consommables.*.quantite' => ['required', 'numeric', 'gt:0'],
        ]);

        $estAdmin = $this->role === 'admin';

        if (! $this->job['machine_id'] && ! $this->job['conception_id']) {
            $this->addError('job.machine_id', 'Choisis une machine ou une conception.');
            return false;
        }

        // un client ne peut agir que sur lui-même
        $clientId = $estAdmin ? $this->job['client_id'] : $this->idUser;
        $operateurId = $estAdmin ? $this->job['operateur_id'] : null;
        $debut = $estAdmin ? Carbon::parse($this->job['date_debut']) : now();

        $projetId = $this->job['projet_id'];
        if ($projetId && ! $estAdmin) {
            abort_unless(Projets::whereKey($projetId)->where('id_client', $this->idUser)->exists(), 403);
        }

        $minutes = ((int) $this->job['duree_heures'] * 60) + (int) $this->job['duree_minutes'];
        if ($minutes > 0) {
            $fin = $debut->copy()->addMinutes($minutes);
        } elseif ($estAdmin && $this->job['date_fin']) {
            $fin = Carbon::parse($this->job['date_fin']);
        } else {
            $this->addError('job.duree_heures', 'La durée est obligatoire.');
            return false;
        }

        if ($fin->lte($debut)) {
            $this->addError('job.date_fin', 'La fin doit être après le début.');
            return false;
        }

        $statut = match (true) {
            ! $estAdmin => 'en attente',
            $fin->isPast() => 'terminé',
            default => 'en cours',
        };

        $jobCree = DB::transaction(function () use ($clientId, $operateurId, $projetId, $debut, $fin, $statut) {
            $nouveau = Jobs::create([
                'id_type'         => $this->job['machine_id'] ? 1 : 2,
                'status'          => $statut,
                'id_operateur'    => $operateurId,
                'id_client'       => $clientId,
                'id_projet'       => $projetId,
                'description'     => $this->job['description'],
                'id_machine'      => $this->job['machine_id'],
                'id_type_travail' => $this->job['machine_id'] ? null : $this->job['conception_id'],
                'debut'           => $estAdmin ? $debut : null,
                'fin'             => $estAdmin ? $fin : null,
            ]);

            foreach ($this->job['consommables'] as $ligne) {
                $prix = (float) Consommables::find($ligne['id'])->prix_unite;
                Jobs_consommables::create([
                    'id_job'         => $nouveau->id,
                    'id_consommable' => $ligne['id'],
                    'quantite'       => $ligne['quantite'],
                    'cout'           => $prix,
                    'cout_tot'       => $ligne['quantite'] * $prix,
                ]);
            }

            return $nouveau;
        });

        $this->reset('job');
        $this->job['date_debut'] = now()->format('Y-m-d\TH:i');
        $this->dispatch('job-ajoute', jobId: $jobCree->id);
        $this->dispatch('fermer-ajout');

        return true;
    }

    public function render()
    {
        return view('livewire.commun.job-modal');
    }
}
