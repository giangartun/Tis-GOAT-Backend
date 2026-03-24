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
        'titulo',
        'descripcion',
        'publicado',
        'slug',
        'fecha_creado',
        'fecha_actualizacion',
        'id_usuario'
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'fecha_creado' => 'date',
        'fecha_actualizacion' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['id_portafolio'];
    }

    // 🔹 RELACIONES
    // Un portafolio pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function portafolios_plantilla()
    {
        return $this->hasMany(PlantillaProyecto::class, "id_portafolio"); 
    }

    // Un portafolio tiene muchas experiencias laborales
    public function experienciasLaborales()
    {
        return $this->hasMany(ExperienciaLaboral::class, 'id_portafolio');
    }

    // Un portafolio tiene muchas experiencias académicas
    public function experienciasAcademicas()
    {
        return $this->hasMany(ExperienciaAcademica::class, 'id_portafolio');
    }

    // Muchos a muchos con habilidades
    public function proyecto_habilidad()
    {
        return $this->hasMany(PortafolioHabilidad::class, 'id_portafolio');
    }

    // Un portafolio tiene muchos proyectos (según tu diagrama)
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_portafolio');
    }
}