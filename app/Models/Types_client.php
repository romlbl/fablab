<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Clients;

class Types_client extends Model
{
    //relations table
    public function clients(): HasMany
    {
        return $this->hasMany(Clients::class, 'id_type_client');
    }
}
