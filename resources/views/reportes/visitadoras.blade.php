@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<div></div>
@stop

@section('content')
<div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-chart-bar"></i>    REPORTE</h2>
        </div>
        
        <div class="col-md-6 d-flex justify-content-end">
            <div class="btn-group" role="group">
                @foreach($reporteAgrupado as $visitadora => $doctores)
                    <button class="btn btn-sm btn-outline" 
                            onclick="mostrarTabla('{{ Str::slug($visitadora, '_') }}')">
                        {{ $visitadora }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    <div style="max-width: 300px; margin: auto;">
        <canvas id="visitadoraChart"></canvas>
    </div>
    @foreach($reporteAgrupado as $visitadora => $doctores)
        <div id="tabla_{{ Str::slug($visitadora, '_') }}" class="tabla-visitadora" style="display: none;">
            <div class="card mb-4">
                <div class="card-header text-white">
                    <strong>{{ $visitadora }}</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm datatable" id="tabla_{{ Str::slug($visitadora, '_') }}_table">

                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>N° Cotizaciones</th>
                                <th>Total Cotizado (S/)</th>
                                <th>Última Cotización</th>
                                <th>Requiere Confirmaciones</th>
                                <th>Confirmaciones Realizadas</th>
                                <th>Faltantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctores as $doc)
                                <tr>
                                    <td>{{ $doc->nombre_cliente }}</td>
                                    <td>{{ $doc->numero_cotizaciones }}</td>
                                    <td>{{ number_format($doc->total_cotizado, 2) }}</td>
                                    <td>
                                        {{ $doc->ultima_cotizacion_fecha 
                                            ? \Carbon\Carbon::parse($doc->ultima_cotizacion_fecha)->format('Y-m-d') . ' - S/' . number_format($doc->ultima_cotizacion_total, 2)
                                            : '—' 
                                        }}
                                    </td>
                                    <td>{{ $doc->cotizaciones_requieren_confirmacion }}</td>
                                    <td>{{ $doc->numero_confirmaciones }}</td>
                                    <td>
                                        {{ max(0, $doc->cotizaciones_requieren_confirmacion - $doc->numero_confirmaciones) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
@section('css')
@section('css')
<style>
    :root {
        --color-base: #fe495f;
        --color-base-light: #ff7a8b;
        --color-contrast: #ffffff;
        --color-table-head: #fff0f2;
    }

    .btn-base {
        background-color: var(--color-base);
        color: white;
        border: none;
    }

    .btn-base:hover {
        background-color: var(--color-base-light);
        color: white;
    }

    .card-header {
        background-color: var(--color-base) !important;
        color: var(--color-contrast) !important;
    }

    .table thead {
        background-color: var(--color-table-head);
        color: #333;
    }

    .btn-outline {
        border-color: var(--color-base);
        color: var(--color-base);
    }

    .btn-outline:hover {
        background-color: var(--color-base);
        color: white;
    }

    h2 {
        color: var(--color-base);
        font-weight: bold;
    }
</style>
@stop

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        $('.datatable').each(function () {
            $(this).DataTable({
                paging: true,
                searching: false,
                ordering: false,
                info: false,
                lengthChange: false, 
                pageLength: 5,
                language: {
                    paginate: {
                        previous: '«',
                        next: '»'
                    }
                },
                dom: 'tp' // Solo tabla + paginación
            });
        });
    });
</script>
<script>
    const visitadoraData = @json($reporteAgrupado->map(function($items, $visitadora) {
        return [
            'visitadora' => $visitadora,
            'total' => $items->sum('total_cotizado')
        ];
    })->values());

    const ctx = document.getElementById('visitadoraChart').getContext('2d');
    const labels = visitadoraData.map(v => v.visitadora);
    const data = visitadoraData.map(v => v.total);

    const visitadoraChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Cotizado por Visitadora',
                data: data,
                backgroundColor: [
                '#a78bfa',  // Lavanda clara
                '#ff93a5',  // Rosa claro pastel
                '#5ee1e6',  // Aqua pastel brillante
                '#9be7a3',  // Verde menta suave
                '#fe495f',  // Rosa vibrante (color base)
                '#ffc145',  // Amarillo cálido vibrante
                '#fcbf49'   // Naranja pastel brillante
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return `${label}: S/ ${value.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
                        }
                    }
                }
            }
        }
    });
</script>
<script>
    function mostrarTabla(id) {
        document.querySelectorAll('.tabla-visitadora').forEach(el => el.style.display = 'none');
        document.getElementById('tabla_' + id).style.display = 'block';
    }
</script>
@stop

