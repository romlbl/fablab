<?php

namespace App\Livewire\Commun;

use App\Models\Jobs;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ListeJobs extends Component
{
    #[Reactive]
    public array $filtres = [];
    public string $ChampTri = 'debut';
    public string $valeurTri = 'desc';

    private function colonnesVisibles(): array
    {
        return match (session('auth_role')) {
            'client' => ['description', 'projet', 'machine', 'debut', 'fin', 'consommables', 'catégorie', 'statut'],
            'admin'  => ['description', 'projet', 'machine', 'debut', 'fin', 'consommables', 'catégorie', 'statut', 'client', 'prix', 'operateur', 'action'],
            default  => [],
        };
    }

    #[On('job-ajoute')]
    public function rafraichir(){
    }

    public function tri($champ): void
    {
        if ($champ === 'debut') {
            $this->ChampTri = 'debut';
            $this->valeurTri = $this->valeurTri === 'desc' ? 'asc' : 'desc';
        }elseif ($champ === 'fin') {
            $this->ChampTri = 'fin';
            $this->valeurTri = $this->valeurTri === 'desc' ? 'asc' : 'desc';
        }
    }

    public function render()
    {
        $jobs = Jobs::avecDetails()
            ->visiblePar(session('auth_role'), session('auth_user_id'))
            ->filtrer($this->filtres)
            ->orderBy($this->ChampTri, $this->valeurTri)
            ->get();

        return view('livewire.commun.liste-jobs', [
            'jobs'    => $jobs,
            'visible' => $this->colonnesVisibles(),
        ]);
    }
}