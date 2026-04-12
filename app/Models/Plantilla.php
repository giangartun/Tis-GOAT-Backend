<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Plantilla extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'plantilla';
    protected $primaryKey = 'id_plantilla';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'url_vista',
    ];

    public function uniqueIds(): array
    {
        return ['id_plantilla'];
    }

    public function portafolios()
    {
        // Relación 1:N (Una plantilla -> Muchos portafolios)
        return $this->hasMany(Portafolio::class, 'id_plantilla');
    }
}