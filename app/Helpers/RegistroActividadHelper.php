<?php

namespace App\Helpers;

use App\Models\RegistroActividad;

class RegistroActividadHelper
{
    public static function registrar(
        string $id_usuario,
        string $evento,
        array  $contexto = []
    ): void
    {
        RegistroActividad::create([
            'id_usuario' => $id_usuario,
            'evento'     => $evento,
            'fecha_hr'   => now(),
            'contexto'   => empty($contexto) ? null : $contexto,
        ]);
    }
}