<?php

namespace App\Models;

use App\Enums\TypeCategorie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories extends Model
{
    protected $fillable = ['nom', 'type'];

    protected function casts(): array
    {
        return ['type' => TypeCategorie::class];
    }

    public function scopeDeType(Builder $q, TypeCategorie $type): Builder
    {
        return $q->where('type', $type);
    }

    public function machines(): HasMany
    {
        return $this->hasMany(Machines::class, 'id_categorie');
    }

    public function consommables(): HasMany
    {
        return $this->hasMany(Consommables::class, 'id_categorie');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_categorie');
    }
}