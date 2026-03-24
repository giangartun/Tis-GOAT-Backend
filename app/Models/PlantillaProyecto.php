<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PlantillaProyecto extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'plantilla_proyecto';
    protected $primaryKey = 'id_plantilla_proyecto';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_plantilla',
        'id_portafolio',
    ];

    public function uniqueIds(): array
    {
        return ['id_plantilla_proyecto'];
    }

    public function portafolios()
    {
        return $this->belongsTo(Portafolio::class, "id_portafolio"); 
    }

    public function plantillas()
    {
        return $this->belongsTo(Plantillas::class, "id_plantilla"); 
    }
}