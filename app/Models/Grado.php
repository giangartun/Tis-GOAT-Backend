<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Grado extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'grado';
    protected $primaryKey = 'id_grado';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nombre_grado'
    ];

    public function uniqueIds(): array
    {
        return ['id_grado'];
    }


    public function experienciaAcademicas()
    {
        return $this->belongsToMany(
            ExperienciaAcademica::class,
            'experiencia_academica_grado',
            'id_grado',
            'id_experiencia_academica'
        );
    }
}