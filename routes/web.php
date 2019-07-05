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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth', 'role:1');

Route::get('/inicio', function () {
    return view('/inicio');
})->middleware('auth', 'role:2');

Route::get('/devolucion', function () {
    return view('/devolucion');
})->middleware('auth', 'role:3');


//CLIENTES
//Agregar cliente
Route::post('new_client', 'ClienteController@store')->name('new_client');
//Buscar cliente
Route::get('/mostrarClientes', 'ClienteController@show')->name('mostrarClientes');
//Editar informacion de cliente
Route::put('editar_cliente', 'ClienteController@editar')->name('editar_cliente');
//Obtener datos de un cliente
Route::get('/getCliente', 'ClienteController@getCliente')->name('getCliente');
//Obtener todos los cliente
Route::get('/getTodo', 'ClienteController@getTodo')->name('getTodo');


//REMISIONES
//Borrar los valores si no se concluyo una remision
Route::get('nueva_remision', 'RemisionController@nueva')->name('nueva_remision');
//Borrar los valores si no se concluyo una remision que estaba siendo editada
Route::get('nueva_edicion', 'RemisionController@nueva_edicion')->name('nueva_edicion');
//Buscar remision
Route::get('lista_datos', 'RemisionController@show')->name('lista_datos');
///Crear remision
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
Route::get('buscar_por_estado', 'RemisionController@por_estado')->name('buscar_por_estado');
Route::get('buscar_por_estado_libros', 'RemisionController@por_estado_libros')->name('buscar_por_estado_libros');


//REMISIONES -Imprimir
Route::get('/imprimirSalida/{id}', 'RemisionController@imprimirSalida')->name('imprimirSalida');
Route::get('/imprimirCliente/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirCliente')->name('imprimirCliente');
Route::get('/imprimirEstado/{estado}', 'RemisionController@imprimirEstado')->name('imprimirEstado');

//DEVOLUCIONES
//Mostrar todos las devoluciones con los libros
Route::get('todos_los_libros', 'DevolucioneController@todos')->name('todos_los_libros');
//Mostrar devoluciones
Route::get('devoluciones_remision', 'DevolucioneController@show')->name('devoluciones_remision');
//Actualizar devolcuion
Route::put('actualizar_unidades', 'DevolucioneController@actualizar')->name('actualizar_unidades');
//Concluir remision
Route::put('concluir_remision', 'DevolucioneController@concluir')->name('concluir_remision');

//LIBROS
//Agregar libro
Route::post('new_libro', 'LibroController@store')->name('new_libro');
//Buscar libro
Route::get('/mostrarLibros', 'LibroController@buscar')->name('mostrarLibros');
//Buscar libro por editorial
Route::get('/mostrarPorEditorial', 'LibroController@porEditorial')->name('mostrarPorEditorial');
//Datos del libro
Route::get('/buscarISBN', 'LibroController@show')->name('buscarISBN'); 
//Obtener todos los libros
Route::get('allLibros', 'LibroController@allLibros')->name('allLibros');

//ENTRADAS
//Borrar los valores si no se concluyo una remision
Route::get('nueva_entrada', 'EntradaController@nueva')->name('nueva_entrada');
///Crear remision
Route::post('crear_entrada', 'EntradaController@store')->name('crear_entrada');
//Crear registro de entrada
Route::post('registro_entrada', 'EntradaController@registro')->name('registro_entrada');
//Eliminar registro de remision
Route::delete('eliminar_registro_entrada', 'EntradaController@eliminar')->name('elimeliminar_registro_entradainar_registro');
//Actualizar entrada
Route::put('actualizar_entrada', 'EntradaController@actualizar')->name('actualizar_entrada');
//Imprimir entrada
Route::get('/imprimirEntrada/{id}', 'EntradaController@imprimirEntrada')->name('imprimirEntrada');
