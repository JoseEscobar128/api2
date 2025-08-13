<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Http\Requests\ProductoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProductoController extends Controller
{
    /**
     * Listar productos.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Producto::query();

            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->categoria_id);
            }

            if ($request->filled('sucursal_id')) {
                $query->where('sucursal_id', $request->sucursal_id);
            }

            if ($request->filled('nombre')) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            }

            $productos = $query->get();
            

            return response()->json([
                'code' => 'PRD-001',
                'status' => 'success',
                'message' => 'Listado de productos obtenido correctamente.',
                'data' => $productos
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 'SRV-500',
                'status' => 'error',
                'message' => 'Error al obtener productos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo producto.
     *
     * @param ProductoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductoRequest $request)
    {
        try {
            $validated = $request->validated();
            $producto = Producto::create($validated);

            return response()->json([
                'code' => 'PRD-002',
                'status' => 'success',
                'message' => 'Producto creado exitosamente.',
                'data' => $producto
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 'SRV-501',
                'status' => 'error',
                'message' => 'Error al crear producto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalle de un producto.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $producto = Producto::findOrFail($id);

            return response()->json([
                'code' => 'PRD-003',
                'status' => 'success',
                'message' => 'Producto consultado correctamente.',
                'data' => $producto
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'PRD-404',
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'code' => 'SRV-502',
                'status' => 'error',
                'message' => 'Error al consultar producto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un producto.
     *
     * @param ProductoRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductoRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $producto = Producto::findOrFail($id);
            $producto->update($validated);

            return response()->json([
                'code' => 'PRD-004',
                'status' => 'success',
                'message' => 'Producto actualizado correctamente.',
                'data' => $producto
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'PRD-404',
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'code' => 'SRV-503',
                'status' => 'error',
                'message' => 'Error al actualizar producto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();

            return response()->json([
                'code' => 'PRD-005',
                'status' => 'success',
                'message' => 'Producto eliminado correctamente.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'PRD-404',
                'status' => 'error',
                'message' => 'Producto no encontrado.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'code' => 'SRV-504',
                'status' => 'error',
                'message' => 'Error al eliminar producto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
