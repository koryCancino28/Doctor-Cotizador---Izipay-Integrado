@extends('adminlte::page')

@section('title', 'Detalle de Formulación')

@section('content_header')
@stop

@section('content')
    <div class="container mt-2" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold; padding-bottom: 8px;">Detalle de Formulación</h1>
        <div class="row">
            <!-- Información Básica Card -->
            <div class="col-md-6 mb-4">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header" style="background-color: #fe495f; color: white;">
                        <h5><i class="bi bi-info-circle" style="margin-right: 6px;"></i> Información Básica</h5>
                    </div>
                    <div class="card-body">
                        <p><strong style="color:rgb(224, 61, 80);">Item:</strong> {{ $formulacione->item }}</p>
                        <p><strong style="color:rgb(224, 61, 80);">Nombre:</strong> {{ $formulacione->name }}</p>
                        <p><strong style="color:rgb(224, 61, 80);">Cliente:</strong> {{ $formulacione->cliente->nombre }}</p>
                    </div>
                </div>
            </div>

            <!-- Precios Card -->
            <div class="col-md-6 mb-4">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header" style="background-color: #fe495f; color: white;">
                        <h5><i class="bi bi-cash" style="margin-right: 6px;"></i> Precios</h5>
                    </div>
                    <div class="card-body">
                        <p><strong style="color:rgb(224, 61, 80);">Precio Público:</strong> ${{ number_format($formulacione->precio_publico, 2) }}</p>
                        <p><strong style="color:rgb(224, 61, 80);">Precio Médico:</strong> ${{ number_format($formulacione->precio_medico, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('formulaciones.index') }}" class="btn" style="background-color: #6c757d; color: white; border-radius: 8px; font-size: 1.1rem; padding: 10px 25px;">
                <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
            <a href="{{ route('formulaciones.edit', $formulacione) }}" class="btn" style="background-color: #fe495f; color: white; font-size: 1.1rem; padding: 12px 30px; border-radius: 8px;">
                <i class="bi bi-pencil"></i> Editar Formulación
            </a>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-title {
            font-size: 1.3rem;
            color: #fe495f;
            font-weight: bold;
        }

        .btn {
        font-size: 1rem; /* Tamaño de texto */
        padding: 8px 20px; /* Padding para que los botones sean más amplios */
        border-radius: 8px; /* Bordes redondeados */
        display: flex; /* Asegura que los iconos y texto estén alineados */
        align-items: center; 
        }

        .btn i {
            margin-right: 5px; /* Espaciado entre el icono y el texto */
        }
        .card-body {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
        }

        .card-header {
            background-color: #fe495f;
            color: white;
            font-size: 1.2rem;
        }

        .card-footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: right;
            border-radius: 0 0 10px 10px;
        }
        .btn:hover {
            transform: scale(1.10); 
            transition: transform 0.4s ease; 
        }
        .d-flex.justify-content-between {
        gap: 35px; /* Espacio entre botones */
        }
    </style>
@stop

@section('js')
    <script> console.log('Detalle de Formulación cargado'); </script>
@stop
