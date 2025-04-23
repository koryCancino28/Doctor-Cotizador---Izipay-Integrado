<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Formulacion;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('cliente.cotizador');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:formulacions,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'telefono' => 'required|string|max:20',
            'tipo_delivery' => 'required|in:Recojo en tienda,Entrega a domicilio',
            'direccion' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $cliente = auth()->user()->cliente()->firstOrFail();
            $clienteId = $cliente->id;
            
            // Actualizar SOLO los datos del cliente
            $cliente->update([
                'telefono' => $validated['telefono'],
                'tipo_delivery' => $validated['tipo_delivery'],
                'direccion' => $validated['direccion']
            ]);
            
            // Crear cotización (sin los campos de envío)
            foreach ($validated['items'] as $item) {
                Cotizacion::create([
                    'cliente_id' => $clienteId,
                    'formulacion_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'total' => $item['precio'] * $item['cantidad']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cotización guardada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}