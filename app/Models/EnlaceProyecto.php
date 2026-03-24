<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class EnlaceProyecto extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'enlace_proyecto';
    protected $primaryKey = 'id_enlace';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'etiqueta',
        'url',
        'id_proyecto'
    ];

    public function uniqueIds(): array
    {
        return ['id_enlace'];
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }
}