<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Clients;
use App\Models\Categories_projets;
use App\Models\Jobs;
use App\Models\Collaborateurs;

class Projets extends Model
{
    //relations tables
    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function categorieProjet(): BelongsTo
    {
        return $this->belongsTo(
            Categories_projets::class,
            'id_categorie_projet'
        );
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_projet');
    }

    public function machines(): BelongsToMany
    {
        return $this->belongsToMany(
            Machines::class,
            'jobs',
            'id_projet',
            'id_machine'
        )->distinct();
    }

    public function collaborateurs(): BelongsToMany
    {
        return $this->belongsToMany(
            Collaborateurs::class,
            'jobs',
            'id_projet',
            'id_operateur'
        )->distinct();
    }

    public function valorisation() {   
        $tot = 0;
        foreach ($this->jobs as $job) {
            $consommablesJob = $job->jobConsommables;
            foreach ($consommablesJob as $consommable) {
                $tot += $consommable->cout_tot;
            }
        }
        return $tot;
    }


}
