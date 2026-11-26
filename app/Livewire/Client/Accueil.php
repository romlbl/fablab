<?php

namespace App\Livewire\Client;

use App\Models\Categories;
use App\Models\Clients;
use App\Models\Consommables;
use App\Models\Jobs;
use App\Models\Machines;
use App\Models\Projets;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Accueil extends Component
{
    public ?Clients $client = null;

    public string $selecteurMachines = 'machine';
    public string $selecteurConception = 'poste';

    public array $filtres = [
        'machines'     => [],
        'categories'   => [],
        'projets'      => [],
        'consommables' => [],
        'statut'       => [],
        'dateDebut'    => null,
        'dateFin'      => null,
    ];

    protected array $couleursDispo = [
        '#E1AE17', '#132340', '#5BADCB', '#F2C94C', '#F2994A', '#6FCF97', '#27AE60',
        '#219653', '#56CCF2', '#2D9CDB', '#BB6BD9', '#9B51E0', '#EB5757', '#FF0000',
    ];

    public function mount() {
        // client connecté et non archivé, sinon accès refusé
        $this->client = Clients::whereNull('date_archive')->find(session('auth_user_id'));
        abort_unless($this->client, 403);
    }

    // l'event relance render() : donuts et conso se mettent à jour
    #[On('job-ajoute')]
    public function rafraichir(){
    }

    // tous les jobs du client, chargés une seule fois
    #[Computed]
    public function jobsClient(){
        return $this->client->jobs()
            ->with([
                'machine', 'categorie', 'projet',
                'jobConsommables.consommable.categorie',
                'jobConsommables.consommable.uniteObjet',
            ])
            ->get();
    }

    #[Computed]
    public function statsMachines() {
        $jobs = $this->jobsClient->whereNotNull('id_machine');

        return $this->segments($jobs, $this->selecteurMachines === 'projet'
            ? fn ($job) => $job->projet?->nom ?? 'Sans projet'
            : fn ($job) => $job->machine->nom);
    }

    #[Computed]
    public function statsConception(){
        $jobs = $this->jobsClient->whereNull('id_machine');

        return $this->segments($jobs, $this->selecteurConception === 'projet'
            ? fn ($job) => $job->projet?->nom ?? 'Sans projet'
            : fn ($job) => $job->categorie->nom);
    }

    #[Computed]
    public function dataConsommables(){
        return $this->jobsClient
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

    // listes des filtres : [libellé, [id => nom]]
    #[Computed]
    public function items(){
        return [
            'machines'     => ['Machines', Machines::pluck('nom', 'id')->toArray()],
            'categories'   => ['Catégories', Categories::pluck('nom', 'id')->toArray()],
            'projets'      => ['Projets', Projets::where('id_client', $this->client->id)->pluck('nom', 'id')->toArray()],
            'consommables' => ['Consommables', Consommables::pluck('nom', 'id')->toArray()],
            'statut'       => ['Statuts', Jobs::STATUTS],
        ];
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
        return view('livewire.client.accueil', [
            'statsMachines'    => $this->statsMachines,
            'statsConception'  => $this->statsConception,
            'dataConsommables' => $this->dataConsommables,
            'items'            => $this->items,
        ]);
    }
}