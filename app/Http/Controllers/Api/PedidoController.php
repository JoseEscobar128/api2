<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 100);

            // --- LÓGICA DE FILTROS CORREGIDA Y COMPLETA ---
            $query = Pedido::with(['estadoPedido', 'modalidad', 'items', 'mesa'])
                ->orderBy('created_at', 'desc');

            // Filtro por Búsqueda (ID del pedido)
            if ($request->filled('search')) {
                $query->where('id', 'like', '%' . $request->search . '%');
            }

            // Filtro por Modalidad del Pedido
            if ($request->filled('modalidad_pedido_id')) {
                $query->where('modalidad_pedido_id', $request->modalidad_pedido_id);
            }

            // Filtro por Estado del Pedido
            if ($request->filled('estado_pedido_id')) {
                $query->where('estado_pedido_id', $request->estado_pedido_id);
            }

            // Filtro por Rango de Fechas
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                // Si se proporcionan ambas fechas, busca en el rango.
                $query->whereBetween('created_at', [$request->fecha_inicio . ' 00:00:00', $request->fecha_fin . ' 23:59:59']);
            } elseif ($request->filled('fecha_inicio')) {
                // Si solo se proporciona la fecha de inicio, busca desde esa fecha en adelante.
                $query->where('created_at', '>=', $request->fecha_inicio . ' 00:00:00');
            } elseif ($request->filled('fecha_fin')) {
                // Si solo se proporciona la fecha de fin, busca hasta esa fecha.
                $query->where('created_at', '<=', $request->fecha_fin . ' 23:59:59');
            }
            
            $pedidos = $query->paginate($limit);

            $data = $pedidos->getCollection()->transform(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'estadoPedido' => $pedido->estadoPedido->nombre ?? null,
                    'modalidad' => $pedido->modalidad->nombre ?? null,
                    'totalPedido' => (float) $pedido->total_pedido,
                    'createdAt' => $pedido->created_at->toIso8601String(),
                    'mesaId' => $pedido->mesa_id,
                    'items' => $pedido->items->map(function($item) {
                        return [
                            'producto_id' => $item->producto_id,
                            'cantidad' => $item->cantidad,
                        ];
                    }),
                ];
            });

            return response()->json([
                'code' => 'PED-002',
                'status' => 'success',
                'message' => 'Lista de pedidos obtenida correctamente.',
                'page' => $pedidos->currentPage(),
                'limit' => $pedidos->perPage(),
                'total' => $pedidos->total(),
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al listar pedidos: ' . $e->getMessage());
            return response()->json([
                'code' => 'SRV-532',
                'status' => 'error',
                'message' => 'Error al consultar los pedidos.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

   public function show($id)
    {
        try {
            // Cargamos todas las relaciones necesarias para una respuesta completa
            $pedido = Pedido::with(['items.producto', 'cliente', 'usuarioCreo', 'estadoPedido', 'modalidad', 'mesa'])->findOrFail($id);

            // Preparamos los nombres para facilitar el uso en el frontend
            $clienteNombre = $pedido->cliente->nombre ?? null;
            $cajeroNombre = $pedido->usuarioCreo->usuario ?? null;
            $estado = $pedido->estadoPedido->nombre ?? 'Desconocido';
            $modalidad = $pedido->modalidad->nombre ?? 'Desconocida';

            return response()->json([
                'code' => 'PED-003',
                'status' => 'success',
                'message' => 'Detalle del pedido obtenido correctamente.',
                'data' => [
                    'id' => $pedido->id,
                    'sucursalId' => $pedido->sucursal_id,
                    'clienteId' => $pedido->cliente_id,
                    'clienteNombre' => $clienteNombre,
                    'usuarioCreoId' => $pedido->usuario_creo_id,
                    'cajeroNombre' => $cajeroNombre,
                    'modalidad' => $modalidad,
                    'estadoPedido' => $estado,
                    
                    // --- ¡CORRECCIÓN CLAVE AQUÍ! ---
                    // Añadimos 'mesaId' y 'mesaNombre' al nivel superior para que el frontend los encuentre fácilmente.
                    'mesaId' => $pedido->mesa_id,
                    'mesaNombre' => $pedido->mesa->nombre ?? null,
                    
                    'totalPedido' => round($pedido->total_pedido, 2),
                    'items' => $pedido->items->map(function ($item) {
                        return [
                            'productoId' => $item->producto_id,
                            'nombre' => $item->producto->nombre ?? 'Producto Eliminado',
                            'cantidad' => $item->cantidad,
                            'precioUnitario' => round($item->precio_unitario, 2),
                            'nota' => $item->nota,
                        ];
                    }),
                    'createdAt' => $pedido->created_at->toIso8601String(),
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['code' => 'PED-404', 'status' => 'error', 'message' => 'Pedido no encontrado.'], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener pedido: ' . $e->getMessage());
            return response()->json(['code' => 'SRV-532', 'status' => 'error', 'message' => 'Error al consultar el pedido.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Cuerpo del request recibido: ' . json_encode($request->all()));

            $validatedData = $request->validate([
                'sucursalId' => 'required|exists:sucursals,id',
                'clienteId' => 'nullable|exists:clientes,id',
                'usuarioCreoId' => 'nullable|exists:usuarios,id',
                'modalidadPedidoId' => 'required|exists:modalidades_pedido,id',
                'estadoPedidoId' => 'required|exists:estado_pedidos,id',
                'mesaId' => 'nullable|exists:mesas,id',
                'items' => 'required|array|min:1',
                'items.*.productoId' => 'required|exists:productos,id',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.nota' => 'nullable|string',
            ]);

            if (is_null($request->clienteId) && is_null($request->usuarioCreoId)) {
                return response()->json([
                    'code' => 'PED-002',
                    'status' => 'error',
                    'message' => 'Debe proporcionar clienteId o usuarioCreoId.'
                ], 422);
            }

            if (!is_null($request->clienteId) && !is_null($request->usuarioCreoId)) {
                return response()->json([
                    'code' => 'PED-003',
                    'status' => 'error',
                    'message' => 'No puede proporcionar ambos: clienteId y usuarioCreoId.'
                ], 422);
            }

            // Validar mesa si modalidad es presencial (id=1)
            if ($validatedData['modalidadPedidoId'] == 1 && empty($validatedData['mesaId'])) {
                return response()->json([
                    'code' => 'PED-VALIDATION-ERROR',
                    'status' => 'error',
                    'message' => 'Debe seleccionar una mesa para modalidad presencial.',
                ], 422);
            }

            DB::beginTransaction();

            $pedido = Pedido::create([
                'sucursal_id' => $validatedData['sucursalId'],
                'cliente_id' => $validatedData['clienteId'],
                'usuario_creo_id' => $validatedData['usuarioCreoId'],
                'modalidad_pedido_id' => $validatedData['modalidadPedidoId'],
                'estado_pedido_id' => $validatedData['estadoPedidoId'],
                'mesa_id' => $validatedData['mesaId'] ?? null,
                'total_pedido' => 0,
            ]);

            $totalPedido = 0;

            foreach ($validatedData['items'] as $item) {
                $producto = Producto::findOrFail($item['productoId']);
                $cantidad = $item['cantidad'];
                $precioUnitario = $producto->precio;
                $total = $cantidad * $precioUnitario;

                $pedido->items()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'total' => $total,
                    'nota' => $item['nota'] ?? null,
                ]);

                $totalPedido += $total;
            }

            $pedido->total_pedido = round($totalPedido, 2);
            $pedido->save();

            // Marcar mesa ocupada si modalidad presencial y mesa asignada
            if ($validatedData['modalidadPedidoId'] == 1 && !empty($validatedData['mesaId'])) {
                $mesa = \App\Models\Mesa::find($validatedData['mesaId']);
                if ($mesa && !$mesa->ocupada) {
                    $mesa->ocupada = true;
                    $mesa->save();
                }
            }

            DB::commit();

            return response()->json([
                'code' => 'PED-001',
                'status' => 'success',
                'message' => 'Pedido creado exitosamente.',
                'data' => [
                    'id' => $pedido->id,
                    'estadoPedidoId' => $pedido->estado_pedido_id,
                    'createdAt' => $pedido->created_at->toIso8601String(),
                    'totalPedido' => round($totalPedido, 2)
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'PED-VALIDATION-ERROR',
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear pedido: ' . $e->getMessage());
            DB::rollBack();

            return response()->json([
                'code' => 'PED-500',
                'status' => 'error',
                'message' => 'Ocurrió un error inesperado al crear el pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function actualizarEstado(Request $request, $id)
    {
        try {
            $request->validate([
                'estadoPedidoId' => 'required|exists:estado_pedidos,id',
                'cambiadoPor' => 'required|exists:usuarios,id'
            ]);

            $pedido = Pedido::find($id);

            if (!$pedido) {
                return response()->json([
                    'code' => 'PED-404',
                    'status' => 'error',
                    'message' => 'Pedido no encontrado.'
                ], 404);
            }

            $pedido->estado_pedido_id = $request->estadoPedidoId;
            $pedido->save();

            return response()->json([
                'code' => 'PED-004',
                'status' => 'success',
                'message' => 'Estado del pedido actualizado correctamente.',
                'data' => [
                    'updated' => true,
                    'estadoPedidoId' => $pedido->estado_pedido_id
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'PED-VALIDATION-ERROR',
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado del pedido: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-532',
                'status' => 'error',
                'message' => 'Error al actualizar el estado del pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pedido = Pedido::find($id);
            if (!$pedido) {
                return response()->json(['code' => 'PED-404', 'status' => 'error', 'message' => 'Pedido no encontrado.'], 404);
            }

            $validatedData = $request->validate([
                'estadoPedidoId' => 'sometimes|exists:estado_pedidos,id',
                'modalidadPedidoId' => 'sometimes|exists:modalidades_pedido,id',
                'mesaId' => 'sometimes|nullable|exists:mesas,id',
            ]);

            if (isset($validatedData['estadoPedidoId'])) {
                $pedido->estado_pedido_id = $validatedData['estadoPedidoId'];
            }

            if (isset($validatedData['modalidadPedidoId'])) {
                $pedido->modalidad_pedido_id = $validatedData['modalidadPedidoId'];
            }

            if (array_key_exists('mesaId', $validatedData)) {
                $pedido->mesa_id = $validatedData['mesaId'];
            }

            $pedido->save();

            return response()->json([
                'code' => 'PED-005',
                'status' => 'success',
                'message' => 'Pedido actualizado correctamente.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'PED-VALIDATION-ERROR',
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar pedido: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-532',
                'status' => 'error',
                'message' => 'Error al actualizar el pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pedido = Pedido::find($id);
            if (!$pedido) {
                return response()->json([
                    'code' => 'PED-404',
                    'status' => 'error',
                    'message' => 'Pedido no encontrado.'
                ], 404);
            }

            $pedido->delete();

            return response()->json([
                'code' => 'PED-006',
                'status' => 'success',
                'message' => 'Pedido eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar pedido: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-532',
                'status' => 'error',
                'message' => 'Error al eliminar el pedido.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
