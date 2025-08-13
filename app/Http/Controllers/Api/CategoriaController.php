<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Listar categorías con filtro opcional por nombre.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Categoria::query();

            if ($request->filled('nombre')) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            }

            $categorias = $query->get();

            return response()->json([
                'code' => 'CAT-001',
                'status' => 'success',
                'message' => 'Listado de categorías obtenido correctamente.',
                'data' => $categorias
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener categorías: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-520',
                'status' => 'error',
                'message' => 'Error al obtener categorías.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva categoría.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:150',
                'descripcion' => 'nullable|string|max:255'
            ]);

            $categoria = Categoria::create($data);

            return response()->json([
                'code' => 'CAT-002',
                'status' => 'success',
                'message' => 'Categoría creada exitosamente.',
                'data' => $categoria
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'VAL-020',
                'status' => 'error',
                'message' => 'Datos inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            Log::error('Error al crear categoría: ' . $e->getMessage());
            return response()->json([
                'code' => 'SRV-521',
                'status' => 'error',
                'message' => 'Error al crear categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver una categoría específica por su ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);

            return response()->json([
                'code' => 'CAT-003',
                'status' => 'success',
                'message' => 'Categoría consultada correctamente.',
                'data' => $categoria
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'CAT-404',
                'status' => 'error',
                'message' => 'Categoría no encontrada.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al consultar categoría: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-522',
                'status' => 'error',
                'message' => 'Error al consultar categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una categoría existente por su ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'nombre' => 'sometimes|required|string|max:150',
                'descripcion' => 'nullable|string|max:255'
            ]);

            $categoria = Categoria::findOrFail($id);
            $categoria->update($data);

            return response()->json([
                'code' => 'CAT-004',
                'status' => 'success',
                'message' => 'Categoría actualizada correctamente.',
                'data' => $categoria
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'CAT-404',
                'status' => 'error',
                'message' => 'Categoría no encontrada.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 'VAL-021',
                'status' => 'error',
                'message' => 'Datos inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar categoría: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-523',
                'status' => 'error',
                'message' => 'Error al actualizar categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar (soft delete) una categoría por su ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'code' => 'CAT-005',
                'status' => 'success',
                'message' => 'Categoría eliminada correctamente.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 'CAT-404',
                'status' => 'error',
                'message' => 'Categoría no encontrada.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría: ' . $e->getMessage());

            return response()->json([
                'code' => 'SRV-524',
                'status' => 'error',
                'message' => 'Error al eliminar categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
