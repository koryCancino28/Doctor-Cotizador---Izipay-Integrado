@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('usuarios.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="background-color: #fe495f; color: white;">
                        <i class="fas fa-user-edit mr-2"></i> Editar Usuario
                    </div>
                    <div class="card-body">
                        <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Nombre -->
                            <div class="form-group">
                                <label for="name" style="color: #fe495f;">Nombre</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user" style="color:rgb(245, 114, 129);"></i></span>
                                    </div>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            <!-- Correo Electrónico -->
                            <div class="form-group">
                                <label for="email" style="color: #fe495f;">Correo Electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope" style="color:rgb(245, 114, 129);"></i></span>
                                    </div>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>

                            <!-- Selección de Rol -->
                            <div class="form-group">
                                <label for="role_id" style="color: #fe495f;">Rol</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-users" style="color:rgb(245, 114, 129);"></i></span>
                                    </div>
                                    <select name="role_id" id="role_id" class="form-control" required>
                                        <option value="" disabled selected>Selecciona el rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                    @if($role->id == $user->role_id) selected @endif>
                                                {{ $role->name }} - {{ $role->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Botón de Actualización -->
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-danger" style="padding: 10px 20px; border-radius: 5px;">
                                    <i class="fas fa-save mr-2"></i> Actualizar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Estilos personalizados */
        .btn-danger {
            background-color: #fe495f;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-danger:hover {
            background-color: #fe9d97;
        }

        .card {
            border-radius: 10px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #fe495f;
            box-shadow: 0 0 5px rgba(254, 73, 95, 0.5);
        }

        .card-header {
            font-weight: bold;
        }
    </style>
@stop

@section('js')
    <script>
        // Aquí puedes agregar código JS si es necesario
    </script>
@stop
