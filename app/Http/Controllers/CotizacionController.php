<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Formulacion;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('cotizacion.cotizador');
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
            'observacion' => 'nullable|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $cliente = auth()->user()->cliente()->firstOrFail();
            $clienteId = $cliente->id;
        
            $cliente->update([
                'telefono' => $validated['telefono'],
                'tipo_delivery' => $validated['tipo_delivery'],
                'direccion' => $validated['direccion']
            ]);
        
            $cotizacionItems = [];
            foreach ($validated['items'] as $item) {
                $cotizacion = Cotizacion::create([
                    'cliente_id' => $clienteId,
                    'formulacion_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'total' => $item['precio'] * $item['cantidad'],
                    'observacion' => $validated['observacion'] ?? null,
                ]);
                
                $formulacion = Formulacion::find($item['id']);
                $cotizacionItems[] = [
                    'nombre' => $formulacion->name,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad']
                ];
            }
        
            DB::commit();
            
            // Generar PDF
            $total = array_sum(array_column($cotizacionItems, 'subtotal'));
            $pdf = $this->generatePDF($cliente, $cotizacionItems, $total, $validated['observacion'] ?? '');
            
            return response()->json([
                'success' => true,
                'message' => 'Cotización guardada exitosamente',
                'pdf_url' => $pdf
            ]);
        
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function generatePDF($cliente, $items, $total, $observacion)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = view('cotizacion.pdf', [
            'cliente' => $cliente,
            'items' => $items,
            'total' => $total,
            'observacion' => $observacion,
            'fecha' => now()->format('d/m/Y')
        ])->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $output = $dompdf->output();
        $filename = 'cotizacion_'.time().'.pdf';
        $path = storage_path('app/public/pdf/'.$filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        
        file_put_contents($path, $output);
        
        return asset('storage/pdf/'.$filename);
    }
}