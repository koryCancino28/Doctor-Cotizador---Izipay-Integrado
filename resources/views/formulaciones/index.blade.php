@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')

@stop

@section('content')
<div class="container mt-2" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Lista de Formulaciones</h1>
    <a href="{{ route('formulaciones.create') }}" class="btne" style="background-color: #fe495f; color: white; font-size: 1.1rem; padding: 12px 30px; border-radius: 8px;">Crear Nueva Formulación</a>

    @if(session('success'))
        <div class="alert alert-success mt-3" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabla responsiva -->
    <div class="table-responsive mt-3">
        <table class="table table-striped table-bordered shadow-sm rounded">
            <thead style="background-color: #fe495f; color: white;">
                <tr>
                    <th>Item</th>
                    <th>Nombre</th>
                    <th>Precio Público</th>
                    <th>Precio Médico</th>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($formulaciones as $formulacion)
                    <tr>
                        <td>{{ $formulacion->item }}</td>
                        <td>{{ $formulacion->name }}</td>
                        <td>{{ $formulacion->precio_publico }}</td>
                        <td>{{ $formulacion->precio_medico }}</td>
                        <td>{{ $formulacion->cliente->nombre }}</td>
                        <td>
                            <div class="w">
                                <a href="{{ route('formulaciones.show', $formulacion) }}" class="btn btn-info btn-sm" style="background-color: #17a2b8; border-color: #17a2b8;"><i class="fa-regular fa-eye"></i>Ver</a>
                                <a href="{{ route('formulaciones.edit', $formulacion) }}" class="btn btn-warning btn-sm" style="background-color: #ffc107; border-color: #ffc107; color: white;"><i class="fa-solid fa-pen"></i>Editar</a>
                                <form action="{{ route('formulaciones.destroy', $formulacion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="background-color: #dc3545; border-color: #dc3545;"><i class="fa-solid fa-trash"></i>Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {!! $formulaciones->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
        </div>      
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

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
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

    </style>
@stop

@section('js')
    <script>
    </script>
@stop
