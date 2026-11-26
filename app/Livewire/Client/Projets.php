<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Clients;
use App\Models\Projets as ProjetsModel;
 
class Projets extends Component
{
    public ?Clients $client = null;
    public string $search = '';
    public $projets;

    public function mount()
    {   
        // client connecté et non archivé, sinon accès refusé
        $this->client = Clients::whereNull('date_archive')->find(session('auth_user_id'));
        abort_unless($this->client, 403);
        $this->projets = ProjetsModel::where('id_client', $this->client->id)->get();
    }

    #[Computed]
    public function resultats()
    {
        $query = ProjetsModel::where('id_client', $this->client->id);

        if (trim($this->search) !== '') {
            $query->where(function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.client.projets');
    }
}
