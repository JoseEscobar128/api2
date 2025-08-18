<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\Asistencia;
use Illuminate\Support\Facades\Http;

class AsistenciaController extends Controller
{
    /**
     * Lista los registros de asistencia, aplicando filtros y paginación.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
{
    try {
        $query = array_filter([
            'search' => $request->search,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        $response = Http::orderApi()->get('/asistencia', $query);

        if ($response->successful()) {
            $data = $response->json();

            // Convertir cadenas a UTF-8
            $data = array_map(function($value) {
                return is_string($value) ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
            }, $data['data'] ?? []);

            return response()->json($data, 200);
        }

        return response()->json([], 200);

    } catch (\Exception $e) {
        Log::error('Error al listar asistencias: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'code' => 'DEBUG',
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}


    /**
     * Sincroniza registros de asistencia recibidos desde la aplicación de escritorio.
     * (Este método no se ha modificado)
     */
    public function sincronizarRegistros(Request $request)
    {
        try {
            $data = $request->validate([
                '*.EmpleadoId'   => 'required|integer|exists:empleados,id',
                '*.FechaHora'    => 'required|date',
                '*.TipoRegistro' => 'required|string|in:Entrada,Salida',
            ]);

            if (empty($data)) {
                return response()->json(['code' => 'ASI-204', 'status' => 'success', 'message' => 'No se recibieron registros para sincronizar.', 'data' => []], 200);
            }

            DB::beginTransaction();

            foreach ($data as $registro) {
                Asistencia::create([
                    'empleado_id'   => $registro['EmpleadoId'],
                    'fecha_hora'    => $registro['FechaHora'],
                    'tipo_registro' => $registro['TipoRegistro'],
                ]);
            }

            DB::commit();

            return response()->json(['code' => 'ASI-001', 'status' => 'success', 'message' => 'Registros de asistencia sincronizados exitosamente.', 'data' => ['cantidad' => count($data)]], 200);

        } catch (ValidationException $e) {
            return response()->json(['code' => 'VAL-030', 'status' => 'error', 'message' => 'Datos inválidos.', 'errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error de base de datos al sincronizar asistencia: ' . $e->getMessage());
            return response()->json(['code' => 'SRV-530', 'status' => 'error', 'message' => 'Error al guardar registros de asistencia.', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al sincronizar asistencia: ' . $e->getMessage());
            return response()->json(['code' => 'SRV-531', 'status' => 'error', 'message' => 'Error inesperado al sincronizar registros de asistencia.', 'error' => $e->getMessage()], 500);
        }
    }
}
