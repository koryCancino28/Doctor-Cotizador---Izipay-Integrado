<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteVisitadoraController extends Controller
{
    public function index()
    {
        // Obtenemos los datos de la vista SQL "reporte_visitadoras"
        $reporte = DB::table('reporte_visitadoras')->get();

        // Agrupamos por nombre de la visitadora para mostrar tabla por cada una
        $reporteAgrupado = $reporte->groupBy('nombre_visitadora');

        // Retornamos la vista con los datos agrupados
        return view('reportes.visitadoras', compact('reporteAgrupado'));
    }
}
