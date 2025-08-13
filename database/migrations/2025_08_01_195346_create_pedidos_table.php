<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursals');
            
            // Clientes y usuarios son externos, así que solo los guardamos como ID
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('usuario_creo_id')->nullable();

            $table->foreignId('modalidad_pedido_id')->constrained('modalidades_pedido');
            $table->foreignId('estado_pedido_id')->constrained('estado_pedidos');

            $table->decimal('total_pedido', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
