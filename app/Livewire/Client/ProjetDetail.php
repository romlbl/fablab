<?php

namespace App\Livewire\Client;

use App\Models\Projets;
use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Jobs;

class ProjetDetail extends Component
{
    public $projetId;
    public Projets $projet;

    public function mount(int|string $projetId): void
    {
        $this->projetId = $projetId;
        $this->projet = Projets::findOrFail($this->projetId);
    }

    protected array $couleursDispo = [
        '#E1AE17', '#132340', '#5BADCB', '#F2C94C', '#F2994A', '#6FCF97', '#27AE60',
        '#219653', '#56CCF2', '#2D9CDB', '#BB6BD9', '#9B51E0', '#EB5757', '#FF0000',
    ];

    #[Computed]
    public function jobsProjet(){
        return $this->projet->jobs()
            ->with([
                'machine', 'categorie', 'projet',
                'jobConsommables.consommable.categorie',
                'jobConsommables.consommable.uniteObjet',
            ])
            ->get();
    }

    #[Computed]
    public function statsMachines() {
        $jobs = $this->jobsProjet->whereNotNull('id_machine');

        return $this->segments($jobs, fn ($job) => $job->machine->nom);
    }

    #[Computed]
    public function statsConception(){
        $jobs = $this->jobsProjet->whereNull('id_machine');

        return $this->segments($jobs, fn ($job) => $job->categorie->nom);
    }

    #[Computed]
    public function dataConsommables(){
        return $this->jobsProjet
            ->flatMap(fn ($job) => $job->jobConsommables)
            ->groupBy('id_consommable')
            ->map(function ($lignes) {
                $conso = $lignes->first()->consommable;

                return [
                    'nom'       => $conso->nom,
                    'catégorie' => $conso->categorie->nom ?? '',
                    'quantité'  => $lignes->sum('quantite'),
                    'unité'     => $conso->uniteObjet->unite ?? '',
                ];
            })
            ->values()
            ->all();
    }

    // regroupe les jobs par libellé et additionne les minutes
    private function segments(Collection $jobs, callable $libelle){
        $segments = [];

        foreach ($jobs->groupBy($libelle) as $label => $groupe) {
            $minutes = $groupe->sum(fn ($job) => $job->dureeMinutes());

            if ($minutes > 0) {
                $segments[] = [
                    'label' => (string) $label,
                    'value' => $minutes,
                    'color' => $this->couleurPourSegment($segments, (string) $label),
                ];
            }
        }

        return [
            'segments' => $segments,
            'total'    => array_sum(array_column($segments, 'value')),
        ];
    }

    private function couleurPourSegment(array $segments, string $label){
        $utilisees = array_column($segments, 'color');
        $prefere = $this->couleursDispo[abs(crc32($label)) % count($this->couleursDispo)];

        if (! in_array($prefere, $utilisees, true)) {
            return $prefere;
        }

        foreach ($this->couleursDispo as $couleur) {
            if (! in_array($couleur, $utilisees, true)) {
                return $couleur;
            }
        }

        return $this->couleursDispo[0];
    }

    public function render()
    {
        return view('livewire.client.projet-detail',[
            'statsMachines'    => $this->statsMachines,
            'statsConception'  => $this->statsConception,
            'dataConsommables' => $this->dataConsommables,
        ]);
    }
}