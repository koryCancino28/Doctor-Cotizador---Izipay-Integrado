<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de clientes con sus formulaciones.
     */
    public function index()
    {
        // Obtener clientes con sus formulaciones relacionadas
        $clientes = Cliente::with('formulaciones')->get();

        return view('cliente.index', compact('clientes'));
    }
}
