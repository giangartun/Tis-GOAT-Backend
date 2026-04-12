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
        'id_habilidad',
        'nombre',
        'tipo',
        'nivel',
        'visible',
        'id_portafolio' // 👈 FALTABA ESTO
    ];

    protected $casts = [
        'nivel' => 'integer',
        'visible' => 'boolean',
    ];

    public function uniqueIds(): array
    {
        return ['id_habilidad'];
    }

    // RELACIÓN: Una habilidad pertenece a un portafolio
    public function portafolio()
    {
        return $this->belongsTo(Portafolio::class, 'id_portafolio');
    }
}