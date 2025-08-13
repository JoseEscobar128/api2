<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Sucursal;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class SucursalController extends Controller
{
    /**
     * Listar sucursales con filtros opcionales.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Sucursal::query();

            if ($request->filled('nombre')) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            }

            if ($request->filled('ciudad')) {
                $query->where('ciudad', $request->ciudad);
            }

            $sucursales = $query->get();

            return response()->json([
                'code' => 'SUC-001',
                'status' => 'success',
                'message' => 'Listado de sucursales obtenido correctamente.',
                'data' => $sucursales
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener sucursales: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-510',
                'status' => 'error',
                'message' => 'Error al obtener sucursales.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva sucursal o restaurar si ya existía.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:150',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:100',
                'telefono' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{7,20}$/']

            ]);

            // Restaurar si ya existía eliminada
            $sucursalEliminada = Sucursal::withTrashed()
                ->where('nombre', $data['nombre'])
                ->where('ciudad', $data['ciudad'])
                ->first();

            if ($sucursalEliminada && $sucursalEliminada->trashed()) {
                $sucursalEliminada->restore();
                $sucursalEliminada->update($data);
                $sucursal = $sucursalEliminada;
            } else {
                $sucursal = Sucursal::create($data);
            }

            return response()->json([
                'code' => 'SUC-002',
                'status' => 'success',
                'message' => 'Sucursal creada exitosamente.',
                'data' => $sucursal
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'VAL-010',
                'status' => 'error',
                'message' => 'Datos inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            Log::error('Error de base de datos al crear sucursal: ' . $e->getMessage());
            return response()->json([
                'code' => 'SRV-511',
                'status' => 'error',
                'message' => 'Error al crear sucursal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalles de una sucursal.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $sucursal = Sucursal::findOrFail($id);

            return response()->json([
                'code' => 'SUC-003',
                'status' => 'success',
                'message' => 'Sucursal consultada correctamente.',
                'data' => $sucursal
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'SUC-404',
                'status' => 'error',
                'message' => 'Sucursal no encontrada.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al consultar sucursal: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-512',
                'status' => 'error',
                'message' => 'Error al consultar sucursal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una sucursal existente.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'nombre' => 'sometimes|string|max:150',
                'direccion' => 'sometimes|string|max:255',
                'ciudad' => 'sometimes|string|max:100',
                'telefono' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{7,20}$/']

            ]);

            $sucursal = Sucursal::findOrFail($id);
            $sucursal->update($data);

            return response()->json([
                'code' => 'SUC-004',
                'status' => 'success',
                'message' => 'Sucursal actualizada correctamente.',
                'data' => $sucursal
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'SUC-404',
                'status' => 'error',
                'message' => 'Sucursal no encontrada.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'VAL-011',
                'status' => 'error',
                'message' => 'Datos inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar sucursal: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-513',
                'status' => 'error',
                'message' => 'Error al actualizar sucursal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar lógicamente una sucursal (Soft Delete).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $sucursal = Sucursal::findOrFail($id);
            $sucursal->delete();

            return response()->json([
                'code' => 'SUC-005',
                'status' => 'success',
                'message' => 'Sucursal eliminada correctamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'SUC-404',
                'status' => 'error',
                'message' => 'Sucursal no encontrada.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar sucursal: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-514',
                'status' => 'error',
                'message' => 'Error al eliminar sucursal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
