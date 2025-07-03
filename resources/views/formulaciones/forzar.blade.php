@extends('adminlte::page')

@section('title', 'Detalle de Formulación')

@section('content_header')
<div></div>
@stop

@section('content')
<div class="container mt-2" style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">¡Atención!</h1>
    <div class="alert" style="background-color:rgb(255, 155, 155); color: #ffffff; font-weight: bold;">
        <h4><i class="bi bi-exclamation-triangle-fill"></i> 
        Esta formulación está asociada a {{ $formulacione->detalleCotizaciones->count() }} cotizaciones.
    <i class="bi bi-exclamation-triangle-fill"></i></h4>
        <h5>¿Desea eliminar también TODAS las relaciones?</h5>
    </div>
    
    <form action="{{ route('formulaciones.forceDestroy', $formulacione) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash3-fill"></i>
            Eliminar todo
        </button>
        <a href="{{ route('formulaciones.index') }}" class="btn btn-secondary"><i class="bi bi-x-octagon-fill"></i>
            Cancelar
        </a>
    </form>
</div>
@stop
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .btn i {
            margin-right: 5px; /* Espaciado entre el icono y el texto */
        }
        .btn:hover {
            transform: scale(1.03); 
            transition: transform 0.2s ease; 
        }
    </style>
@stop