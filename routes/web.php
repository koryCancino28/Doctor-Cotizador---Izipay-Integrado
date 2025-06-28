<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FormulacionController;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//CRUD PARA REGISTRAR A UN DOCTOR
Route::resource('usuarios', RegisterController::class)
    ->middleware('checkRole:Admin,Jefe Proyecto'); 

Route::get('/registrar', [RegisterController::class, 'showRegistrationForm'])
    ->name('register.show')
    ->middleware('checkRole:Admin,Jefe Proyecto'); 

Route::post('/registrar', [RegisterController::class, 'register'])
    ->name('register.store')
    ->middleware('checkRole:Admin,Jefe Proyecto'); 

//CLIENTE
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index')->middleware('checkRole:Admin,Visitadora Medica');
Route::post('/confirmacion', [ClienteController::class, 'store'])->name('confirmacion.store');


//cotizacion
Route::get('/cotizacion', [CotizacionController::class, 'index'])->name('cotizacion.index')->middleware('checkRole:Doctor'); 
Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizacion.store');

//FORMULACION CRUD
Route::resource('formulaciones', FormulacionController::class)
    ->parameters(['formulaciones' => 'formulacione'])->middleware('checkRole:Admin, Jefe Proyecto'); 

//COTIZACIONES DE CADA DOCTOR
Route::middleware(['auth'])->group(function () {
    Route::get('mis-cotizaciones', [CotizacionController::class, 'misCotizaciones'])->name('cotizacion.mis');
});
Route::delete('/formulaciones/{formulacione}/force', [FormulacionController::class, 'forceDestroy'])
     ->name('formulaciones.forceDestroy');