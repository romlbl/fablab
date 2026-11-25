<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Jobs;
use App\Models\Type_job;

class Type_travail extends Model
{
    protected $table = 'type_travails';

    protected $fillable = [
        'nom',
        'id_job_type',
    ];
    //relations tables
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(Type_job::class, 'id_job_type');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_type_travail');
    }
}
