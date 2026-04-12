<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Model
{
     use HasFactory, HasUlids, HasApiTokens;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'email', //esta asi en el modelo
        'contrasena',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'biografia',
        'foto', // igual asi en el modelo
        'fecha'
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['id_usuario'];
    }

    // Relaciones basadas en el modelo ER
    public function registrosActividad()
    {
        return $this->hasMany(RegistroActividad::class, 'id_usuario');
    }

    public function redesProfesionales()
    {
        return $this->hasMany(RedesProfesionales::class, 'id_usuario');
    }

    public function portafolio()
    {
        return $this->hasOne(Portafolio::class, 'id_usuario');
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }
}