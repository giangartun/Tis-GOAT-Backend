<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TecnologiaSeeder extends Seeder
{
    public function run(): void
    {
        $tecnologias = [
            // Lenguajes de programación
            ['nombre' => 'JavaScript',  'categoria' => 'Lenguajes de programación'],
            ['nombre' => 'Python',      'categoria' => 'Lenguajes de programación'],
            ['nombre' => 'Java',        'categoria' => 'Lenguajes de programación'],
            ['nombre' => 'PHP',         'categoria' => 'Lenguajes de programación'],
            ['nombre' => 'TypeScript',  'categoria' => 'Lenguajes de programación'],
            ['nombre' => 'C#',          'categoria' => 'Lenguajes de programación'],

            // Frameworks y librerías
            ['nombre' => 'React',       'categoria' => 'Frameworks y Librerías'],
            ['nombre' => 'Node.js',     'categoria' => 'Frameworks y Librerías'],
            ['nombre' => 'Django',      'categoria' => 'Frameworks y Librerías'],
            ['nombre' => 'Laravel',     'categoria' => 'Frameworks y Librerías'],
            ['nombre' => 'Vue.js',      'categoria' => 'Frameworks y Librerías'],
            ['nombre' => 'Spring Boot', 'categoria' => 'Frameworks y Librerías'],

            // Base de datos
            ['nombre' => 'MySQL',       'categoria' => 'Base de Datos'],
            ['nombre' => 'PostgreSQL',  'categoria' => 'Base de Datos'],
            ['nombre' => 'MongoDB',     'categoria' => 'Base de Datos'],
            ['nombre' => 'SQLite',      'categoria' => 'Base de Datos'],

            // Herramientas y tecnologías
            ['nombre' => 'Git',         'categoria' => 'Herramientas y Tecnologías'],
            ['nombre' => 'Docker',      'categoria' => 'Herramientas y Tecnologías'],
            ['nombre' => 'Linux',       'categoria' => 'Herramientas y Tecnologías'],
            ['nombre' => 'Postman',     'categoria' => 'Herramientas y Tecnologías'],
            ['nombre' => 'GitHub',      'categoria' => 'Herramientas y Tecnologías'],
        ];

        foreach ($tecnologias as $tecnologia) {
            DB::table('tecnologia')->insert([
                'id_tecnologia' => Str::ulid(),
                'nombre'        => $tecnologia['nombre'],
                'categoria'     => $tecnologia['categoria'],
            ]);
        }
    }
}