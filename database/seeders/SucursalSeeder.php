<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sucursals')->insert([
            [
                'nombre' => 'Sucursal Centro Torreón',
                'direccion' => 'Av. Juárez 123, Centro',
                'telefono' => '8711234567',
                'ciudad' => 'Torreon',
            ],
        ]);
    }
}
