<?php

namespace App\Livewire\Commun;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Categories;
use App\Models\Clients;
use App\Models\Consommables;
use App\Models\Jobs;
use App\Models\Machines;
use App\Models\Projets;

class OngletJobs extends Component
{
    public Projets $projet;

    public array $filtres = [];
    public array $valeursDefaults = [];

    function mount() {
        $this->filtres = [
            'machines'     => [],
            'categories'   => [],
            'projets'      => [$this->projet->id],
            'consommables' => [],
            'statut'       => [],
            'dateDebut'    => null,
            'dateFin'      => null,
        ];

        $this->valeursDefaults["projet_id"] = $this->projet->id;
    }

    // listes des filtres : [libellé, [id => nom]]
    #[Computed]
    public function items(){
        return [
            'machines'     => ['Machines', Machines::pluck('nom', 'id')->toArray()],
            'categories'   => ['Catégories', Categories::pluck('nom', 'id')->toArray()],
            'consommables' => ['Consommables', Consommables::pluck('nom', 'id')->toArray()],
            'statut'       => ['Statuts', Jobs::STATUTS],
        ];
    }

    public function render()
    {
        return view('livewire.commun.onglet-jobs',[
            'items'=> $this->items,
        ]);
    }
}
