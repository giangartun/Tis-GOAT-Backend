<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Tecnologia extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'tecnologia';
    protected $primaryKey = 'id_tecnologia';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'categoria'
    ];

    public function uniqueIds(): array
    {
        return ['id_tecnologia'];
    }
/**
     * Una tecnología puede estar presente en muchos proyectos.
     */
    public function proyectos()
    {
        return $this->belongsToMany(
            Proyecto::class, 
            'proyecto_tecnologia', 
            'id_tecnologia', 
            'id_proyecto'
        )->withPivot('tipo');
    }
}