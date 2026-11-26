<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Categories;
use App\Models\Unites;
use App\Models\Job_consommables;
use App\Models\Jobs;

class Consommables extends Model
{
    //relations tables
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'id_categorie');
    }

    public function uniteObjet(): BelongsTo
    {
        return $this->belongsTo(Unites::class, 'id_unite');
    }

    public function jobConsommables(): HasMany
    {
        return $this->hasMany(Jobs_consommables::class, 'id_consommable');
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(
            Jobs::class,
            'jobs_consommables',
            'id_consommable',
            'id_job'
        )->withPivot(['id', 'quantite', 'cout', 'cout_tot']);
    }
}
