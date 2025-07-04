@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<div></div>
@stop

@section('content')
<div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    @include('messages')

    <h1 class="text-center mb-3" style="color: #fe495f; font-weight: bold;">Formulaciones <i class="fas fa-flask mr-2"></i></h1>
    <a href="{{ route('formulaciones.create') }}" class="btne" style="background-color: #fe495f; color: white; font-size: 1.1rem; padding: 12px 30px; border-radius: 8px;"><i class="fas fa-file-medical mr-2"></i>
    Crear Nueva Formulación</a>

    <!-- Tabla responsiva -->
    <div class="table-responsive mt-3" style="border-radius: 10px;">
        <table class="table table-striped table-bordered shadow-sm rounded" id="formulacionesTable">
            <thead style="background-color: #fe495f; color: white;">
                <tr>
                    <th>Item</th>
                    <th>Nombre</th>
                    <th>Precio Público</th>
                    <th>Precio Médico</th>
                    <th>Doctor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($formulaciones as $formulacion)
                    <tr>
                        <td>{{ $formulacion->item }}</td>
                        <td style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $formulacion->name }}</td>
                        <td>{{ $formulacion->precio_publico }}</td>
                        <td>{{ $formulacion->precio_medico }}</td>
                        <td style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $formulacion->cliente->nombre }}</td>
                        <td>
                            <div class="w">
                                @include('formulaciones.show', ['formulacione' => $formulacion])
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detalleFormulacionModal{{ $formulacion->id }}" style="background-color: #17a2b8; border-color: #17a2b8;"><i class="fa-regular fa-eye"></i>Ver</button>
                                <a href="{{ route('formulaciones.edit', $formulacion) }}" class="btn btn-warning btn-sm" style="background-color: #ffc107; border-color: #ffc107; color: white;"><i class="fa-solid fa-pen"></i>Editar</a>
                                <form action="{{ route('formulaciones.destroy', $formulacion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #dc3545; border-color: #dc3545;" onclick="return confirm('¿Eliminar esta formulación?')"><i class="fa-solid fa-trash"></i>Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>      
    </div>
</div>
@stop

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .btn {
        font-size: 1rem; /* Tamaño de texto */
        padding: 8px 20px; /* Padding para que los botones sean más amplios */
        border-radius: 8px; /* Bordes redondeados */
        display: flex; /* Asegura que los iconos y texto estén alineados */
        align-items: center; /* Alinea verticalmente el icono con el texto */
        }

        .btn i {
            margin-right: 5px; /* Espaciado entre el icono y el texto */
        }
        .w {
            display: flex;
            justify-content: space-around;
            gap: 10px;
        }

        table thead th {
            background-color: #fe495f;
            color: white;
        }

        table tbody td {
            background-color: rgb(255, 249, 249);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table-bordered {
            border-color: #fe495f;
        }
        table th, table td {
            text-align: center;
        }
        /* Efecto hover solo para este botón específico */
        .btn:hover {
            transform: scale(1.10); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.4s ease; /* Transición suave */
        }
        .modal-body::-webkit-scrollbar,
        .scrollable-custom::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track,
        .scrollable-custom::-webkit-scrollbar-track {
            background: #fcebed;
        }

        .modal-body::-webkit-scrollbar-thumb,
        .scrollable-custom::-webkit-scrollbar-thumb {
            background: rgb(255, 178, 187);
            border-radius: 4px;
        }

        /* Estilos para Firefox */
        .modal-body,
        .scrollable-custom {
            scrollbar-width: thin;
            scrollbar-color: rgb(255, 178, 187) #fcebed;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
        $('#formulacionesTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json',
            },
            responsive: true,
            ordering: false,
            pageLength: 10,
            lengthChange: false,
            info: false
        });
        $('.dataTables_filter').addClass('mb-3 mt-3');
    });
    </script>
@stop
@section('plugins.Datatables', true)
