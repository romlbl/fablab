<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Consommables;
use App\Models\Jobs;

class Jobs_consommables extends Model
{
    protected $fillable = ['id_job', 'id_consommable', 'quantite', 'cout', 'cout_tot'];

    //relations tables
    public function job(): BelongsTo
    {
        return $this->belongsTo(Jobs::class, 'id_job');
    }

    public function consommable(): BelongsTo
    {
        return $this->belongsTo(
            Consommables::class,
            'id_consommable'
        );
    }
}
