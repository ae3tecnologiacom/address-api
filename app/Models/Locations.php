<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    public $timestamps = false;

    protected $table = 'public.locations';

    protected $fillable = [
        'state_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(States::class, 'state_id', 'id');
    }

    public function scopeByUf($query, string $uf)
    {
        return $query->whereHas('state', function ($q) use ($uf) {
            $q->where('acronym', $uf);
        });
    }


    public function scopeSortBy($query, ?string $column = 'name')
    {
        return $query->orderBy($column);
    }
}
