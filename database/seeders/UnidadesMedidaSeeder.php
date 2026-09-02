<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadesMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Unidad', 'abreviatura' => 'und'],
            ['nombre' => 'Caja', 'abreviatura' => 'caja'],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg'],
            ['nombre' => 'Litro', 'abreviatura' => 'l'],
            ['nombre' => 'Metro', 'abreviatura' => 'm'],
            ['nombre' => 'Paquete', 'abreviatura' => 'pqte'],
            ['nombre' => 'Docena', 'abreviatura' => 'doc'],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::firstOrCreate(['nombre' => $unidad['nombre']], $unidad);
        }
    }
}