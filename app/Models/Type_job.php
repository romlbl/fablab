<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Jobs;
use App\Models\Type_travail;

class Type_job extends Model
{
    protected $fillable = [
        'nom',
    ];

    //relations tables
    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_type');
    }

    public function typesTravail(): HasMany
    {
        return $this->hasMany(Type_travail::class, 'id_job_type');
    }
}
