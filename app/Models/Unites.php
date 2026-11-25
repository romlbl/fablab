<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Consommables;

class Unites extends Model
{
    //relations tables
    public function consommables(): HasMany
    {
        return $this->hasMany(Consommables::class, 'id_unite');
    }
}
