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
        $cliente = auth()->user()->cliente;

        // Si no hay cliente (por ejemplo, no es un doctor), devolvemos la vista sin formulaciones
        if (!$cliente) {
            return view('cotizacion.cotizador');
        }

        // Paginamos las formulaciones del cliente
        $formulaciones = $cliente->formulaciones()->paginate(5); 

        return view('cotizacion.cotizador', compact('formulaciones'));
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
            $cotizaciones = [];
            foreach ($validated['items'] as $item) {
                $cotizacion = Cotizacion::create([
                    'cliente_id' => $clienteId,
                    'formulacion_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'total' => $item['precio'] * $item['cantidad'],
                    'observacion' => $validated['observacion'] ?? null,
                ]);
                
                $cotizaciones[] = $cotizacion; 
                
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
            
            // Obtener el nombre del archivo PDF generado
            $pdfFilename = basename($pdf);  // Extraemos solo el nombre del archivo
            
            // Ahora, actualizamos todas las cotizaciones relacionadas con la misma formulación
            foreach ($cotizaciones as $cotizacion) {
                $cotizacion->pdf_filename = $pdfFilename;
                $cotizacion->save();
            }
            
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
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        
        $html = view('cotizacion.pdf', [
            'cliente' => $cliente,
            'items' => $items,
            'total' => $total,
            'observacion' => $observacion,
            'fecha' => now()->format('d/m/Y'),
            $logoPath = public_path('images/logo_grobdi.png'),
            $logoData = base64_encode(file_get_contents($logoPath)),
            $logo = 'data:image/png;base64,' . $logoData,
            'logo' => $logo
        ])->render();
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $output = $dompdf->output();
        $filename = 'cotizacion_'.time().'.pdf';
        $path = storage_path('app/public/pdf/'.$filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);  // Crear el directorio si no existe
        }
        
        file_put_contents($path, $output);
        
        return asset('storage/pdf/'.$filename);  // Devolver la URL pública del archivo
    }

        public function misCotizaciones()
    {
        $cliente = auth()->user()->cliente;

        if (!$cliente) {
            return redirect()->back()->with('error', 'No tienes cotizaciones porque no eres cliente.');
        }

        // Agrupar cotizaciones por PDF generado
        $cotizaciones = Cotizacion::with('formulacion')
            ->where('cliente_id', $cliente->id)
            ->whereNotNull('pdf_filename')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('pdf_filename');

        return view('cotizacion.mis_cotizaciones', compact('cotizaciones'));
    }

}