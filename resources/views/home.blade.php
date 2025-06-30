@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="text-white text-center py-4 bg-transparent">
    </div>
@stop

@section('content')
    <div class="welcome-card animate__animated animate__fadeInUp" style="max-width: 900px; margin: auto;">
        <div class="text-center">
            <img src="{{ asset('favicons/favicon.ico.ico') }}" alt="Icono laboratorio" style="width: 30px; margin-bottom: 10px;">
            <h1 class="display-5 fw-bold text-grobdi">¡Bienvenido a <span class="ki">Grobdi</span>!</h1>
            <p class="lead">Solo para ti</p>
            <hr class="divider my-4">
            <p class="text-muted">Desde aquí puedes gestionar formulaciones, pedidos y cotizaciones registradas.</p>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        /* Fondo general con superposición */
        .content-wrapper {
            background: linear-gradient(rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.25)), 
                        url("{{ asset('images/gomas.jpeg') }}") no-repeat center center fixed;
            background-size: cover;
            padding: 40px 20px;
            user-select: none;
        }

        .welcome-card {
            background-color: rgba(255, 244, 244, 0.58);
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
            padding: 40px;
            transition: transform 0.3s ease-in-out;
        }

        .welcome-card:hover {
            transform: translateY(-5px);
        }

        .ki {
            color: #FC0000;
        }

        .text-grobdi {
            color: #202020;
        }

        .divider {
            width: 60px;
            height: 4px;
            background-color: #FC0000;
            border: none;
            margin: 20px auto;
        }

        .lead {
            font-size: 1.25rem;
            color: #555;
        }
    </style>
@stop

@section('js')
    <script>
        console.log("Realizado por Kory Cancino Arias");
    </script>
@stop
