<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadesPedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modalidades_pedido')->insert([
            ['id' => 1, 'nombre' => 'En tienda', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Para llevar', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Para recoger', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
