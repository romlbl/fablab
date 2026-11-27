<?php

namespace App\Livewire\Client;

use App\Models\Categories;
use App\Models\Machines as MachinesModel;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Machines extends Component
{
    public string $search = '';
    public array $filtreCategories = [];
    public array $choixCate = [];

    public function mount(){
        $this->choixCate = Categories::query()
            ->where('type', 'machine')
            ->pluck('nom', 'id')
            ->toArray();
    }

    #[Computed]
    public function resultats() {
        $query = MachinesModel::query()->with('categorie');
        $terme = trim($this->search);

        if ($terme !== '') {
            $echappe = addcslashes($terme, '%_\\');
            $motif = '%' . $echappe . '%';

            $query->where(function ($q) use ($motif) {
                $q->where('nom', 'like', $motif)
                    ->orWhere('description', 'like', $motif);
            });
        }

        $categoriesIds = array_values(array_map('intval', array_filter($this->filtreCategories)));

        if ($categoriesIds !== []) {
            $query->whereIn('id_categorie', $categoriesIds);
        }

        return $query->orderBy('nom')->get();
    }

    public function render()
    {
        return view('livewire.client.machines');
    }
}