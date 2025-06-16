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

            // Actualizamos info del cliente
            $cliente->update([
                'telefono' => $validated['telefono'],
                'tipo_delivery' => $validated['tipo_delivery'],
                'direccion' => $validated['direccion']
            ]);

            // Calculamos total
            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            // Creamos la cotización (cabecera)
            $cotizacion = Cotizacion::create([
                'cliente_id' => $cliente->id,
                'total' => $total,
                'observacion' => $validated['observacion'] ?? null,
            ]);

            $cotizacionItems = [];

            foreach ($validated['items'] as $item) {
                // Guardamos en detalle_cotizacion
                DB::table('detalle_cotizacion')->insert([
                    'cotizacion_id' => $cotizacion->id,
                    'formulacion_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Para generar el PDF
                $formulacion = Formulacion::find($item['id']);
                $cotizacionItems[] = [
                    'nombre' => $formulacion->name,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad'],
                ];
            }

            // Generamos PDF
            $pdfUrl = $this->generatePDF($cliente, $cotizacionItems, $total, $validated['observacion'] ?? '');

            // Guardamos el nombre del archivo PDF en la cabecera
            $pdfFilename = basename($pdfUrl);
            $cotizacion->pdf_filename = $pdfFilename;
            $cotizacion->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cotización guardada exitosamente',
                'pdf_url' => $pdfUrl
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

        // Obtener el logo
        $logoPath = public_path('images/logo_grobdi.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logo = 'data:image/png;base64,' . $logoData;

        // Renderizar la vista HTML para el PDF
        $html = view('cotizacion.pdf', [
            'cliente' => $cliente,
            'items' => $items,
            'total' => $total,
            'observacion' => $observacion,
            'fecha' => now()->format('d/m/Y'),
            'logo' => $logo
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $filename = 'cotizacion_' . time() . '.pdf';

        // 📂 Guardar en public/pdf (directamente accesible por navegador)
        $relativePath = 'pdf';
        $fullPath = public_path($relativePath);

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0775, true); // Crear carpeta si no existe
        }

        file_put_contents($fullPath . '/' . $filename, $output);

        // 🌐 Devolver URL pública
        return asset($relativePath . '/' . $filename);
    }


            public function misCotizaciones()
    {
        $cliente = auth()->user()->cliente;

        if (!$cliente) {
            return redirect()->back()->with('error', 'No tienes cotizaciones porque no eres cliente.');
        }

        // Cargar cotizaciones con sus detalles y formulaciones
        $cotizaciones = Cotizacion::with(['detalles.formulacion'])
            ->where('cliente_id', $cliente->id)
            ->whereNotNull('pdf_filename')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cotizacion.mis_cotizaciones', compact('cotizaciones'));
    }


}