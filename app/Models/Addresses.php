<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Addresses extends Model
{
    public $timestamps = false;

    protected $table = 'public.addresses';

    protected $fillable = [
        'zip_code',
        'short_name',
        'type',
        'location_id',
        'neighborhood_id'
    ];

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(Locations::class, 'location_id', 'id');
    }

    public function fullLandmark(): Attribute
    {
        return Attribute::make(
            fn() => "$this->tipo_logradouro $this->logradouro"
        );
    }

    public function scopeSearch($query, string $andPart, string $orPart)
    {
        return $query
            ->whereRaw("searchable_text_ts @@ to_tsquery('pg_catalog.portuguese', unaccent(?))", $andPart)
            ->orderByRaw("ts_rank(searchable_text_ts, to_tsquery('pg_catalog.portuguese', unaccent(?))) DESC NULLS LAST", $andPart)
            ->orderByRaw("ts_rank(searchable_text_ts, to_tsquery('pg_catalog.portuguese', unaccent(?))) DESC", $orPart)
            ->paginate();
    }
}

