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
})->name('inicio')->middleware('auth', 'role:2');

Route::get('/devolucion', function () {
    return view('/devolucion');
})->name('devolucion')->middleware('auth', 'role:3');

Route::get('/reset_password', function () {
    return view('/reset_password');
})->name('reset_password');
 
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
//Llenar tabla de vendidos
Route::put('vendidos_remision', 'RemisionController@registrar_vendidos')->name('vendidos_remision');
//Cancelar remision
Route::put('cancelar_remision', 'RemisionController@cancelar_remision')->name('cancelar_remision');
//Guardar deposito de remision
Route::post('deposito_remision', 'RemisionController@deposito_remision')->name('deposito_remision');
//Aplicar descuento a la remision
// Route::put('aplicar_descuento', 'RemisionController@aplicar_descuento')->name('aplicar_descuento');

//REMISIONES -Listado
Route::get('get_iniciados', 'RemisionController@get_iniciados')->name('get_iniciados');
Route::get('todos_los_clientes', 'RemisionController@todos')->name('todos_los_clientes');
Route::get('buscar_por_numero', 'RemisionController@por_numero')->name('buscar_por_numero');
Route::get('buscar_por_cliente', 'RemisionController@por_cliente')->name('buscar_por_cliente');
Route::get('buscar_por_fecha', 'RemisionController@por_fecha')->name('buscar_por_fecha');
Route::get('buscar_por_estado', 'RemisionController@por_estado')->name('buscar_por_estado');
Route::get('buscar_por_estado_libros', 'RemisionController@por_estado_libros')->name('buscar_por_estado_libros');

//Obtener todas las unidades pendientes
Route::get('obtener_vendidos', 'RemisionController@obtener_vendidos')->name('obtener_vendidos');
//Obtener por fecha
Route::get('obtener_por_fecha', 'RemisionController@obtener_por_fecha')->name('obtener_por_fecha');
//Obtener detalles de vendidos
Route::get('detalles_vendidos', 'RemisionController@detalles_vendidos')->name('detalles_vendidos');

//REMISIONES -Imprimir
Route::get('/imprimirSalida/{id}', 'RemisionController@imprimirSalida')->name('imprimirSalida');
Route::get('/imprimirCliente/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirCliente')->name('imprimirCliente');
Route::get('/imprimirEstado/{estado}', 'RemisionController@imprimirEstado')->name('imprimirEstado');

//DEVOLUCIONES
//Mostrar todos los registros de devoluciones
Route::get('all_devoluciones', 'DevolucioneController@all_devoluciones')->name('all_devoluciones');
//Mostrar los datos de una devolución
Route::get('datos_devolucion', 'DevolucioneController@datos_devolucion')->name('datos_devolucion');
//Mostrar todos las devoluciones con los libros
Route::get('todos_los_libros', 'DevolucioneController@todos')->name('todos_los_libros');
//Mostrar devoluciones
Route::get('devoluciones_remision', 'DevolucioneController@registrar_datos')->name('devoluciones_remision');
//Actualizar devolcuion
Route::put('actualizar_unidades', 'DevolucioneController@actualizar')->name('actualizar_unidades');
//Concluir remision
Route::put('concluir_remision', 'DevolucioneController@concluir')->name('concluir_remision');

//LIBROS
//Agregar libro
Route::post('new_libro', 'LibroController@store')->name('new_libro');
//Actualizar libro
Route::put('actualizar_libro', 'LibroController@update')->name('actualizar_libro');
//Eliminar libro
Route::delete('eliminar_libro', 'LibroController@delete')->name('eliminar_libro');
//Buscar libro
Route::get('/mostrarLibros', 'LibroController@buscar')->name('mostrarLibros');
//Buscar libro por editorial
Route::get('/mostrarPorEditorial', 'LibroController@porEditorial')->name('mostrarPorEditorial');
//Datos del libro
Route::get('/buscarISBN', 'LibroController@show')->name('buscarISBN'); 
//Obtener todos los libros
Route::get('allLibros', 'LibroController@allLibros')->name('allLibros');
//Descargar formato de todos los libros
Route::get('descargarLibros', 'LibroController@descargarLibros')->name('descargarLibros');

