<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Portafolio extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'portafolio';
    protected $primaryKey = 'id_portafolio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_plantilla',
        'enlace_pagi_web',
        'visible',
        'creado_en',
        'fecha_act',        
    ];

    protected $casts = [
        'creado_en' => 'datetime',
        'fecha_act' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['id_portafolio'];
    }

    // 🔹 RELACIONES

    // Un portafolio pertenece a un usuario (1:1 definido en Usuario)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // El portafolio usa una plantilla
    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class, 'id_plantilla');
    }

    // Un portafolio tiene muchos proyectos
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_portafolio');
    }

    // Un portafolio incluye muchas habilidades
    public function habilidades()
    {
        return $this->hasMany(Habilidad::class, 'id_portafolio');
    }

    // Un portafolio publica muchas experiencias laborales
    public function experienciasLaborales()
    {
        return $this->hasMany(ExperienciaLaboral::class, 'id_portafolio');
    }

    // Un portafolio publica muchas experiencias académicas
    public function experienciasAcademicas()
    {
        return $this->hasMany(ExperienciaAcademica::class, 'id_portafolio');
    }
}