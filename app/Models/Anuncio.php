<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Anuncio extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'anuncio';
    protected $primaryKey = 'id_anuncio';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'foto_url',
        'url_redireccion',
        'creado_en',
    ];

    protected $casts = [
        'creado_en' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['id_anuncio'];
    }
}