//ENTRADAS
//Mostrar todas las entradas
Route::get('all_entradas', 'EntradaController@show')->name('all_entradas');
//Buscar editorial
Route::get('/mostrarEditoriales', 'EntradaController@mostrarEditoriales')->name('mostrarEditoriales');
//Buscar folio
Route::get('/buscarFolio', 'EntradaController@buscarFolio')->name('buscarFolio');
//Mostrar todas las entradas
Route::get('detalles_entrada', 'EntradaController@detalles_entrada')->name('detalles_entrada');
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
//Actualizar costos unitarios
Route::put('actualizar_costos', 'EntradaController@actualizar_costos')->name('actualizar_costos');
//Mostrarentradas por fecha
Route::get('fecha_entradas', 'EntradaController@fecha_entradas')->name('fecha_entradas');
//Mostrarentradas por fecha
Route::put('pago_entrada', 'EntradaController@pago_entrada')->name('pago_entrada');

//PAGOS
//Guardar pago
Route::post('registrar_pago', 'PagoController@store')->name('registrar_pago');
//Obtener registros de vendidos
Route::get('datos_vendidos', 'PagoController@datos_vendidos')->name('datos_vendidos');
//Buscar pagos por cliente
Route::get('/all_pagos', 'PagoController@all_pagos')->name('all_pagos');
//Buscar pagos por numero de remision
Route::get('/num_pagos', 'PagoController@num_pagos')->name('num_pagos');

//NOTA
//Mostrar notas
Route::get('all_notas', 'NoteController@show')->name('all_notas');
//Guardar nota
Route::post('guardar_nota', 'NoteController@store')->name('guardar_nota');
//Actualizar nota
Route::post('actualizar_nota', 'NoteController@update')->name('actualizar_nota');
//Mostrar detalles de nota
Route::get('detalles_nota', 'NoteController@detalles_nota')->name('detalles_nota');
//Guardar pago de la nota
Route::post('guardar_pago', 'NoteController@guardar_pago')->name('guardar_pago');
//Guardar devolucion
Route::post('guardar_devolucion', 'NoteController@guardar_devolucion')->name('guardar_devolucion');

//ADEUDO
//Guardar adeudo
Route::post('guardar_adeudo', 'AdeudoController@store')->name('guardar_adeudo');
//Mostrar adeudos
Route::get('obtener_adeudos', 'AdeudoController@show')->name('obtener_adeudos');
//Guardar abono
Route::post('guardar_abono', 'AdeudoController@guardar_abono')->name('guardar_abono');
//Obtener adeudos de un cliente
Route::get('adeudos_cliente', 'AdeudoController@adeudos_cliente')->name('adeudos_cliente');
//Detalles de un adeudo
Route::get('detalles_adeudo', 'AdeudoController@detalles_adeudo')->name('detalles_adeudo');
//Buscar remision
Route::get('/buscarRemision', 'AdeudoController@buscarRemision')->name('buscarRemision');
//Guardar devolucion
Route::put('guardar_adeudo_devolucion', 'AdeudoController@guardar_adeudo_devolucion')->name('guardar_adeudo_devolucion');
//Buscar adeudo
Route::get('/buscar_adeudo', 'AdeudoController@buscar_adeudo')->name('buscar_adeudo');

//PROMOCION
//Guardar promocion
Route::post('guardar_promocion', 'PromotionController@store')->name('guardar_promocion');
//Mostrar promociones
Route::get('obtener_promociones', 'PromotionController@show')->name('obtener_promociones');
//Mostrar departures
Route::get('obtener_departures', 'PromotionController@obtener_departures')->name('obtener_departures');