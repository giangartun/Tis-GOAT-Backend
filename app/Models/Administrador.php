<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Administrador extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'administrador';
    protected $primaryKey = 'id_administrador';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'correo',
        'contraseña',
        'nombre',
    ];

    //Generamos un UIID  para los usuarios
    public function uniqueIds(): array
    {
        return ['id_administrador'];
    }
}