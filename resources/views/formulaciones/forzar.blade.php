@extends('adminlte::page')

@section('title', 'Detalle de Formulación')

@section('content_header')
<div></div>
@stop

@section('content')
<div class="container mt-2" style="background-color: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.07);">
    <h1 class="text-center" style="color: #fe495f; font-weight: bold;">⚠ ¡Atención!</h1>

    <div class="alert d-flex flex-column justify-content-center align-items-center text-center" style="background-color:rgba(247, 154, 164, 0.56); color: rgb(124, 122, 122); font-weight: bold; border-radius: 8px;">
        <h4>
            <i class="bi bi-exclamation-triangle-fill"></i>
            Esta formulación está asociada a {{ $formulacione->detalleCotizaciones->count() }} cotizaciones.
            <i class="bi bi-exclamation-triangle-fill"></i>
        </h4>
        <h5>¿Desea eliminar también <u>TODAS</u> las relaciones?</h5>
    </div>

    <form action="{{ route('formulaciones.forceDestroy', $formulacione) }}" method="POST" class="text-center mt-4">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-danger" style="background-color: #fe495f; border: none;">
            <i class="bi bi-trash3-fill"></i> Eliminar todo
        </button>

        <a href="{{ route('formulaciones.index') }}" class="btn btn-secondary ms-2">
            <i class="bi bi-x-octagon-fill"></i> Cancelar
        </a>
    </form>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .btn i {
        margin-right: 5px;
    }

    .btn:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
        opacity: 0.9;
    }

    .btn-danger:hover {
        background-color: #e84356 !important;
    }

    .alert h4, .alert h5 {
        margin: 10px 0;
    }
</style>
@stop
