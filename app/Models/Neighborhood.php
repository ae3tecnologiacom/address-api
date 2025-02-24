<?php

namespace App\Models;

use App\Models\Zipcode\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Neighborhood extends Model
{
    public $timestamps = false;

    protected $table = 'public.neighborhoods';

    public function city(): BelongsTo
    {
        return $this->belongsTo(Locations::class, 'location_id', 'id');
    }

    public function locations()
    {
        return $this->belongsTo(Locations::class, 'location_id', 'id');
    }

    public function scopeByCity($query, int|string $city_id)
    {
        return $query->whereHas('locations', function ($q) use ($city_id) {
            $q->where('state_id', $city_id);
        });
    }

    public function scopeSortBy($query, string $column = 'name')
    {
        return $query->orderBy($column);
    }

    public function scopeByName($query, string $name)
    {
        return $query->where('full_name', 'ilike', "%{$name}%");
    }
}
