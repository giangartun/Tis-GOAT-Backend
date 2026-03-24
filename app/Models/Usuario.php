<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Usuario extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'correo',
        'contrasena',
        'nombre',
        'profesion',
        'biografia',
        'foto_url',
        'activo',
        'fecha_creado',
        'fecha_actualizacion'
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_creado' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['id_usuario'];
    }

    public function registrosActividad()
    {
        return $this->hasMany(RegistroActividad::class, 'id_usuario');
    }

    public function redesProfesionales()
    {
        return $this->hasMany(RedesProfesionales::class, 'id_usuario');
    }

    public function portafolios()
    {
        return $this->hasMany(Portafolio::class, 'id_usuario');
    }
}