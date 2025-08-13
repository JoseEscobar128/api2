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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('mesa_id')
                ->nullable()
                ->constrained('mesas')
                ->nullOnDelete()
                ->after('estado_pedido_id'); // Lo coloca después de estado_pedido_id
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['mesa_id']);
            $table->dropColumn('mesa_id');
        });
    }
};
