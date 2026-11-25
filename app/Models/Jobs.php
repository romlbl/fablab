<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Collaborateurs;
use App\Models\Clients;
use App\Models\Projets;
use App\Models\Machines;
use App\Models\Jobs_consommables;
use App\Models\Consommables;
use App\Models\Type_job;
use App\Models\Type_travail;    

class Jobs extends Model
{
    //relations tables
    public function operateur(): BelongsTo
    {
        return $this->belongsTo(
            Collaborateurs::class,
            'id_operateur'
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projets::class, 'id_projet');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machines::class, 'id_machine');
    }

    public function jobConsommables(): HasMany
    {
        return $this->hasMany(Jobs_consommables::class, 'id_job');
    }
    
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(Type_job::class, 'id_type');
    }

    public function typeTravail(): BelongsTo
    {
        return $this->belongsTo(Type_travail::class, 'id_type_travail');
    }

    public function consommables(): BelongsToMany
    {
        return $this->belongsToMany(
            Consommables::class,
            'job_consommables',
            'id_job',
            'id_consommable'
        )->withPivot(['id', 'quantite', 'cout', 'cout_tot']);
    }

}
