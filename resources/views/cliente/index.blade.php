@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Visitadora medica</h1>
@stop

@section('content')
<div class="container">
    <h2>Clientes y sus Formulaciones</h2>

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

    <!-- Tabla de formulaciones -->
    <table id="clientes-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre Cliente</th>
                <th>Item</th>
                <th>Nombre de Formulación</th>
                <th>Precio Público</th>
                <th>Precio Médico</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                @foreach ($cliente->formulaciones as $formulacion)
                    <tr class="cliente-row" data-cliente-id="{{ $cliente->id }}">
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $formulacion->item }}</td>
                        <td>{{ $formulacion->name }}</td>
                        <td>{{ $formulacion->precio_publico }}</td>
                        <td>{{ $formulacion->precio_medico }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@stop

@section('css')
    <!-- Agregar el CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <!-- Agregar los scripts de DataTables y Select2 -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializa Select2 en el select de clientes
            $('#cliente-select').select2({
                placeholder: "Seleccione un Cliente",
                allowClear: true
            });

            // Inicializa la tabla de DataTables
            var table = $('#clientes-table').DataTable({
                "ordering": false,  // Desactivar la ordenación de columnas
                "paging": true,     // Habilitar paginación
                "searching": true   // Habilitar búsqueda
            });

            // Filtrar por cliente seleccionado
            $('#cliente-select').change(function() {
                var clienteId = $(this).val();

                // Si no se selecciona cliente, mostrar todas las filas
                if (clienteId === '') {
                    table.rows().show(); // Muestra todas las filas
                } else {
                    // Ocultar las filas que no pertenecen al cliente seleccionado
                    table.rows().every(function() {
                        var row = this.node();
                        var rowClienteId = $(row).data('cliente-id');
                        
                        if (rowClienteId == clienteId) {
                            $(row).show(); // Mostrar solo las filas del cliente seleccionado
                        } else {
                            $(row).hide(); // Ocultar las demás
                        }
                    });
                }
            });
        });
    </script>
@stop
