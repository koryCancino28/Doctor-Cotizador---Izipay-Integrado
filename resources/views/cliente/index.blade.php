@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Visitadora Medica</h1>
@stop

@section('content')
<div class="container">
    <h2>Clientes y sus Cotizaciones</h2>

    <!-- Select de clientes -->
    <div class="mb-3">
        <label for="cliente-select" class="form-label">Seleccionar Cliente</label>
        <select id="cliente-select" class="form-control" style="width: 100%;">
            <option value="">Seleccione un Cliente</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Tabla de cotizaciones -->
    <table id="clientes-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre Cliente</th>
                <th>Item</th>
                <th>Nombre de Formulación</th>
                <th>Precio Público</th>
                <th>Precio Médico</th>
                <th>Acciones</th> <!-- Columna para los botones de descarga -->
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                @foreach ($cliente->cotizaciones as $cotizacion)
                    <tr class="cotizacion-row" data-cliente-id="{{ $cliente->id }}">
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cotizacion->formulacion->item }}</td>
                        <td>{{ $cotizacion->formulacion->name }}</td>
                        <td>{{ $cotizacion->formulacion->precio_publico }}</td>
                        <td>{{ $cotizacion->formulacion->precio_medico }}</td>
                        <td>
                            <!-- Verificar si el archivo PDF existe antes de mostrar el botón -->
                            @php
                                // Ruta completa del archivo
                                $pdfPath = storage_path('app/public/pdf/'.$cotizacion->pdf_filename);
                            @endphp

                            <!-- Solo mostrar el botón si el archivo PDF existe -->
                            @if(Storage::exists('public/pdf/'.$cotizacion->pdf_filename))
                                <a href="{{ asset('storage/pdf/'.$cotizacion->pdf_filename) }}" class="btn btn-success btn-sm" target="_blank">Descargar PDF</a>
                            @else
                                <button class="btn btn-warning btn-sm" disabled>PDF no disponible</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#cliente-select').select2({
                placeholder: "Seleccione un Cliente",
                allowClear: true
            });

            var table = $('#clientes-table').DataTable({
                "ordering": false,
                "paging": true,
                "searching": true
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
