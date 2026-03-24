<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ExperienciaLaboral extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'experiencia_laboral';
    protected $primaryKey = 'id_experiencia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'empresa',
        'cargo',
        'descripcion',
        'fecha_ini',
        'fecha_fin',
        'actual',
        'visible',
        'id_portafolio'
    ];

    protected $casts = [
        'fecha_ini' => 'date',
        'fecha_fin' => 'date',
        'actual' => 'boolean',
        'visible' => 'boolean',
    ];

    public function uniqueIds(): array
    {
        return ['id_experiencia'];
    }

    public function portafolio()
    {
        return $this->belongsTo(Portafolio::class, 'id_portafolio');
    }
}