<?php

namespace App\Livewire\Commun;

use Livewire\Component;
use App\Models\Jobs;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportModal extends Component
{
    public array $export = [];
    public array $filtres = [];
    public array $items = [];

    public function mount($filtres, $items){
        $this->filtres = $filtres;
        $this->items = $items;
        $this->export = array_merge($this->filtres, ['format' => 'xlsx']);
    }

    public function requeteJobs()
    {
        return Jobs::avecDetails()
            ->visiblePar(session('auth_role'), session('auth_user_id'))
            ->filtrer($this->export)
            ->latest('debut');
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
                    'categorie'   => $job->categorie?->nom ?? '',
                    'consommable' => '',
                    'unite'       => '',
                    'quantite'    => '',
                ];

                if ($role === 'admin') {
                    $ligne['cout'] = '';
                }

                $ligne['projet'] = $job->projet?->nom ?? '';

                if ($role === 'admin') {
                    $ligne['client'] = $job->client?->nom ?? '';
                    $ligne['operateur'] = $job->operateur?->nom ?? '';
                }

                $ligne['debut'] = $job->debut ? Carbon::parse($job->debut)->format('d/m/Y H:i') : '';
                $ligne['fin'] = $job->fin ? Carbon::parse($job->fin)->format('d/m/Y H:i') : '';
                $ligne['status'] = $job->status;

                $lignes[] = $ligne;
            }else{

                foreach ($listeConsommables as $index => $consommableJob) {
                    $estPremiereLigne = $index === 0;
                    $consommable = $consommableJob->consommable;

                    $ligne = [
                        'description'=> $estPremiereLigne ? ($job->description ?? '') : '',
                        'machine'=> $estPremiereLigne ? ($job->machine?->nom ?? '') : '',
                        'categorie' => $estPremiereLigne ? ($job->categorie?->nom ?? '') : '' ,
                        'consommable' => $consommable?->nom ?? '',
                        'unite'=> $consommable?->uniteObjet?->unite ?? '',
                        'quantite'=> $consommableJob->quantite ?? '',
                    ];
                    if ($role === 'admin') {
                        $ligne['cout'] = $consommableJob->cout_tot ?? '';   
                    }  
                    $ligne['projet' ] = $estPremiereLigne ? ($job->projet?->nom ?? '') : '';
                    if ($role === 'admin') {
                        $ligne['client'] = $estPremiereLigne ? ($job->client?->nom ?? '') : '';   
                    }  
                    if ($role === 'admin') {
                        $ligne['operateur'] = $estPremiereLigne ? ($job->operateur?->nom ?? '') : '';   
                    }
                    $ligne['debut'] = $estPremiereLigne && $job->debut ? Carbon::parse($job->debut)->format('d/m/Y H:i') : '';
                    $ligne['fin'] = $estPremiereLigne ? Carbon::parse($job->fin)->format('d/m/Y H:i') : '';
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