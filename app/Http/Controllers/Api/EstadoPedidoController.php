<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\EstadoPedido;
use Exception;

class EstadoPedidoController extends Controller
{
    /**
     * Listar todos los estados de pedido.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $estados = EstadoPedido::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Listado de estados de pedido obtenido correctamente.',
                'data' => $estados
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener los estados de pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}