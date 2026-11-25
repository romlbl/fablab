<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jobs;
use App\Models\Projets;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collaborateurs extends Model
{
    //relations table
    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_operateur');
    }

    public function projets(): BelongsToMany
    {
        return $this->belongsToMany(
            Projets::class,
            'jobs',
            'id_operateur',
            'id_projet'
        )->wherePivotNotNull('id_projet')->distinct();
    }
    //---

    protected $table = 'collaborateurs';

    protected $fillable = [
        'nom',
        'type',
        'mdp_hash',
        'date_creation',
        'date_archive',
    ];

    protected $hidden = [
        'mdp_hash',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
            'date_archive'  => 'datetime',
        ];
    }

    public function estArchive(): bool
    {
        return ! is_null($this->date_archive);
    }
}