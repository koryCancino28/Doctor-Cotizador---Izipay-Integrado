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
                <form action="{{ route('register.store') }}" method="POST">
                    @csrf

                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="name" style="color: #fe495f;">Nombre Completo</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user" style="color:rgb(245, 114, 129);"></i></span>
                            </div>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nombre completo" required value="{{ old('name') }}">
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
});
</script>
@stop
