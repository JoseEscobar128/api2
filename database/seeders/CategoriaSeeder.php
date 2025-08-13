<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Entradas',
                'descripcion' => 'Platos ligeros para abrir el apetito como nachos, quesadillas y botanas.'
            ],
            [
                'nombre' => 'Sopas',
                'descripcion' => 'Variedad de sopas calientes como crema de champiñones y caldo de pollo.'
            ],
            [
                'nombre' => 'Ensaladas',
                'descripcion' => 'Ensaladas frescas y saludables con ingredientes variados.'
            ],
            [
                'nombre' => 'Carnes',
                'descripcion' => 'Cortes de carne y platillos preparados con res, cerdo o pollo.'
            ],
            [
                'nombre' => 'Mariscos',
                'descripcion' => 'Delicias del mar como camarones, pulpo y filetes de pescado.'
            ],
            [
                'nombre' => 'Postres',
                'descripcion' => 'Dulces para cerrar con broche de oro: pasteles, helados, flanes y más.'
            ],
            [
                'nombre' => 'Bebidas',
                'descripcion' => 'Refrescos, jugos, aguas frescas y bebidas preparadas.'
            ],
            [
                'nombre' => 'Desayunos',
                'descripcion' => 'Platos matutinos como chilaquiles, huevos al gusto y hot cakes.'
            ],
            [
                'nombre' => 'Comida rápida',
                'descripcion' => 'Hamburguesas, papas fritas, alitas y más platillos rápidos.'
            ],
            [
                'nombre' => 'Especialidades de la casa',
                'descripcion' => 'Platillos únicos del restaurante con recetas propias.'
            ],
        ];

        DB::table('categorias')->insert($categorias);
    }
}
