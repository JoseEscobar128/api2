<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Pedido;
use App\Models\Mesa;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        // Esto es un ejemplo, puedes adaptar la lógica a tus necesidades
        $totalIngresos = Pedido::where('estado_pedido_id', 3)->sum('total_pedido'); // Asume que 3 es 'Entregado'
        $pedidosHoy = Pedido::whereDate('created_at', today())->count();
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_ingresos' => $totalIngresos,
                'pedidos_hoy' => $pedidosHoy,
                'mesas_ocupadas' => $mesasOcupadas,
                // Puedes agregar más datos aquí
            ]
        ]);
    }

}