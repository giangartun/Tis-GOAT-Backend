<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Evidencia extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'evidencia';
    protected $primaryKey = 'id_evidencia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'url_evidencia',
        'nombre_archivo',
        'foto_url',
        'tamano_bytes',
        'fecha_subida',
        'id_proyecto',
        'id_experiencia_academica',
        'id_experiencia_laboral'
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
        'fecha_subida' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['id_evidencia'];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    // --- NUEVAS RELACIONES ---

    public function experienciaAcademica()
    {
        return $this->belongsTo(ExperienciaAcademica::class, 'id_experiencia_academica');
    }

    public function experienciaLaboral()
    {
        return $this->belongsTo(ExperienciaLaboral::class, 'id_experiencia_laboral');
    }
}