<?php

namespace App\Livewire\Commun;

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Projets;
use App\Models\Consommables;
use App\Models\Categories;
use Illuminate\Database\Eloquent\Collection;

class OngletConsos extends Component
{

    public Projets $projet;
    public String $search = '';
    public Collection $jobsProjets;
    public array $infos = [];
    public array $triCategories = [];
    public array $choixCate = [];

    public function mount(): void
    {
        $this->jobsProjets = $this->projet->jobs()->get();
        $this->setInfos();
        $this->choixCate = Categories::pluck('nom', 'id')->toArray();
    }

    private function setInfos($tri = []) {
        $this->infos = [];

        foreach ($this->jobsProjets as $job) {
            foreach ($job->jobConsommables as $jobConso) {
                $conso = $jobConso->consommable;
                $categorie = $conso->categorie;

                if (!empty($tri) && !in_array($categorie->id, $tri)) {
                    continue;
                }

                if (!isset($this->infos[$conso->id])) {
                    $this->infos[$conso->id] = [
                        'chemin_image' => $conso->chemin_image,
                        'nom' => $conso->nom,
                        'categorie' => $categorie->nom,
                        'quantite' => 0,
                        'unite' => $conso->uniteObjet?->unite ?? '',
                    ];
                }

                $this->infos[$conso->id]['quantite'] += $jobConso->quantite;
            }
        }
    }

    public function updatedTriCategories(){   
        $this->setInfos($this->triCategories);
    }


    #[Computed]
    public function resultats() {
        if (empty($this->search)) {
            return $this->infos;
        }

        return array_filter($this->infos, function ($machine) {
            return str_contains(strtolower($machine['nom']), strtolower($this->search));
        });

    }

    public function render()
    {
        return view('livewire.commun.onglet-consos');
    }
}
