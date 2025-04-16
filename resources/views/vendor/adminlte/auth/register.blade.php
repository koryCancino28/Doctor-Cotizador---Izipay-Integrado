@extends('adminlte::page')

@section('title', 'Registrar Usuarios')

@section('content_header')
    <h1>Registrar Usuarios</h1>
@stop

@section('content')
<form action="{{ route('register.store') }}" method="POST">
    @csrf

    <!-- Nombre del usuario -->
    <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Nombre completo" required>
    </div>

    <!-- Correo electrónico -->
    <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Correo electrónico" required>
    </div>

    <!-- Contraseña -->
    <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
    </div>

    <!-- Confirmar contraseña -->
    <div class="form-group">
        <label for="password_confirmation">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirmar contraseña" required>
    </div>

    <!-- Selección de rol -->
    <div class="form-group">
        <label for="role_id">Rol</label>
        <select name="role_id" id="role_id" class="form-control" required>
            <option value="" disabled selected>Selecciona el rol</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }} - {{ $role->description }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Registrar</button>
</form>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
