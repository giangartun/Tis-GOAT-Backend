<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ProyectoTecnologia extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'proyecto_tecnologia';
    protected $primaryKey = 'id_proyecto_tecnologia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_proyecto',
        'id_tecnologia'
    ];

    public function uniqueIds(): array
    {
        return ['id_proyecto_tecnologia'];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function tecnologias()
    {
        return $this->belongsTo(Tecnologia::class, 'id_tecnologia');
    }
}