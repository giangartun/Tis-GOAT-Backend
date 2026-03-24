<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Proyecto extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'proyecto';
    protected $primaryKey = 'id_proyecto';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'url_repositorio',
        'url_demo',
        'imagen_url',
        'fecha_ini',
        'fecha_fin',
        'visible',
        'fecha_creado',
        'id_portafolio'
    ];

    protected $casts = [
        'fecha_ini' => 'date',
        'fecha_fin' => 'date',
        'fecha_creado' => 'date',
        'visible' => 'boolean',
    ];

    public function uniqueIds(): array
    {
        return ['id_proyecto'];
    }

    public function portafolio()
    {
        return $this->belongsTo(Portafolio::class, 'id_portafolio');
    }

    public function enlaces()
    {
        return $this->hasMany(EnlaceProyecto::class, 'id_proyecto');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'id_proyecto');
    }

    public function proyecto_tecnologias()
    {
        return $this->hasMany(ProyectoTecnologia::class, 'id_proyecto');
    }
}