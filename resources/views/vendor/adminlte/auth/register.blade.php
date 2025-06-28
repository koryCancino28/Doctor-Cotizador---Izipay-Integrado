@extends('adminlte::page')

@section('title', 'Registrar Usuarios')

@section('content_header')
    <h1 class="m-0 text-dark">
        
    </h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
        <div class="card-header" style="background-color: #fe495f; color: white;">
            <i class="fas fa-user-edit mr-2"></i> Registrar Nuevo Usuario
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
                <form action="{{ route('register.store') }}" method="POST">
                    @csrf

                    <!-- Nombre -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name" style="color: #fe495f;">Nombre</label>
                            <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user" style="color:rgb(245, 114, 129);"></i></span>
                            </div>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nombre" required value="{{ old('name') }}">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="last_name" style="color: #fe495f;">Apellido</label>
                            <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user" style="color:rgb(245, 114, 129);"></i></span>
                            </div>
                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Apellido" required value="{{ old('last_name') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" style="color: #fe495f;">Correo Electrónico</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope" style="color:rgb(245, 114, 129);"></i></span>
                            </div>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@grobdi.com" required value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password" style="color: #fe495f;">Contraseña</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock" style="color:rgb(245, 114, 129);"></i></span>
                            </div>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
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
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repita la contraseña" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-confirm-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Rol -->
                    <div class="form-group">
                        <label for="role_id" style="color: #fe495f;">Rol</label>
                        <div class="input-group">
                        <div class="input-group-prepend">
                             <span class="input-group-text"><i class="fas fa-users" style="color:rgb(245, 114, 129);"></i></span>
                         </div>
                        <select name="role_id" id="role_id" class="form-control" required>
                            <option value="" disabled selected>Seleccione un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} - {{ $role->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CMP (solo visible si el rol es Doctor) -->
                <div class="form-group" id="cmp_group" style="display: none;">
                        <label for="cmp" style="color: #fe495f;">CMP</label>
                        <div class="input-group  mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-id-card" style="color:rgb(245, 114, 129);"></i></span>
                            </div>
                            <input type="text" name="cmp" id="cmp" class="form-control" placeholder="Ingrese el CMP">
                        </div>
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
                                    <option value="{{ $visitadora->id }}" {{ old('visitadora_id') == $visitadora->id ? 'selected' : '' }}>
                                        {{ $visitadora->name }} {{ $visitadora->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-default mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn" style="background-color: #fe495f; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold;">
                            <i class="fas fa-save mr-1"></i> Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .form-group input:focus,
        .form-group select:focus {
            border-color: #fe495f;
            box-shadow: 0 0 5px rgba(254, 73, 95, 0.5);
        }

        .card-header {
            font-weight: bold;
        }
        .btn:hover {
            transform: scale(1.05); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.2s ease; /* Transición suave */
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Verificar si el rol seleccionado al cargar la página es "Doctor"
        if (roleSelect.value == '4') {
            cmpGroup.style.display = 'block'; // Mostrar el campo CMP si el rol es Doctor
        }

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
    });
</script>
@stop
