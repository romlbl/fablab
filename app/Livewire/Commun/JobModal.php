<?php

namespace App\Livewire\Commun;

use App\Enums\TypeCategorie;
use App\Models\Categories;
use App\Models\Clients;
use App\Models\Collaborateurs;
use App\Models\Consommables;
use App\Models\Jobs;
use App\Models\Jobs_consommables;
use App\Models\Machines;
use App\Models\Projets;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class JobModal extends Component
{
    public array $suggestions = [];

    #[Locked]
    public string $role = '';

    #[Locked]
    public ?int $idUser = null;

    public array $job = [
        'description'   => '',
        'machine_id'    => null,
        'projet_id'     => null,
        'client_id'     => null,
        'operateur_id'  => null,
        'categorie_id'  => null,
        'date_debut'    => '',
        'duree_heures'  => '',
        'duree_minutes' => '',
        'date_fin'      => '',
        'consommables'  => [],
    ];

    public function mount(array $prerempli = []){
        $this->role = session('auth_role');
        $this->idUser = session('auth_user_id');
        $this->job['date_debut'] = now()->format('Y-m-d\TH:i');
        $this->valeursDefaut = 
        $this->suggestions = [
            'machines'   => Machines::pluck('nom', 'id')->toArray(),
            // catégories humaines seulement : celle d'une machine vient de la machine
            'categories' => Categories::deType(TypeCategorie::Humain)->pluck('nom', 'id')->toArray(),
        ];

        if ($this->role === 'admin') {
            $this->suggestions['operateurs'] = Collaborateurs::pluck('nom', 'id')->toArray();
            $this->suggestions['clients'] = Clients::pluck('nom', 'id')->toArray();
        }

        $clesAutorisees = array_intersect_key($prerempli, $this->job);
        foreach ($clesAutorisees as $cle => $valeur) {
            if ($valeur !== null) {
                $this->job[$cle] = $valeur;
            }
        }

        $idClientFiltre = $this->role === 'client' ? $this->idUser : null;

        if (! empty($this->job['projet_id'])) {
            $projet = Projets::with('client:id,nom')->findOrFail((int) $this->job['projet_id']);

            if ($this->role === 'client' && $projet->id_client !== $this->idUser) {
                abort(403);
            }

            $this->job['projet_id'] = $projet->id;
            $this->job['client_id'] = $this->job['client_id'] ?? $projet->id_client;
            $idClientFiltre = $this->job['client_id'];
        } elseif ($this->role === 'admin' && ! empty($this->job['client_id'])) {
            $idClientFiltre = (int) $this->job['client_id'];
        }

        $this->chargerSuggestionsProjets($idClientFiltre);

        $idCategorieConsommables = null;
        if (! empty($this->job['machine_id'])) {
            $machine = Machines::findOrFail((int) $this->job['machine_id']);
            $this->job['machine_id'] = $machine->id;
            $this->job['categorie_id'] = null;
            $idCategorieConsommables = $machine->id_categorie;
        }

        $this->chargerSuggestionsConsommables($idCategorieConsommables);

        $this->calculerDateFinSiDureePresente();
    }

    private function calculerDateFinSiDureePresente(){
        $minutes = ((int) ($this->job['duree_heures'] ?: 0) * 60) + (int) ($this->job['duree_minutes'] ?: 0);
        if ($minutes > 0 && ! empty($this->job['date_debut'])) {
            $this->job['date_fin'] = Carbon::parse($this->job['date_debut'])
                ->addMinutes($minutes)
                ->format('Y-m-d\TH:i');
        }
    }

    public function machineChoisie(int $id) {
        $machine = Machines::with('categorie:id,nom')->findOrFail($id);
        $this->job['machine_id'] = $machine->id;
        $this->job['categorie_id'] = null;

        $this->chargerSuggestionsConsommables($machine->id_categorie);

        return $machine->categorie
            ? ['id' => $machine->categorie->id, 'nom' => $machine->categorie->nom]
            : null;
    }

    public function clientChoisi(int $id) {
        abort_unless($this->role === 'admin', 403);

        $client = Clients::findOrFail($id);
        $this->job['client_id'] = $client->id;
        $this->chargerSuggestionsProjets($client->id);

        $projetId = $this->job['projet_id'];
        if ($projetId !== null && ! Projets::whereKey($projetId)->where('id_client', $client->id)->exists()) {
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

        return $client ? ['id' => $client->id, 'nom' => $client->nom] : null;
    }

    private function chargerSuggestionsProjets(?int $idClient = null) {
        $this->suggestions['projets'] = Projets::query()
            ->when($idClient !== null, fn ($query) => $query->where('id_client', $idClient))
            ->pluck('nom', 'id')
            ->toArray();
    }

    private function chargerSuggestionsConsommables(?int $idCategorie = null){
        $this->suggestions['consommables'] = Consommables::query()
            ->with('uniteObjet')
            ->when($idCategorie !== null, fn ($query) => $query->where('id_categorie', $idCategorie))
            ->get()
            ->map(fn (Consommables $consommable) => [
                'id'    => $consommable->id,
                'nom'   => $consommable->nom,
                'unite' => $consommable->uniteObjet?->unite,
            ])
            ->toArray();
    }

    public function creer(){
        $estAdmin = $this->role === 'admin';

        $this->validate([
            'job.description'             => ['nullable', 'string', 'max:500'],
            'job.machine_id'              => ['nullable', 'exists:machines,id'],
            'job.categorie_id'            => ['nullable', Rule::exists('categories', 'id')->where('type', TypeCategorie::Humain->value)],
            'job.projet_id'               => ['nullable', 'exists:projets,id'],
            'job.client_id'               => ['nullable', 'exists:clients,id'],
            'job.operateur_id'            => [$estAdmin ? 'required' : 'nullable', 'exists:collaborateurs,id'],
            'job.date_debut'              => ['required', 'date'],
            'job.duree_heures'            => ['nullable', 'integer', 'min:0'],
            'job.duree_minutes'           => ['nullable', 'integer', 'min:0', 'max:59'],
            'job.date_fin'                => ['nullable', 'date'],
            'job.consommables.*.id'       => ['required', 'exists:consommables,id'],
            'job.consommables.*.quantite' => ['required', 'numeric', 'gt:0'],
        ]);

        // catégorie d'un job machine = celle de la machine 
        $machine = $this->job['machine_id'] ? Machines::findOrFail($this->job['machine_id']) : null;
        $idCategorie = $machine?->id_categorie ?? $this->job['categorie_id'];

        if (! $idCategorie) {
            $this->addError('job.categorie_id', 'Choisis une machine ou une catégorie.');
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
            ! $estAdmin    => 'en attente',
            $fin->isPast() => 'terminé',
            default        => 'en cours',
        };

        $jobCree = DB::transaction(function () use ($idCategorie, $clientId, $operateurId, $projetId, $debut, $fin, $statut) {
            $nouveau = Jobs::create([
                'id_categorie' => $idCategorie,
                'status'       => $statut,
                'id_operateur' => $operateurId,
                'id_client'    => $clientId,
                'id_projet'    => $projetId,
                'description'  => $this->job['description'],
                'id_machine'   => $this->job['machine_id'],
                'debut'        => $debut,
                'fin'          => $fin,
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