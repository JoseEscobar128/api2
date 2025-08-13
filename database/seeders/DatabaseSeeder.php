<?php

namespace Database\Seeders;

use App\Models\EstadoPedido;
use Database\Seeders\SucursalSeeder;
use Database\Seeders\CategoriaSeeder;
use Database\Seeders\ProductoSeeder;
use Database\Seeders\ModalidadesPedidoSeeder;
use Database\Seeders\ModalidadPedidoSeeder;
use App\Models\Sucursal;



use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\Node\Expr\AssignOp\Mod;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SucursalSeeder::class,
            CategoriaSeeder::class,
            ProductoSeeder::class,
            ModalidadesPedidoSeeder::class,
            EstadoPedidoSeeder::class,
            MesasSeeder::class,
        ]);
    }
}
