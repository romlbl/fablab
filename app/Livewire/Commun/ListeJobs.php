<?php

namespace App\Livewire\Commun;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\Session;
use App\Models\Jobs;

class ListeJobs extends Component
{
    #[Reactive]
    public array $filtres;
    public $jobs = [];
    public $visible = [];
    public array $correspondanceColonnes = [
        'projets' => 'id_projet',
        'machines' => 'id_machine',
        'conception' => 'id_type_travail',
        'client' => 'id_client',
        'operateur' => 'id_operateur',
    ];

    public function appliquerFiltres(){
        $role = session('auth_role');
        $idUser = session('auth_user_id');

        $requete = Jobs::query()->with([
            'machine.categorie',
            'client',
            'projet',
            'operateur',
            'typeTravail',
            'jobConsommables.consommable',
        ]);

        if (in_array($role, ['client', 'admin'], true)) {
            $requete->where($role === 'client' ? 'id_client' : 'id_operateur', $idUser);
        }

        foreach ($this->filtres as $colonne => $valeur) {
            if ($valeur === null || $valeur === '' || $valeur === []) {
                continue;
            }
            if ($colonne === 'consommables') {
                $idsConsommables = is_array($valeur) ? $valeur : [$valeur];
                $requete->whereHas('jobConsommables', function ($query) use ($idsConsommables) {
                    $query->whereIn('id_consommable', $idsConsommables);
                });
            } elseif ($colonne === 'dateDebut') {
                $requete->whereDate('debut', '>=', $valeur);
            } elseif ($colonne === 'dateFin') {
                $requete->whereDate('fin', '<=', $valeur);
            } elseif ($colonne === 'statut') {
                $selectionStatut = ["en attente", "en cours", "Terminé", "arreté"];
                foreach ($selectionStatut as $key => $statut) {
                    if (!in_array($key, $valeur)) {
                        unset($selectionStatut[$key]);
                    }
                }
                $requete->whereIn('status', $selectionStatut);
            }elseif ($colonne === 'categories') {
                $idsCategories = is_array($valeur) ? $valeur : [$valeur];
                $requete->whereHas('machine.categorie', function ($query) use ($idsCategories) {
                    $query->whereIn('id', $idsCategories);
                });
            } elseif (isset($this->correspondanceColonnes[$colonne])) {
                $ids = is_array($valeur) ? $valeur : [$valeur];
                $requete->whereIn($this->correspondanceColonnes[$colonne], $ids);
            }
        }

        $this->jobs = $requete->get();
    }

    public function updatedFiltres(){
        $this->appliquerFiltres();
    }

    public function mount(){   
        $role = session('auth_role');
        $idUser= session('auth_user_id');

        if($role == 'client') {
            $this->visible = ['description','projet', 'machine', 'debut', 'fin', 'consommables', 'catégorie','statut'];
        }else if($role == 'admin') {
            $this->visible = ['description','projet', 'machine', 'debut', 'fin', 'consommables', 'catégorie','statut','client','prix', 'operateur','action' ];
        }

        $this->appliquerFiltres();
    }


    public function render()
    {
        return view('livewire.commun.liste-jobs');
    }
}
