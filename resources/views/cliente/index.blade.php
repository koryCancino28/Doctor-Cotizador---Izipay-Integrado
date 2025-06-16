@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Proforma del Doctor</h1>

    <!-- Select de clientes -->
    <div class="mb-4">
        <label for="cliente-select" class="form-label" style="font-size: 1.1rem; color:rgb(169, 23, 23);">Seleccionar Cliente</label>
        <select id="cliente-select" class="form-control select2" style="width: 100%; border-color: #fe495f;">
            <option value="">Seleccione un Cliente</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Tabla de cotizaciones -->
    <div class="table-responsive mt-3">
        <table id="clientes-table" class="table table-striped table-bordered shadow-sm rounded">
            <thead style="background-color: #fe495f; color: white;">
                <tr>
                    <th>Doctor</th>
                    <th>CMP</th>
                    <th>Teléfono</th>
                    <th>Proforma</th> <!-- Columna para el botón de descarga -->
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    @foreach ($cliente->cotizaciones as $cotizacion)
                        <tr class="cotizacion-row" data-cliente-id="{{ $cliente->id }}">
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->cmp }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>
                                @php
                                    $pdfFilename = $cotizacion->pdf_filename;
                                    $pdfPath = public_path('pdf/' . $pdfFilename);
                                @endphp

                                @if($pdfFilename && file_exists($pdfPath))
                                    <a href="{{ asset('pdf/' . $pdfFilename) }}"
                                    class="btn btn-s btn-sm"
                                    target="_blank">
                                        <i class="fas fa-download"></i> Descargar PDF
                                    </a>
                                @else
                                    <button class="btn btn-warning btn-sm" disabled>PDF no disponible</button>
                                    @if($pdfFilename)
                                        <small class="text-muted">(Archivo no encontrado)</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            border-color: #fe495f;
            border-radius: 8px;
            box-shadow: 0 0 0 0.2rem rgba(254, 73, 95, 0.25);
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .table-bordered {
            border-color: #fe495f;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table thead th {
            background-color: #fe495f;
            color: white;
            font-size: 1.1rem;
        }

        .btn {
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            gap: 5px;          /* Espacio entre icono y texto */
            white-space: nowrap;
        }

        .btn-warning {
            background-color: #f0ad4e;
            border: none;
        }

        .btn-s{
            background-color: #fe495f;
            color: white;"
            border: none;
        }
        
        .btn:hover {
            transform: scale(1.05); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.2s ease; /* Transición suave */
        }

        .btn-warning:disabled {
            background-color:rgb(168, 61, 61);
            border-color: #ccc;
        }

        .container {
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(212, 206, 206, 0.86);
        }

        /* Estilos personalizados para la tabla DataTables */
        table#clientes-table {
            background-color:rgb(255, 200, 200);
        }

        table#clientes-table th {
            background-color: #fe495f;
            color: white;
        }

        table#clientes-table td {
            background-color: rgb(255, 249, 249);
        }

        table#clientes-table thead th {
            text-align: center;
            font-weight: bold;
        }

        table#clientes-table tfoot td {
            font-weight: bold;
        }

    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#cliente-select').select2({
                placeholder: "Seleccione un Cliente",
                allowClear: true
            });

            var table = $('#clientes-table').DataTable({
                "ordering": false, // Deshabilitar el ordenamiento por columna
                "paging": true,    // Habilitar la paginación
                "info": true,      // Habilitar la información de la tabla
                "lengthChange": false, // Deshabilitar el cambio de cantidad de registros por página
                "searching": false, // Deshabilitar el buscador predeterminado
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" // Cargar idioma español
                }
            });

            $('#cliente-select').change(function() {
                var clienteId = $(this).val();
                if (clienteId === '') {
                    table.rows().show();
                } else {
                    table.rows().every(function() {
                        var row = this.node();
                        var rowClienteId = $(row).data('cliente-id');
                        if (rowClienteId == clienteId) {
                            $(row).show();
                        } else {
                            $(row).hide();
                        }
                    });
                }
            });
        });
    </script>
@stop
