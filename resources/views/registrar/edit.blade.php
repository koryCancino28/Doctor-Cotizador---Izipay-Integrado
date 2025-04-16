{{-- resources/views/registrar/edit.blade.php --}}
@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
@stop

@section('content')
    <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <!-- Selección de rol -->
        <div class="form-group">
            <label for="role_id">Rol</label>
            <select name="role_id" id="role_id" class="form-control" required>
                <option value="" disabled>Selecciona el rol</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" 
                            @if(old('role_id', $user->role_id) == $role->id) selected @endif>
                        {{ $role->name }} - {{ $role->description }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </div>
    </form>
@stop
