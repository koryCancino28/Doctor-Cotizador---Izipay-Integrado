@extends('adminlte::page')

@section('title', 'Cotizador')

@section('content_header')
<div></div>
@stop

@section('content')
<div class="" style="background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <div class="form-check mb-3 d-flex align-items-center justify-content-center position-relative">
        <a href="{{ route('formulaciones.index') }}" class="text-secondary" title="Volver" style="position: absolute; left: 0; font-size: 2rem">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-center" style="color: #fe495f; font-weight: bold;">Editar<br>Formulación</h1>
    </div>

    <form action="{{ route('formulaciones.update', $formulacione) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="item" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Item</label>
                <input type="text" class="form-control @error('item') is-invalid @enderror" id="item" name="item" value="{{ $formulacione->item }}" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
                @error('item')
                    <div style="color:rgb(199, 0, 0);" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label for="name" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $formulacione->name }}" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="precio_publico" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Precio Público</label>
                <input type="number" step="0.01" class="form-control" id="precio_publico" name="precio_publico" value="{{ $formulacione->precio_publico }}" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
            </div>

            <div class="form-group col-md-6">
                <label for="precio_medico" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Precio Médico</label>
                <input type="number" step="0.01" class="form-control" id="precio_medico" name="precio_medico" value="{{ $formulacione->precio_medico }}" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
            </div>
        </div>

        <div class="form-group">
            <label for="cliente_id" class="font-weight-bold" style="font-size: 1.1rem; color: #fe495f;">Doctor</label>
            <select name="cliente_id" id="cliente_id" class="form-control form-control-lg select2" required style="border-color: #fe495f; box-shadow: 0 0 5px rgba(254, 73, 95, 0.3);">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $formulacione->cliente_id == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }} ({{ $cliente->cmp }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-danger" style="font-size: 1.1rem; padding: 12px 30px; border-radius: 8px; background-color: #fe495f; color: white;">
                <i class="bi bi-save"></i>Actualizar Formulación
            </button>
        </div>
    </form>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-group label {
            font-size: 1.1rem;
            color: #fe495f;
        }

        .btne {
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
