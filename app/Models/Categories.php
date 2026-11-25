<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Machines;
use App\Models\Consommables;

class Categories extends Model
{
    //relation tables
    public function machines(): HasMany
    {
        return $this->hasMany(Machines::class, 'id_categorie');
    }

    public function consommables(): HasMany
    {
        return $this->hasMany(Consommables::class, 'id_categorie');
    }
}
