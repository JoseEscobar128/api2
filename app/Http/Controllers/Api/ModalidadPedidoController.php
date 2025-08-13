<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\ModalidadPedido;
use Exception;

class ModalidadPedidoController extends Controller
{
    /**
     * Listar todas las modalidades de pedido.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $modalidades = ModalidadPedido::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Listado de modalidades de pedido obtenido correctamente.',
                'data' => $modalidades
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener las modalidades de pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}