@extends('adminlte::page')

@section('title', 'Registrar Usuarios')

@section('content_header')
    <h1>Registrar Usuarios</h1>
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
{{-- Register link --}}
    @if($registerUrl)
        <p class="my-0">
            <a href="{{ $registerUrl }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Fecha de Creación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name ?? 'Sin rol' }}</td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <!-- Botón para editar -->
                    <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    
                    <!-- Formulario para eliminar -->
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
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop