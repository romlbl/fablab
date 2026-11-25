<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Projets;

class Categories_projets extends Model
{
    //relations tables
    public function projets(): HasMany
    {
        return $this->hasMany(Projets::class, 'id_categorie_projet');
    }
}
