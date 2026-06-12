<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class RegistroActividad extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'registro_actividad';
    protected $primaryKey = 'id_registro';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'evento',
        'fecha_hr',
        'contexto',
    ];

    protected $casts = [
        'fecha_hr' => 'datetime',
        'contexto' => 'array',
    ];

    public function uniqueIds(): array
    {
        return ['id_registro'];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}