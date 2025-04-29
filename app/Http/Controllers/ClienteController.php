<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ClienteController extends Controller
{
    public function index()
    {
       $clientes = Cliente::with(['cotizaciones' => function($query) {
        $query->whereIn('id', function($query) {
            $query->select(DB::raw('MIN(id)'))
                  ->from('cotizaciones')
                  ->whereNotNull('pdf_filename')
                  ->groupBy('pdf_filename'); // Subconsulta para obtener una cotización por cada pdf_filename único
        });
    }])->get();


        return view('cliente.index', compact('clientes'));
    } 
}
