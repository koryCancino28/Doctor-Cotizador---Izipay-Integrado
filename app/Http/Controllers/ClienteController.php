<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use App\Models\Confirmacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ClienteController extends Controller
{
        public function index()
    {
        $visitadoraId = auth()->id(); // id de la visitadora logueada

        $clientes = Cliente::with(['cotizaciones' => function($query) {
            $query->whereNotNull('pdf_filename')
                ->orderBy('id', 'desc');
        }, 'cotizaciones.confirmacion', 'user'])
        ->where('visitadora_id', $visitadoraId)
        ->get();
        
        return view('cliente.index', compact('clientes'));
    }

        public function store(Request $request)
    {
        $request->validate([
            'cotizacion_id' => 'required|exists:cotizaciones,id',
            'archivo' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        $archivo->move(public_path('confirmaciones'), $nombreArchivo);

        Confirmacion::create([
            'cotizacion_id' => $request->cotizacion_id,
            'visitadora_id' => auth()->id(),
            'archivo' => 'confirmaciones/' . $nombreArchivo,
            'tipo_archivo' => $archivo->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'imagen',
        ]);

        return redirect()->back()->with('success', 'Archivo enviado correctamente.');
    }


}
