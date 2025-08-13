<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mesa;

class MesasSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            Mesa::create([
                'nombre' => 'Mesa ' . $i,
                'capacidad' => rand(2, 6),
                'ocupada' => false
            ]);
        }
    }
}
