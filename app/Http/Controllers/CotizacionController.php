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
        $formulaciones = $cliente->formulaciones()->get(); 

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
            'tipo_pago' => 'required|in:contra_entrega,pasarela_izipay,transferencia',
            'voucher' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'codigo_transaccion' => 'nullable|string|max:100',
        ], [
            'items.required' => 'Debes ingresar al menos una Formulación para ser cotizada.',
            'items.min' => 'Debes ingresar al menos una Formulación para ser cotizada.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria para cada ítem.',
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

            $voucherPath = null;
            if ($request->hasFile('voucher')) {
                $path = $request->file('voucher')->store('vouchers', 'public');
                $voucherPath = 'storage/' . $path;
            }

            // Creamos la cotización
            $cotizacion = Cotizacion::create([
                'cliente_id' => $cliente->id,
                'total' => $total,
                'observacion' => $validated['observacion'] ?? null,
                'tipo_pago' => $validated['tipo_pago'],
                'voucher_path' => $voucherPath,
                'estado_pago' => $validated['tipo_pago'] === 'pasarela_izipay' ? 'pendiente' : null,
                'codigo_transaccion' => $validated['codigo_transaccion'] ?? null,
            ]);

            $cotizacionItems = [];

            foreach ($validated['items'] as $item) {
                DB::table('detalle_cotizacion')->insert([
                    'cotizacion_id' => $cotizacion->id,
                    'formulacion_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $formulacion = Formulacion::find($item['id']);
                $cotizacionItems[] = [
                    'item'=> $formulacion->item,
                    'nombre' => $formulacion->name,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad'],
                ];
            }

            // Generar PDF
            $pdfUrl = $this->generatePDF(
                $cliente,
                $cotizacionItems,
                $total,
                $validated['observacion'] ?? '',
                $validated['tipo_pago'],
                $voucherPath,
                $validated['tipo_pago'] === 'pasarela_izipay',
                $validated['codigo_transaccion'] ?? ''
            );

            $pdfFilename = basename($pdfUrl);
            $cotizacion->pdf_filename = $pdfFilename;
            $cotizacion->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cotización guardada exitosamente',
                'pdf_url' => $pdfUrl,
                'cotizacion_id' => $cotizacion->id,
                'total' => $total,
                'tipo_pago' => $validated['tipo_pago']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
        private function generatePDF($cliente, $items, $total, $observacion, $tipoPago, $voucherPath = null, $esPagoExitoso = false,  $codigoTransaccion = '')
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $tipoPagoTexto = ucfirst(str_replace('_', ' ', $tipoPago));

        $infoPago = "Condiciones de pago: $tipoPagoTexto";

        if ($tipoPago === 'transferencia') {
            if ($voucherPath && file_exists(public_path($voucherPath))) {
                $voucherFullPath = public_path($voucherPath);
                $base64 = base64_encode(file_get_contents($voucherFullPath));
                $voucherImage = 'data:image/jpeg;base64,' . $base64;
                $infoPago .= "<p style='margin-top:5px;'>";
                if (!empty($codigoTransaccion)) {
                    $infoPago .= "Código de transacción: <b>$codigoTransaccion</b><br>";
                }
                $infoPago .= "<b>Voucher:</b><br>
                            <img src='$voucherImage' style='max-width: 300px; max-height: 400px; height: auto; width: auto; display:block; margin-top: 5px;' />
                            </p>";
            } else {
                $infoPago .= "<p style='margin-top:5px;'>Estado: Pago pendiente (no se ha subido el voucher)</p>";
            }
        } elseif ($tipoPago === 'pasarela_izipay') {
            $infoPago .= $esPagoExitoso
                ? "\nEstado: Operación exitosa"
                : "\nEstado: Pago fallido o no confirmado";
        }

        // Obtener el logo
        $logoPath = public_path('images/logo_grobdi.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logo = 'data:image/png;base64,' . $logoData;

        $html = view('cotizacion.pdf', [
            'cliente' => $cliente,
            'items' => $items,
            'total' => $total,
            'observacion' => $observacion,
            'fecha' => now()->format('d/m/Y'),
            'logo' => $logo,
            'infoPago' => $infoPago
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $filename = 'cotizacion - Dr.' .$cliente->id.' - '. time() . '.pdf';

        $relativePath = 'pdf';
        $fullPath = public_path($relativePath);

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0775, true); // Crear carpeta si no existe
        }

        file_put_contents($fullPath . '/' . $filename, $output);

        return asset($relativePath . '/' . $filename);
    }


            public function misCotizaciones()
    {
        $cliente = auth()->user()->cliente;

        if (!$cliente) {
            return redirect()->back()->with('error', 'No tienes cotizaciones porque no eres cliente.');
        }

        $cotizaciones = Cotizacion::with(['detalles.formulacion'])
            ->where('cliente_id', $cliente->id)
            ->whereNotNull('pdf_filename')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cotizacion.mis_cotizaciones', compact('cotizaciones'));
    }
        public function verificarExistencia(Request $request)
    {
        $ids = $request->input('ids', []);
        $idsValidos = Formulacion::whereIn('id', $ids)->pluck('id')->toArray();

        return response()->json(['ids_validos' => $idsValidos]);
    }
}