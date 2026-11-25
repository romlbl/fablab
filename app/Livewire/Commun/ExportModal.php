<?php

namespace App\Livewire\Commun;

use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\Jobs;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportModal extends Component
{
    #[Reactive]
    public array $items = [];

    #[Reactive]
    public array $filtres = [];

    public array $export = [];

    public array $correspondanceColonnes = [
        'projets' => 'id_projet',
        'machines' => 'id_machine',
        'types' => 'id_type_travail',
        'client' => 'id_client',
        'operateur' => 'id_operateur',
    ];

    public function mount(){
        $this->initialiserExport();
    }

    public function updatedFiltres(){
        $this->initialiserExport();
    }

    public function initialiserExport(){
        $aDates = !empty($this->filtres['dateDebut']) || !empty($this->filtres['dateFin']);

        $this->export = array_merge(
            $this->filtres,
            [
                'dateDebut' => $this->filtres['dateDebut'] ?? null,
                'dateFin'   => $this->filtres['dateFin'] ?? null,
                'format'    => $this->export['format'] ?? 'xlsx',
            ]
        );
    }


    public function requeteJobs() {
        $role = session('auth_role');
        $idUser = session('auth_user_id');

        //on trouve les jobs
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

        foreach ($this->export as $colonne => $valeur) {
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

        return $requete;
    }

    public function formaterLignes($jobs) {
        $lignes = [];
        $role = session('auth_role');

        foreach ($jobs as $job) {
            if (!in_array($role, ['admin', 'client'], true)) {
                continue;
            }

            $listeConsommables = $job->jobConsommables;

            if ($listeConsommables->isEmpty()) {
                $ligne = [
                    'description' => $job->description ?? '',
                    'machine'     => $job->machine?->nom ?? '',
                    'categorie'   => $job->machine?->categorie?->nom ?? '',
                    'consommable' => '',
                    'unite'       => '',
                    'quantite'    => '',
                ];

                if ($role === 'admin') {
                    $ligne['cout'] = '';
                }

                $ligne['projet'] = $job->projet?->nom ?? '';

                if ($role === 'admin') {
                    $ligne['client']    = $job->client?->nom ?? '';
                    $ligne['operateur'] = $job->operateur?->nom ?? '';
                }

                $ligne['type_travail'] = $job->typeTravail?->nom ?? '';
                $ligne['debut']        = $job->debut;
                $ligne['fin']          = $job->fin;
                $ligne['status']       = $job->status;

                $lignes[] = $ligne;
            }else{

                foreach ($listeConsommables as $index => $consommableJob) {
                    $estPremiereLigne = $index === 0;
                    $consommable = $consommableJob->consommable;

                    $ligne = [
                        'description'=> $estPremiereLigne ? ($job->description ?? '') : '',
                        'machine'=> $estPremiereLigne ? ($job->machine?->nom ?? '') : '',
                        'categorie' => $estPremiereLigne ? ($job->machine?->categorie?->nom ?? '') : '' ,
                        'consommable' => $consommable?->nom ?? '',
                        'unite'=> $consommable?->uniteObjet?->unite ?? '',
                        'quantite'=> $consommableJob->quantite ?? '',
                    ];
                    if ($role === 'admin') {
                        $ligne['cout'] = $consommableJob->cout ?? '';   
                    }  
                    $ligne['projet' ] = $estPremiereLigne ? ($job->projet?->nom ?? '') : '';
                    if ($role === 'admin') {
                        $ligne['client'] = $estPremiereLigne ? ($job->client?->nom ?? '') : '';   
                    }  
                    if ($role === 'admin') {
                        $ligne['operateur'] = $estPremiereLigne ? ($job->operateur?->nom ?? '') : '';   
                    }
                    $ligne['type_travail'] = $estPremiereLigne ? ($job->typeTravail?->nom ?? '') : '';
                    $ligne['debut'] = $estPremiereLigne ? ($job->debut) : '';
                    $ligne['fin'] = $estPremiereLigne ? ($job->fin) : '';
                    $ligne['status'] = $estPremiereLigne ? ($job->status) : '';

                    $lignes[] = $ligne;
                }
            }
        }

        return $lignes;
    }

    public function exporter(){
        $jobs   = $this->requeteJobs()->get();
        $lignes = $this->formaterLignes($jobs);
        $format = $this->export['format'] ?? 'xlsx';
        $nomFichierBase = 'export-jobs-' . now()->format('Y-m-d_H-i');

        if ($format === 'csv') {
            return $this->telechargerCsv($lignes, $nomFichierBase . '.csv');
        }

        return $this->telechargerXlsx($lignes, $nomFichierBase . '.xlsx');
    }

    protected function telechargerXlsx( $lignes, $nomFichier){
        return response()->streamDownload(function () use ($lignes) {
            $writer = new Writer();
            $writer->openToFile('php://output');

            if (!empty($lignes)) {
                $writer->addRow(Row::fromValues(array_keys($lignes[0])));

                // Lignes de données
                foreach ($lignes as $ligne) {
                    $writer->addRow(Row::fromValues(array_values($ligne)));
                }
            }

            $writer->close();
        }, $nomFichier, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function telechargerCsv($lignes, $nomFichier){
        return response()->streamDownload(function () use ($lignes) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            if (!empty($lignes)) {
                fputcsv($handle, array_keys($lignes[0]), ';');
                foreach ($lignes as $ligne) {
                    fputcsv($handle, $ligne, ';');
                }
            }

            fclose($handle);
        }, $nomFichier, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }


    public function render()
    {
        return view('livewire.commun.export-modal');
    }
}