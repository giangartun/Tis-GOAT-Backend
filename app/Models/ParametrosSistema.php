<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ParametrosSistema extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'parametro_sistema';
    protected $primaryKey = 'id_parametro';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'tipo_parametro',
        'contenido_parametro'
    ];

    public function uniqueIds(): array
    {
        return ['id_parametro'];
    }
}