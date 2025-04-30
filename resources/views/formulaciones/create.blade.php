@extends('adminlte::page')

@section('title', 'Crear Formulación')

@section('content_header')

@stop

@section('content')
<div class="container mt-2" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Crear Nueva Formulación</h1>

    <form action="{{ route('formulaciones.store') }}" method="POST">
        @csrf

        <!-- Campo de Item -->
        <div class="form-group mb-4">
            <label for="item" class="font-weight-bold" style="color: #fe495f;">Item</label>
            <input type="text" class="form-control form-control-lg @error('item') is-invalid @enderror" id="item" name="item" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
            @error('item')
                <div style="color:rgb(199, 0, 0);" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Campo de Nombre -->
        <div class="form-group mb-4">
            <label for="name" class="font-weight-bold" style="color: #fe495f;">Nombre</label>
            <input type="text" class="form-control form-control-lg" id="name" name="name" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <!-- Campo de Precio Público -->
        <div class="form-group mb-4">
            <label for="precio_publico" class="font-weight-bold" style="color: #fe495f;">Precio Público</label>
            <input type="number" step="0.01" class="form-control form-control-lg" id="precio_publico" name="precio_publico" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <!-- Campo de Precio Médico-->
        <div class="form-group mb-4">
            <label for="precio_medico" class="font-weight-bold" style="color: #fe495f;">Precio Médico</label>
            <input type="number" step="0.01" class="form-control form-control-lg" id="precio_medico" name="precio_medico" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
        </div>

        <!-- Campo de Cliente -->
        <div class="form-group mb-4">
            <label for="cliente_id" class="font-weight-bold" style="color: #fe495f;">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-control form-control-lg select2" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
                <option value="">Seleccionar Cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }} ({{ $cliente->cmp }})</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('formulaciones.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i>Volver</a>
            <button type="submit" class="btn" style="background-color: #fe495f; color: white; font-size: 1.1rem; padding: 12px 30px; border-radius: 8px;"><i class="fa-solid fa-floppy-disk"></i>Crear Formulación</button>
        </div>
    </form>
</div>
@stop
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .btn {
        font-size: 1rem; /* Tamaño de texto */
        padding: 8px 20px; /* Padding para que los botones sean más amplios */
        border-radius: 8px; /* Bordes redondeados */
        display: flex; /* Asegura que los iconos y texto estén alineados */
        align-items: center; /* Alinea verticalmente el icono con el texto */
        }

        .btn i {
            margin-right: 5px; /* Espaciado entre el icono y el texto */
        }
        .btn:hover {
            transform: scale(1.05); /* Un ligero aumento en tamaño cuando se pasa por encima */
            transition: transform 0.2s ease; /* Transición suave */
        }
        
        .select2-container .select2-selection--single {
            border: 1px solid #fe495f;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);
            height: 48px;
            padding: 8px 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #495057;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #fe495f;
            color: white;
        }

        .select2-dropdown {
            border-color: #fe495f;
            box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);
        }
    </style>
@stop
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cliente_id').select2({
                placeholder: "Seleccionar Doctor",
                allowClear: true,
                width: '100%' // asegura que se ajuste bien al contenedor
            });
        });
    </script>
@stop
