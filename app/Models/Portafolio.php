<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portafolio extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_portafolio';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_portafolio',
        'id_usuario',
        'id_plantilla',
        'enlace_pagi_web',
        'creado_en',
        'fecha_act',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function habilidades()
    {
        return $this->hasMany(Habilidad::class, 'id_portafolio', 'id_portafolio');
    }

    public function experienciasLaborales()
    {
        return $this->hasMany(ExperienciaLaboral::class, 'id_portafolio', 'id_portafolio');
    }

    public function experienciasAcademicas()
    {
        return $this->hasMany(ExperienciaAcademica::class, 'id_portafolio', 'id_portafolio');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_portafolio', 'id_portafolio');
    }
}