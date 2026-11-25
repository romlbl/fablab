<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Categories;
use App\Models\Jobs;

class Machines extends Model
{
    //relation tables
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'id_categorie');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class, 'id_machine');
    }
}
