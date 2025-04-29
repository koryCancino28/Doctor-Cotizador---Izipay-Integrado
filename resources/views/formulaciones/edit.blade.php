@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">{{ isset($formulacione) ? 'Editar Formulación' : 'Crear Nueva Formulación' }}</h1>

    <form action="{{ route('formulaciones.update', $formulacione) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="item" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Item</label>
            <input type="text" class="form-control" id="item" name="item" value="{{ $formulacione->item }}"  required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <div class="form-group">
            <label for="name" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $formulacione->name }}"  required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <div class="form-group">
            <label for="precio_publico" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Precio Público</label>
            <input type="number" step="0.01" class="form-control" id="precio_publico" name="precio_publico" value="{{ $formulacione->precio_publico }}"  required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <div class="form-group">
            <label for="precio_medico" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Precio Médico</label>
            <input type="number" step="0.01" class="form-control" id="precio_medico" name="precio_medico" value="{{ $formulacione->precio_medico }}"  required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <div class="form-group">
            <label for="cliente_id" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-control"  required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $formulacione->cliente_id == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }} ({{ $cliente->cmp }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('formulaciones.index') }}" class="btn btn-secondary" style="background-color: #6c757d; color: white; border-radius: 8px; font-size: 1.1rem; padding: 10px 25px;">
                <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
            <button type="submit" class="btn btn-danger" style="font-size: 1.1rem; padding: 12px 30px; border-radius: 8px; background-color: #fe495f; color: white;">
            <i class="bi bi-save"></i>Actualizar Formulación
            </button>
        </div>
    </form>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-group label {
            font-size: 1.1rem;
            color: #fe495f;
        }

        .btn {
            font-size: 1rem;
            padding: 10px 25px;
            border-radius: 8px;
        }

        /* Estilos de la tabla */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table-bordered {
            border-color: #fe495f;
        }

        .table thead th {
            background-color: #fe495f;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        table td {
            background-color: rgb(255, 249, 249);
            text-align: center;
        }
        .btn i {
            margin-right: 5px;
        }
        .btn:hover {
            transform: scale(1.10); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.4s ease; /* Transición suave */
        }
        .d-flex.justify-content-between {
        gap: 35px; /* Espacio entre botones */
        }
    </style>
@stop

@section('js')
    <!-- Puedes agregar scripts personalizados aquí si lo necesitas -->
@stop
