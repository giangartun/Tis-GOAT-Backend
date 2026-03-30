<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ProyectoTecnologia extends Pivot
{
    use HasUlids;

    protected $table = 'proyecto_tecnologia';
    protected $primaryKey = 'id_proyecto_tecnologia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_tecnologia',
        'tipo'
    ];

    public function uniqueIds(): array
    {
        return ['id_proyecto_tecnologia'];
    }

}