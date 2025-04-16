<?php

use Illuminate\Support\Facades\Route;
// routes/web.php

use App\Http\Controllers\Auth\RegisterController;


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//CRUD PARA REGISTRAR A UN DOCTOR

Route::resource('usuarios', RegisterController::class);