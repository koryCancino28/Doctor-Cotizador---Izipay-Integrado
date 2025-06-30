@extends('adminlte::page')

@section('title', 'Usuarios Registrados')

@section('content_header')
<div></div>
   
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
    <div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    @include('messages')

        <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Usuarios
            <i class="fas fa-users mr-2"></i>
        </h1>
                <p class="my-0">
                    <a href="{{ $registerUrl }}" class="btn mb-3" style="background-color: #fe495f; color: white; padding: 10px 20px; border-radius: 5px; margin-top: 10px; font-weight: bold;">
                    <i class="fas fa-user-plus mr-2"></i>Registrar Nuevo Usuario
                    </a>
                </p>
            @endif

        <div class="table-responsive mt-3" style="border-radius: 10px;">
            {{-- DataTable --}}
            <table class="table table-bordered" id="usuariosTable" width="100%">
                <thead>
                    <tr>
                        <th class="text-center">N°</th>
                        <th class="text-center">Usuario</th>
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
                            <td class="text-center" style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $user->name }} {{ $user->last_name }}</td>
                            <td class="text-center">{{ $user->email }}</td>
                            <td class="text-center">{{ $user->role->name ?? 'Sin rol' }}</td>
                            <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="w">
                                    <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-wa btn-sm"><i class="fas fa-user-edit mr-2"></i>
                                    Editar</a>
                                    <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" style="display:inline;" onclick="return confirm('¿Eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-dan btn-sm"><i class="fas fa-trash-alt mr-2"></i>
                                        Eliminar</button>
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
    <style>
        .btn-sm {
        font-size: 1rem; /* Tamaño de texto */
        padding: 8px 20px; /* Padding para que los botones sean más amplios */
        border-radius: 8px; /* Bordes redondeados */
        display: flex; /* Asegura que los iconos y texto estén alineados */
        align-items: center; /* Alinea verticalmente el icono con el texto */
        }

        .btn-sm i {
            margin-right: 5px; /* Espaciado entre el icono y el texto */
        }
        .w {
            display: flex;
            justify-content: space-around;
            gap: 5px;
        }
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
        .btn-wa {
            background-color:rgb(118, 124, 148);
            color: white;
            border: none;
        }

        .btn-wa:hover {
            background-color:rgb(93, 95, 125);
            color: white;
        }

        .btn-dan {
            background-color: #fe495f;
            color: white;
            border: none;
        }

        .btn-dan:hover {
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
       
    </style>
@stop

@section('js')
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
    </script>
@stop
