<?php

namespace App\Livewire\Commun;

use Livewire\Component;
use App\Models\Projets;
use App\Models\Machines;
use Illuminate\Database\Eloquent\Collection;

class OngletTemps extends Component
{
    public Projets $projet;
    public String $search = '';
    public Collection $machines;
    public array $infosMachines = [];

    public function mount(): void
    {
        $this->machines = $this->projet->machines()->get();
        $this->setInfosMachines();
    }

    private function setInfosMachines(): void
    {
        $jobsParMachine = $this->projet->jobs()->get()->groupBy('id_machine');

        foreach ($this->machines as $machine) {
            $tempsTotal = $jobsParMachine
                ->get($machine->id, collect())
                ->sum(fn ($job) => $job->debut->diffInMinutes($job->fin));

            $this->infosMachines[$machine->id] = [
                'chemin_image' => $machine->chemin_image,
                'nom' => $machine->nom,
                'temps_total' => $tempsTotal,
                'categorie' => $machine->categorie->nom,
            ];
        }
    }

    public function render()
    {
        return view('livewire.commun.onglet-temps');
    }
}
