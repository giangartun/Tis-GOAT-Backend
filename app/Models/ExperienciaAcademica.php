<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ExperienciaAcademica extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'experiencia_academica';
    protected $primaryKey = 'id_experiencia_academica';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'institucion',
        'titulo',
        'descripcion',
        'fecha_ini',
        'fecha_fin',
        'visible',
        'id_portafolio'
    ];

    protected $casts = [
        'fecha_ini' => 'date',
        'fecha_fin' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['id_experiencia_academica'];
    }

    public function portafolio()
    {
        return $this->belongsTo(Portafolio::class, 'id_portafolio');
    }
    public function evidencias()
    {
    // Una experiencia académica puede tener muchas evidencias
    return $this->hasMany(Evidencia::class, 'id_experiencia_academica', 'id_experiencia_academica');
    }
}