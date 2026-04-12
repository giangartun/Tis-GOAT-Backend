<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProyectoStoreRequest extends FormRequest
{
    // 1. Cambiar a true para permitir que se use esta validación
    public function authorize(): bool
    {
        return true; 
    }

    // 2. Aquí definimos las reglas de tu HU
    public function rules(): array
    {
        return [
            'id_portafolio' => 'required|exists:portafolio,id_portafolio',
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'url_proyecto'  => 'nullable|url',
            'imagen_url'    => 'nullable|string', // Por ahora lo manejamos como string
            'fecha_ini'     => 'required|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_ini',
            'tecnologias'   => 'nullable|array', // Los IDs que vienen del Seeder
        ];
    }
}