@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
<div>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #fe495f; color: white;">
                        <div>
                            <i class="fas fa-user-edit mr-2"></i> Editar Usuario
                        </div>
                        
                        <button type="button" class="btn btne btn-sm" id="toggle-password-fields"
                            style="background-color:hsl(353, 100%, 93.1%); color:rgb(255, 112, 122); border-radius: 5px; font-weight: bold;">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-dismissible fade show text-center position-relative" role="alert" style="background-color:rgb(254, 247, 120); color: rgb(184, 187, 11);">
                                <ul class="mb-0 list-unstyled">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button"
                                        class="close position-absolute" style="top: 0.5rem; right: 1rem;"
                                        data-dismiss="alert" aria-label="Cerrar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                        <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Nombre -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name" style="color: #fe495f;">Nombre</label>
                                    <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user" style="color:rgb(245, 114, 129);"></i></span>
                                    </div>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Nombre" required value="{{ old('name', $user->name) }}">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="last_name" style="color: #fe495f;">Apellido</label>
                                    <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user" style="color:rgb(245, 114, 129);"></i></span>
                                    </div>
                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Apellido" required value="{{ old('last_name', $user->last_name) }}">
                                    </div>
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

                            <div id="password-fields" style="display: none;">
                                    <!-- Contraseña -->
                                <div class="form-group">
                                    <label for="password" style="color: #fe495f;">Contraseña</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock" style="color:rgb(245, 114, 129);"></i></span>
                                        </div>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Nueva contraseña (8 caracteres)">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div class="form-group">
                                    <label for="password_confirmation" style="color: #fe495f;">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock" style="color:rgb(245, 114, 129);"></i></span>
                                        </div>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repita la contraseña">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary toggle-confirm-password" type="button">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
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

                            <!-- CMP (solo visible si el rol es Doctor) -->
                        <div class="form-group" id="cmp_group" style="display: none;">
                            <label for="cmp" style="color: #fe495f;">CMP</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card" style="color:rgb(245, 114, 129);"></i></span>
                                </div>
                                <input type="text" name="cmp" id="cmp" class="form-control" placeholder="Ingrese el CMP" value="{{ old('cmp', optional($user->cliente)->cmp) }}">
                            </div>
                            <div class="form-group mt-3">
                                <label for="visitadora_id" style="color: #fe495f;">Visitadora Médica</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-user-nurse" style="color:rgb(245, 114, 129);"></i>
                                        </span>
                                    </div>
                                    <select name="visitadora_id" id="visitadora_id" class="form-control">
                                        <option value="">-- Selecciona una visitadora --</option>
                                        @foreach ($visitadoras as $visitadora)
                                            <option value="{{ $visitadora->id }}" {{ old('visitadora_id', $user->cliente->visitadora_id ?? '') == $visitadora->id ? 'selected' : '' }}>
                                                {{ $visitadora->name }} {{ $visitadora->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    <div class="form-group text-right">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-default mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Cancelar
                        </a>
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
        .btne:hover {
            transform: scale(1.10); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.4s ease; /* Transición suave */
        }
    </style>
@stop

@section('js')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función genérica para toggle
        function setupToggle(buttonClass, fieldId) {
            const toggleBtn = document.querySelector(buttonClass);
            if(toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const field = document.querySelector(fieldId);
                    const icon = this.querySelector('i');
                    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                    field.setAttribute('type', type);
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        }
        // Configurar ambos toggles
        setupToggle('.toggle-password', '#password');
        setupToggle('.toggle-confirm-password', '#password_confirmation');
        // Función para mostrar u ocultar el campo CMP según el rol seleccionado
        const roleSelect = document.getElementById('role_id');
        const cmpGroup = document.getElementById('cmp_group');
        
        roleSelect.addEventListener('change', function() {
            // Si el rol es "Doctor" (ID 4), mostrar el campo cmp
            if (roleSelect.value == '4') {
                cmpGroup.style.display = 'block'; // Mostrar el campo CMP
            } else {
                cmpGroup.style.display = 'none'; // Ocultar el campo CMP
            }
        });

        if (roleSelect.value == '4') {
            cmpGroup.style.display = 'block'; // Mostrar el campo CMP si el rol es Doctor
        }
        //ocultar contraseña 
        const toggleButton = document.getElementById('toggle-password-fields');
        const passwordFields = document.getElementById('password-fields');

        toggleButton.addEventListener('click', function () {
            const isHidden = passwordFields.style.display === 'none' || passwordFields.style.display === '';

            // Mostrar u ocultar campos
            passwordFields.style.display = isHidden ? 'block' : 'none';

            // Cambiar texto e ícono del botón
            toggleButton.innerHTML = isHidden
                ? '<i class="fas fa-eye"></i>'
                : '<i class="fas fa-eye-slash"></i>';
        });
    });

    </script>
@stop
