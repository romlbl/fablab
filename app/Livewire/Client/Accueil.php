<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Clients;
use App\Models\Machines;
use Illuminate\Support\Carbon;
use App\Models\Projets;
use App\Models\Consommables;
use App\Models\Categories;
use App\Models\Jobs;
 
class Accueil extends Component
{
    //variable connexion
    public ?Clients $client = null;
    public ?string $nomSession = null;
    public ?int $userIdSession = null;

    public function mount(): void
    {
        $this->userIdSession = session('auth_user_id');
        $this->nomSession = session('auth_user_nom');

        if ($this->userIdSession) {
            $this->client = Clients::query()
                ->where('id', $this->userIdSession)
                ->whereNull('date_archive')
                ->first();
        }

        //execution des fonctions
        $this->donneesConsommables();
        $this->initItems();
    }

    //donut heures machines
    public array $couleursDispo = ['#E1AE17','#132340','#5BADCB','#F2C94C','#F2994A','#F2C94C','#6FCF97','#27AE60','#219653','#56CCF2','#2D9CDB','#BB6BD9','#9B51E0','#EB5757','#FF0000'];
    public string $selecteurMachines = 'machine'; 

    private function couleurPourSegment($segments,$label){
        $couleursUtilisees = array_column($segments, 'color');
        $index = abs(crc32($label)) % count($this->couleursDispo);

        if (!in_array($this->couleursDispo[$index], $couleursUtilisees, true)) {
            return $this->couleursDispo[$index];
        }

        foreach ($this->couleursDispo as $couleur) {
            if (!in_array($couleur, $couleursUtilisees, true)) {
                return $couleur;
            }
        }

        return $this->couleursDispo[0];
    }

    public function segmentsMachines(){
        $segements = [];
        $total = 0;
        foreach ($this->client->jobs()->whereNotNull('id_machine')->with('machine')->get() as $job) {
            $nomMachine = $job->machine->nom;
            $debut = Carbon::parse($job->debut);
            $fin = Carbon::parse($job->fin);
            $dureeMinutes = (int) $debut->diffInMinutes($fin);
            $total += $dureeMinutes;

            $indexSegment = array_search($nomMachine, array_column($segements, 'label'), true);
            if ($indexSegment === false) {
                $segements[] = [
                    'label' => $nomMachine,
                    'value' => $dureeMinutes,
                    'color' => $this->couleurPourSegment($segements, $nomMachine),
                ];
            } else {
                $segements[$indexSegment]['value'] += $dureeMinutes;
            }
        }

        return [$segements,$total];
    }

    public function segmentsProjetsMachines(){
        $segements = [];
        $total = 0;
        foreach($this->client->projets as $projet){
            $nomProjet = $projet->nom;
            $dureeMinutes = 0;
            foreach($projet->jobs()->whereNotNull('id_machine')->get() as $job){
                $debut = Carbon::parse($job->debut);
                $fin = Carbon::parse($job->fin);
                $dureeMinutes += (int) $debut->diffInMinutes($fin);
            }
            $total += $dureeMinutes;
            if($dureeMinutes > 0 && !in_array($nomProjet, array_column($segements, 'label'))){
                $segements[] = [
                    'label' => $nomProjet,
                    'value' => $dureeMinutes,
                    'color' => $this->couleurPourSegment($segements, $nomProjet),
                ];
            }
        }

        return [$segements,$total];
    }

    public function getStatsMachinesProperty(){   
        $dataMachines = $this->segmentsMachines();
        $dataProjets = $this->segmentsProjetsMachines();

        return match ($this->selecteurMachines) {
            default => [
                'total' => $dataMachines[1],
                'segments' => $dataMachines[0]
            ],
            'projet' => [
                'total' => $dataProjets[1],
                'segments' => $dataProjets[0]
            ]
        };
    }

    //donuts temps conception
    public string $selecteurConception = 'poste'; 

