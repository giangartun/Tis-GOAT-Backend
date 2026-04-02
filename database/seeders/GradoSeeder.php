<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GradoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('grado')->insert([
            [
                'id_grado' => Str::ulid(),
                'nombre_grado' => 'Técnico'
            ],
            [
                'id_grado' => Str::ulid(),
                'nombre_grado' => 'Licenciatura'
            ],
            [
                'id_grado' => Str::ulid(),
                'nombre_grado' => 'Ingeniería'
            ],
            [
                'id_grado' => Str::ulid(),
                'nombre_grado' => 'Maestría'
            ],
            [
                'id_grado' => Str::ulid(),
                'nombre_grado' => 'Doctorado'
            ]
        ]);
    }
}