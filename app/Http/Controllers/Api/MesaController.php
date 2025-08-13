<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mesa;

class MesaController extends Controller
{
    // Listar todas las mesas (o solo libres, según necesidad)
    public function index(Request $request)
    {
        // Si quieres solo mesas libres:
        if ($request->query('solo_libres') == 'true') {
            $mesas = Mesa::where('ocupada', false)->get();
        } else {
            $mesas = Mesa::all();
        }

        return response()->json([
            'status' => 'success',
            'data' => $mesas,
        ]);
    }

    // Mostrar mesa específica
    public function show($id)
    {
        $mesa = Mesa::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $mesa,
        ]);
    }

    // Actualizar estado de ocupada o cualquier otro dato de mesa
    public function update(Request $request, $id)
    {
        $request->validate([
            'ocupada' => 'required|boolean',
        ]);

        $mesa = Mesa::findOrFail($id);
        $mesa->ocupada = $request->ocupada;
        $mesa->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Estado de la mesa actualizado correctamente.',
            'data' => $mesa,
        ]);
    }
}
