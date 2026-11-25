<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Types_Client;
use App\Models\Projets;
use App\Models\Jobs;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clients extends Model
{
    //relations entre tables
    public function typeClient(): BelongsTo
    {
        return $this->belongsTo(Types_client::class, 'id_type_client');
    }

    public function projets(): HasMany
    {
        return $this->hasMany(Projets::class, 'id_client');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_client');
    }
    //---

    protected $table = 'clients';

    protected $fillable = [
        'nom',
        'id_type_client',
        'code_acces_hash',
        'date_creation',
        'date_archive',
    ];

    protected $hidden = [
        'code_acces_hash',
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
