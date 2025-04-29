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
        $formulaciones = Formulacion::with('cliente')->paginate(10);
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
            'precio_publico' => 'required|decimal:0,2',
            'precio_medico' => 'required|decimal:0,2',
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        Formulacion::create($request->all());

        return redirect()->route('formulaciones.index')->with('success', 'Formulación creada correctamente');
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
            'precio_publico' => 'required|decimal:0,2', 
            'precio_medico' => 'required|decimal:0,2',
            'cliente_id' => 'required|exists:clientes,id',
        ]);

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
        $formulacione->delete();
        return redirect()->route('formulaciones.index')->with('success', 'Formulación eliminada correctamente');
    }
}
