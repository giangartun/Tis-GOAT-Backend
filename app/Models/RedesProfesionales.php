<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class RedesProfesionales extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'redes_profesionales';
    protected $primaryKey = 'id_redes_prof';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'nombre_red',
        'url_red',
        'id_usuario',
        'visible'
    ];

    public function uniqueIds(): array
    {
        return ['id_redes_prof'];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}