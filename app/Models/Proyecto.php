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
        'url_proyecto',
        'imagen_url',
        'visible',
        'fecha_ini',
        'fecha_fin',
        'creado_en',
        'id_portafolio'
    ];

    protected $casts = [
        'fecha_ini' => 'date',
        'fecha_fin' => 'date',
        'creado_en' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['id_proyecto'];
    }

// 🔹 RELACIONES

    // Un proyecto pertenece a un portafolio específico
    public function portafolio()
    {
        return $this->belongsTo(Portafolio::class, 'id_portafolio');
    }

    // Relación Muchos a Muchos con Tecnología (Usa la tabla intermedia)
    public function tecnologias()
    {
        return $this->belongsToMany(
            Tecnologia::class, 
            'proyecto_tecnologia', // Nombre de la tabla intermedia
            'id_proyecto',         // FK de Proyecto en la intermedia
            'id_tecnologia'        // FK de Tecnologia en la intermedia
        )->withPivot('tipo'); // <--- Acceso al campo extra;
    }

    // Un proyecto puede tener varias evidencias (fotos/capturas)
    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'id_proyecto');
    }
}