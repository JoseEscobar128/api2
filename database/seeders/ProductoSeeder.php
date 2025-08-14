<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'categoria_id' => 1, // Entradas
                'sucursal_id' => 1,
                'nombre' => 'Nachos con queso',
                'descripcion' => 'Totopos crujientes bañados en queso cheddar derretido.',
                'precio' => 89.00,
                'imagen_principal' => json_encode(['nachos.jpg']),
            ],
            [
                'categoria_id' => 2, // Sopas
                'sucursal_id' => 1,
                'nombre' => 'Caldo de pollo',
                'descripcion' => 'Caldo casero con pollo, verduras y arroz.',
                'precio' => 75.00,
                'imagen_principal' => json_encode(['caldo_pollo.jpg']),
            ],
            [
                'categoria_id' => 3, // Ensaladas
                'sucursal_id' => 1,
                'nombre' => 'Ensalada César',
                'descripcion' => 'Lechuga fresca, aderezo césar, crutones y queso parmesano.',
                'precio' => 95.00,
                'imagen_principal' => json_encode(['cesar.jpg']),
            ],
            [
                'categoria_id' => 4, // Carnes
                'sucursal_id' => 1,
                'nombre' => 'Arrachera con papas',
                'descripcion' => 'Corte de arrachera asada acompañada de papas y ensalada.',
                'precio' => 220.00,
                'imagen_principal' => json_encode(['arrachera.jpg']),
            ],
            [
                'categoria_id' => 5, // Mariscos
                'sucursal_id' => 1,
                'nombre' => 'Camarones al ajillo',
                'descripcion' => 'Camarones salteados en mantequilla y ajo con arroz.',
                'precio' => 185.00,
                'imagen_principal' => json_encode(['camarones.jpg']),
            ],
            [
                'categoria_id' => 6, // Postres
                'sucursal_id' => 1,
                'nombre' => 'Pastel de chocolate',
                'descripcion' => 'Rebanada de pastel húmedo con betún de chocolate.',
                'precio' => 55.00,
                'imagen_principal' => json_encode(['pastel.jpg']),
            ],
            [
                'categoria_id' => 7, // Bebidas
                'sucursal_id' => 1,
                'nombre' => 'Agua de jamaica',
                'descripcion' => 'Agua fresca natural elaborada con flor de jamaica.',
                'precio' => 30.00,
                'imagen_principal' => json_encode(['jamaica.jpg']),
            ],
            [
                'categoria_id' => 8, // Desayunos
                'sucursal_id' => 1,
                'nombre' => 'Huevos al gusto',
                'descripcion' => 'Dos huevos preparados como desees, con frijoles y tortillas.',
                'precio' => 70.00,
                'imagen_principal' => json_encode(['huevos.jpg']),
            ],
            [
                'categoria_id' => 9, // Comida rápida
                'sucursal_id' => 1,
                'nombre' => 'Hamburguesa clásica',
                'descripcion' => 'Hamburguesa con carne 100% res, lechuga, jitomate y papas.',
                'precio' => 110.00,
                'imagen_principal' => json_encode(['hamburguesa.jpg']),
            ],
            [
                'categoria_id' => 10, // Especialidades
                'sucursal_id' => 1,
                'nombre' => 'Fajitas mixtas',
                'descripcion' => 'Platillo especial con pollo, res y camarón acompañados de guarniciones.',
                'precio' => 240.00,
                'imagen_principal' => json_encode(['fajitas.jpg']),
            ],
            [
                'categoria_id' => 8, // Especialidades
                'sucursal_id' => 1,
                'nombre' => 'Fajitas BBQ',
                'descripcion' => 'Platillo especial con pollo cubierto con BBQ',
                'precio' => 250.00,
                'imagen_principal' => json_encode(['fajitasBBQ.jpg']),
            ],
        ];

        DB::table('productos')->insert($productos);
    }
}
