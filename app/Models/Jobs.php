<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Collaborateurs;
use App\Models\Clients;
use App\Models\Projets;
use App\Models\Machines;
use App\Models\Jobs_consommables;
use App\Models\Consommables;

class Jobs extends Model
{

    protected $fillable = [
        'id_categorie', 'status', 'id_operateur', 'id_client', 'id_projet',
        'id_machine', 'description', 'debut', 'fin',
    ];

    public const STATUTS = [
        'en attente' => 'En attente',
        'validé'    => 'Validé',
        'en cours'   => 'En cours',
        'terminé'    => 'Terminé',
        'arrêté'     => 'Arrêté',
    ];

    protected function casts(): array
    {
        return [
            'debut' => 'datetime',
            'fin'   => 'datetime',
        ];
    }

    public function dureeMinutes(): int
    {
        if (! $this->debut || ! $this->fin) {
            return 0;
        }

        return (int) $this->debut->diffInMinutes($this->fin);
    }

    // relations chargées pour les listes
    public function scopeAvecDetails(Builder $q): Builder
    {
        return $q->with([
            'machine', 'categorie', 'client', 'projet', 'operateur',
            'jobConsommables.consommable.uniteObjet',
        ]);
    }

    // client : ses jobs, admin : tous
    public function scopeVisiblePar(Builder $q, ?string $role, ?int $id): Builder
    {
        return match ($role) {
            'client' => $q->where('id_client', $id),
            'admin'  => $q,
            default  => $q->whereRaw('0 = 1'),
        };
    }

    public function scopeFiltrer(Builder $q, array $filtres): Builder
    {
        $colonnes = [
            'projets'    => 'id_projet',
            'machines'   => 'id_machine',
            'conception' => 'id_type_travail',
            'client'     => 'id_client',
            'operateur'  => 'id_operateur',
            'statut'     => 'status',
        ];

        foreach ($colonnes as $cle => $colonne) {
            if (! empty($filtres[$cle])) {
                $q->whereIn($colonne, (array) $filtres[$cle]);
            }
        }

        if (! empty($filtres['categories'])) {
            $q->WhereIn('id_categorie', $filtres['categories']);
        }

        if (! empty($filtres['consommables'])) {
            $q->whereHas('jobConsommables', fn ($c) => $c->whereIn('id_consommable', $filtres['consommables']));
        }

        if (! empty($filtres['dateDebut'])) {
            $q->whereDate('debut', '>=', $filtres['dateDebut']);
        }

        if (! empty($filtres['dateFin'])) {
            $q->whereDate('fin', '<=', $filtres['dateFin']);
        }

        return $q;
    }

    //relations tables

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'id_categorie');
    }

    public function operateur(): BelongsTo
    {
        return $this->belongsTo(
            Collaborateurs::class,
            'id_operateur'
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'id_client');
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projets::class, 'id_projet');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machines::class, 'id_machine');
    }

    public function jobConsommables(): HasMany
    {
        return $this->hasMany(Jobs_consommables::class, 'id_job');
    }

    public function consommables(): BelongsToMany
    {
        return $this->belongsToMany(
            Consommables::class,
            'jobs_consommables',
            'id_job',
            'id_consommable'
        )->withPivot(['id', 'quantite', 'cout', 'cout_tot']);
    }

}
