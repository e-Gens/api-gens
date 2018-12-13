<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('/login', 'UserController@login')->name('login');

Route::post('/usuario/create', 'UserController@create')->name('usuario.create');

Route::middleware('auth:api')->put('/usuario/edit', 'UserController@edit')->name('usuario.edit');



/* Faz a verificação de login com o token do usuário, retornando o usuário*/
Route::middleware('auth:api')->get('/usuario', function (Request $request) {
    return $request->user();
});
