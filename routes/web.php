<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Agregar cliente
Route::post('new_client', 'ClienteController@store')->name('new_client');
//Buscar cliente
Route::get('/mostrarClientes', 'ClienteController@show')->name('mostrarClientes');


//REMISIONES
//Buscar remision
Route::get('lista_datos', 'RemisionController@show')->name('lista_datos');
//Crear remision
Route::post('crear_remision', 'RemisionController@store')->name('registro_remision');
//Crear registro de remisión
Route::post('registro_remision', 'RemisionController@registro')->name('registro_remision');
//Eliminar registro de remision
Route::delete('eliminar_registro', 'RemisionController@eliminar')->name('eliminar_registro');
//Actualizar remision y registros
Route::put('actualizar_remision', 'RemisionController@actualizar')->name('actualizar_remision');

//REMISIONES -Listado
Route::get('todos_los_clientes', 'RemisionController@todos')->name('todos_los_clientes');
Route::get('buscar_por_numero', 'RemisionController@por_numero')->name('buscar_por_numero');
Route::get('buscar_por_cliente', 'RemisionController@por_cliente')->name('buscar_por_cliente');
Route::get('buscar_por_fecha', 'RemisionController@por_fecha')->name('buscar_por_fecha');

//DEVOLUCIONES
//Mostrar devoluciones
Route::get('devoluciones_remision', 'DevolucioneController@show')->name('devoluciones_remision');
//Actualizar devolcuion
Route::put('actualizar_unidades', 'DevolucioneController@actualizar')->name('actualizar_unidades');
//Concluir remision
Route::put('concluir_remision', 'DevolucioneController@concluir')->name('concluir_remision');


//Agregar libro
Route::post('new_libro', 'LibroController@store')->name('new_libro');
//Buscar libro
Route::get('/mostrarLibros', 'LibroController@buscar')->name('mostrarLibros');
//Datos del libro
Route::get('/buscarISBN', 'LibroController@show')->name('buscarISBN');

Route::get('/imprimirSalida', 'RemisionController@imprimirSalida')->name('imprimirSalida');
