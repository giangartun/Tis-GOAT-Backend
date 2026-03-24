<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PortafolioHabilidad extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'portafolio_habilidad';
    protected $primaryKey = 'id_portafolio_habilidad';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_portafolio',
        'id_habilidad'
    ];

    public function uniqueIds(): array
    {
        return ['id_portafolio_habilidad'];
    }

    public function habilidad()
    {
        return $this->belongsTo(Habilidad::class, 'id_habilidad');
    }

    public function portafolio()
    {
        return $this->belongsTo(Portafolio::class, 'id_portafolio');
    }
}