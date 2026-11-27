<?php

namespace App\Livewire\Commun;

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Projets;
use App\Models\Machines;
use App\Models\Categories;
use Illuminate\Database\Eloquent\Collection;

class OngletTemps extends Component
{
    public Projets $projet;
    public String $search = '';
    public Collection $machines;
    public array $infos = [];
    public array $triCategories = [];
    public array $choixCate = [];

    public function mount(): void
    {
        $this->machines = $this->projet->machines()->get();
        $this->setInfos();
        $this->choixCate = Categories::pluck('nom', 'id')->toArray();
    }

    private function setInfos($tri = []) {
        $jobsParMachine = $this->projet->jobs()->get()->groupBy('id_machine');
        $this->infos = [];

        //partie machines
        foreach ($this->machines as $machine) {
            if ( $tri === []  || ($tri && in_array($machine->id_categorie, $tri))) {
                $tempsTotal = $jobsParMachine
                    ->get($machine->id, collect())
                    ->sum(fn ($job) => $job->debut->diffInMinutes($job->fin));

                $this->infos[] = [
                    'chemin_image' => $machine->chemin_image,
                    'nom' => $machine->nom,
                    'temps_total' => $tempsTotal,
                    'categorie' => $machine->categorie->nom,
                    'type' => $machine->categorie->type->value,
                ];
            }
        }
        //partie conception
        $jobsConception = $this->projet->jobs()->whereNull('id_machine')->get();
        foreach ($jobsConception as $job) {
            if ( $tri === []  || ($tri && in_array($job->categorie->id, $tri))) {
                $tempsTotalConception = $job->debut->diffInMinutes($job->fin);
                $this->infos[] = [
                    'chemin_image' => '',
                    'nom' => $job->categorie->nom,
                    'temps_total' => $tempsTotalConception,
                    'categorie' => $job->categorie->nom,
                    'type' => $job->categorie->type->value,
                ];
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

        return array_filter($this->infos, function ($elem) {
            return str_contains(strtolower($elem['nom']), strtolower($this->search));
        });

    }
    
    public function render()
    {
        return view('livewire.commun.onglet-temps');
    }
}
