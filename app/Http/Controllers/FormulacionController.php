<?php

namespace App\Http\Controllers;

use App\Models\Formulacion;
use App\Models\Cliente;
use Illuminate\Http\Request;

class FormulacionController extends Controller
{
    public function index()
    {
        // Traemos todas las formulaciones con la relación cliente
        $formulaciones = Formulacion::with('cliente')->get();
        return view('formulaciones.index', compact('formulaciones'));
    }

    public function create()
    {
        // Obtenemos todos los clientes
        $clientes = Cliente::all();
        return view('formulaciones.create', compact('clientes'));
    }

        public function store(Request $request)
    {
        $request->validate([
            'item' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'precio_publico' => 'required|numeric|between:0,999999.99',
            'precio_medico'  => 'required|numeric|between:0,999999.99',
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $existingFormulation = Formulacion::where('item', $request->item)
                                        ->where('cliente_id', $request->cliente_id)
                                        ->first();

        if ($existingFormulation) {
            return back()->withErrors(['item' => 'Este item ya está asignado a este doctor.'])->withInput();
        }

        $formulation = new Formulacion();
        $formulation->item = $request->item;
        $formulation->name = $request->name;
        $formulation->precio_publico = $request->precio_publico;
        $formulation->precio_medico = $request->precio_medico;
        $formulation->cliente_id = $request->cliente_id;
        $formulation->save();

        return redirect()->route('formulaciones.index')->with('success', 'Formulación creada exitosamente.');
    }

    public function show(Formulacion $formulacione)
    {
        return view('formulaciones.show', compact('formulacione'));
    }

    public function edit(Formulacion $formulacione) // Usar Route Model Binding aquí también
    {
        $clientes = Cliente::all(); 
        return view('formulaciones.edit', compact('formulacione', 'clientes')); 
    }

        public function update(Request $request, Formulacion $formulacione)
    {
        $request->validate([
            'item' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'precio_publico' => 'required|numeric|between:0,999999.99',
            'precio_medico'  => 'required|numeric|between:0,999999.99',
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        // Verificar si el item ya existe para el cliente (doctor)
        $existingFormulation = Formulacion::where('item', $request->item)
        ->where('cliente_id', $request->cliente_id)
        ->where('id', '!=', $formulacione->id) // Excluir el actual
        ->first();

        if ($existingFormulation) {
            // Si existe, devuelve un error con el mensaje adecuado
            return back()->withErrors(['item' => 'El item que ingresó ya está asignado a este doctor.'])->withInput();
        }

        // Actualizar la formulación con los datos del formulario
        $formulacione->update([
            'item' => $request->item,
            'name' => $request->name,
            'precio_publico' => $request->precio_publico,
            'precio_medico' => $request->precio_medico,
            'cliente_id' => $request->cliente_id,
        ]);

        return redirect()->route('formulaciones.index')->with('success', 'Formulación actualizada correctamente');
    }

        public function destroy(Formulacion $formulacione)
    {
        $hasRelations = $formulacione->detalleCotizaciones()->exists();
        
        if ($hasRelations) {
            return view('formulaciones.forzar', compact('formulacione', 'hasRelations'));
        }
        
        $formulacione->delete();
        return redirect()->route('formulaciones.index')
            ->with('success', 'Formulación eliminada correctamente');
    }

    public function forceDestroy(Formulacion $formulacione)
    {
        $formulacione->detalleCotizaciones()->delete();
        $formulacione->delete();
        
        return redirect()->route('formulaciones.index')
            ->with('success', 'Formulación y sus relaciones eliminadas correctamente');
    }
}
