@extends('adminlte::page')

@section('title', 'Usuarios Registrados')

@section('content_header')
   
@stop

@section('content')
@php
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

    if (config('adminlte.use_route_url', false)) {
        $registerUrl = $registerUrl ? route($registerUrl) : '';
    } else {
        $registerUrl = $registerUrl ? url($registerUrl) : '';
    }
@endphp

{{-- Botón de Registro --}}
@if($registerUrl)
    <p class="my-0">
        <a href="{{ $registerUrl }}" class="btn" style="background-color: #fe495f; color: white; padding: 10px 20px; border-radius: 5px; margin-top: 10px; font-weight: bold;">
        <i class="fas fa-user-plus mr-2"></i>Registrar Nuevo Usuario
        </a>
    </p>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
        {{ session('success') }}
    </div>
@endif

{{-- DataTable --}}
<table class="table table-bordered" id="usuariosTable">
    <thead>
        <tr>
            <th class="text-center">N°</th>
            <th class="text-center">Nombre</th>
            <th class="text-center">Email</th>
            <th class="text-center">Rol</th>
            <th class="text-center">Fecha de Creación</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $index => $user)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $user->name }}</td>
                <td class="text-center">{{ $user->email }}</td>
                <td class="text-center">{{ $user->role->name ?? 'Sin rol' }}</td>
                <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="text-center">
                    <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop

@section('css')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        /* Estilos personalizados para la tabla DataTables */
        table#usuariosTable {
            background-color: #fffec8;
        }

        table#usuariosTable th {
            background-color: #fe495f;
            color: white;
        }

        table#usuariosTable td {
            background-color: rgb(255, 249, 249);
        }

        table#usuariosTable thead th {
            text-align: center;
            font-weight: bold;
        }

        table#usuariosTable tfoot td {
            font-weight: bold;
        }

        /* Estilo de los botones de acción */
        .btn-warning {
            background-color:rgb(118, 124, 148);
            color: white;
            border: none;
        }

        .btn-warning:hover {
            background-color:rgb(93, 95, 125);
            color: white;
        }

        .btn-danger {
            background-color: #fe495f;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background-color:rgb(237, 124, 118);
        }
        .btn:hover {
            transform: scale(1.05); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.2s ease; /* Transición suave */
        }
        table#usuariosTable thead th {
            text-align: center;
            font-weight: bold;
        }

        table#usuariosTable tfoot td {
            font-weight: bold;
        }
        .alert-success {
            margin-top: 10px;
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            text-align: center;
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    {{-- jQuery y DataTables JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#usuariosTable').DataTable({
                "ordering": false, // Deshabilitar el ordenamiento por columna
                "searching": true, // Habilitar la barra de búsqueda
                "paging": true,    // Habilitar la paginación
                "info": true,      // Habilitar la información de la tabla
                "lengthChange": false,
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" // Cargar idioma español
                }
            });
        });
        $(document).ready(function() {
            setTimeout(function() {
                $('#successAlert').fadeOut();
            }, 3000); 
        });
    </script>
@stop
