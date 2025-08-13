<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoPedido;

class EstadoPedidoSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiamos la tabla para evitar duplicados al re-ejecutar el seeder
        EstadoPedido::query()->delete();

        $estados = [
            ['id' => 1, 'nombre' => 'En preparación'],
            ['id' => 2, 'nombre' => 'Listo para entregar'], // Para mesas ("En tienda")
            ['id' => 3, 'nombre' => 'Listo para recoger'],  // Para "Para llevar"
            ['id' => 4, 'nombre' => 'Entregado'],           // Cuando el mesero ya lo llevó a la mesa
            ['id' => 5, 'nombre' => 'Completado'],          // Cuando el cliente pagó
            ['id' => 6, 'nombre' => 'Cancelado'],
        ];

        foreach ($estados as $estado) {
            EstadoPedido::create($estado);
        }
    }
}