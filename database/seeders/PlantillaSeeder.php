<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plantilla;
use Illuminate\Support\Str;

class PlantillaSeeder extends Seeder
{
    public function run(): void
    {
        $plantillas = [
            [
                'nombre' => 'Bento',
                'descripcion' => 'Diseño basado en rejillas modernas (estilo Apple), ideal para mostrar mucha información visual de forma organizada.',
                'url_vista' => 'https://res.cloudinary.com/demo/image/upload/v1/samples/bento-preview.png',
            ],
            [
                'nombre' => 'Sidebar',
                'descripcion' => 'Navegación lateral fija con un área de contenido amplia. Clásico para portafolios profesionales y currículos.',
                'url_vista' => 'https://res.cloudinary.com/demo/image/upload/v1/samples/sidebar-preview.png',
            ],
            [
                'nombre' => 'Editorial',
                'descripcion' => 'Inspirado en revistas digitales, con tipografías grandes y un enfoque muy fuerte en la lectura y la narrativa.',
                'url_vista' => 'https://res.cloudinary.com/demo/image/upload/v1/samples/editorial-preview.png',
            ],
        ];

        foreach ($plantillas as $p) {
            // Usamos updateOrCreate para no duplicar si se vuelve a correr el seeder
            Plantilla::updateOrCreate(
                ['nombre' => $p['nombre']], // Si el nombre ya existe, lo actualiza. Si no, lo crea.
                [
                    'id_plantilla' => (string) Str::ulid(),
                    'descripcion' => $p['descripcion'],
                    'url_vista' => $p['url_vista'],
                ]
            );
        }
    }
}