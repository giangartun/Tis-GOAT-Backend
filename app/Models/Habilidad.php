<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Habilidad extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'habilidad';
    protected $primaryKey = 'id_habilidad';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'tipo',
        'nivel',
        'visible'
    ];

    protected $casts = [
        'nivel' => 'integer',
        'visible' => 'boolean',
    ];

    public function uniqueIds(): array
    {
        return ['id_habilidad'];
    }

    public function habilidad_portafolio()
    {
        return $this->hasMany(PortafolioHabilidad::class, 'id_habilidad');
    }
}