    public function segmentsPosteConception(){
        $segements = [];
        $total = 0;
        foreach ($this->client->jobs()->whereNull('id_machine')->with('categorie')->get() as $job) {
            $poste = $job->categorie->nom;
            $debut = Carbon::parse($job->debut);
            $fin = Carbon::parse($job->fin);
            $dureeMinutes = (int) $debut->diffInMinutes($fin);
            $total += $dureeMinutes;

            $indexSegment = array_search($poste, array_column($segements, 'label'), true);
            if ($indexSegment === false) {
                $segements[] = [
                    'label' => $poste,
                    'value' => $dureeMinutes,
                    'color' => $this->couleurPourSegment($segements, $poste),
                ];
            } else {
                $segements[$indexSegment]['value'] += $dureeMinutes;
            }
        }

        return [$segements,$total];
    }

    public function segmentsProjetsConception(){
        $segements = [];
        $total = 0;
        foreach($this->client->projets as $projet){
            $nomProjet = $projet->nom;
            $dureeMinutes = 0;
            foreach($projet->jobs()->whereNull('id_machine')->get() as $job){
                $debut = Carbon::parse($job->debut);
                $fin = Carbon::parse($job->fin);
                $dureeMinutes += (int) $debut->diffInMinutes($fin);
            }
            $total += $dureeMinutes;
            if($dureeMinutes > 0 && !in_array($nomProjet, array_column($segements, 'label'))){
                $segements[] = [
                    'label' => $nomProjet,
                    'value' => $dureeMinutes,
                    'color' => $this->couleurPourSegment($segements, $nomProjet),
                ];
            }
        }

        return [$segements,$total];
    }

    public function getStatsConceptionProperty() {   
        $dataConception = $this->segmentsPosteConception();
        $dataProjetsConception = $this->segmentsProjetsConception();

        return match ($this->selecteurConception) {
            default => [
                'total' => $dataConception[1],
                'segments' => $dataConception[0]
            ],
            'projet' => [
                'total' => $dataProjetsConception[1],
                'segments' => $dataProjetsConception[0]
            ]
        };
    }
    
    //carte consommables
    public array $dataConsommables = [];

    public function donneesConsommables(): void
    {
        foreach ($this->client->jobs as $job) {
            foreach ($job->jobConsommables as $consommableUtilise) {
                $nomConsommable = $consommableUtilise->consommable->nom;
                $categorieConsommable = $consommableUtilise->consommable->categorie->nom ?? '';
                $quantite = $consommableUtilise->quantite;
                $unité = $consommableUtilise->consommable->uniteObjet->unite ?? '';

                if (!isset($this->dataConsommables[$nomConsommable])) {
                    $this->dataConsommables[$nomConsommable] = [
                        'nom' => $nomConsommable,
                        'catégorie' => $categorieConsommable,
                        'quantité' => $quantite,
                        'unité' => $unité,
                    ];
                } else {
                    $this->dataConsommables[$nomConsommable]['quantité'] += $quantite;
                }
            }
        }
    }

    #[On('job-ajoute')]
    public function rafraichir(){
        $this->donneesConsommables();
    }

    //gestion des filtres
    public array $machines = [];
    public array $categories = [];
    public array $projets = [];
    public array $items = [];
    public array $consommables = [];
    public array $statut = [];
    public array $filtres = [
        'machines' => [],
        'categories' => [],
        'projets' => [],
        'consommables' => [],
        'statut' => [],
        'dateDebut' => null,
        'dateFin' => null,
    ];

    public function initItems()
    {
        $this->machines = Machines::orderBy('nom')->pluck('nom', 'id')->toArray();
        $this->projets = Projets::query()->where('id_client',$this->userIdSession)->orderBy('nom')->pluck('nom', 'id')->toArray();
        $this->consommables = Consommables::orderBy('nom')->pluck('nom', 'id')->toArray();
        $this->categories = Categories::pluck('nom', 'id')->toArray();
        $this->statut = Jobs::STATUTS; 

    }

    public function render()
    {
        $this->items = [
            'machines' => ["Machines", $this->machines],
            'categories' => ["Catégories", $this->categories],
            'projets' => ["Projets", $this->projets],
            'consommables' => ["Consommables", $this->consommables],
            'statut' => ["Statuts", $this->statut],
        ];

        return view('livewire.client.accueil', [
            'statsMachines' => $this->statsMachines,
            'statsConception' => $this->statsConception,
            'dataConsommables' => $this->dataConsommables,
            'items' => $this->items,
        ]);
    }
}
