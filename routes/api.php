<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TCisternaController;
use App\Http\Controllers\API\AuthenticationController;

Route::post('register', [AuthenticationController::class, 'register'])->name('register');
Route::post('login', [AuthenticationController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {

     Route::get('/cisternas', [TCisternaController::class, 'index']);//cisternas por usuario
    
    Route::get('/cisternas/{id}', [TCisternaController::class, 'show']);//primer dato
    Route::post('/cisternas', [TCisternaController::class, 'store']);//guardar un dato

    Route::get('/cisterna/{cisterna_id}/ultimo', [TCisternaController::class, 'ultimoDato']);//ultimo dato
    Route::get('/cisterna/{cisterna_id}/dia', [TCisternaController::class, 'dia']);//datos de ultimas 24 horas, para consulta dia
    Route::get('/cisterna/{cisterna_id}/ultimos7dias', [TCisternaController::class, 'ultimos7dias']);//datos de ultimos 7 dia, para consulta semana
    Route::get('/cisterna/{cisterna_id}/historico', [TCisternaController::class, 'historico']);//datos historicos
     Route::get('/dimensiones/{cisterna_id}', [TCisternaController::class, 'dimensiones']);//dimensiones de cisterna


    Route::get('/get-user', [AuthenticationController::class, 'userInfo']);//info de usuarios para debug
    Route::post('/logout', [AuthenticationController::class, 'logOut']);//eliminar token

   

});







