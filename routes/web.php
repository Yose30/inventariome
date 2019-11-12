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

// GESTOR
Route::name('gestor.')->prefix('gestor')->middleware(['auth', 'role:Gestor'])->group(function () {
    Route::get('/remisiones', 'GestorController@remisiones')->name('remisiones');    
});

// ADMINISTRADOR
Route::name('administrador.')->prefix('administrador')->middleware(['auth', 'role:Administrador'])->group(function () {
    Route::get('/remisiones', 'AdministradorController@remisiones')->name('remisiones');
    Route::get('/vendidos', 'AdministradorController@vendidos')->name('vendidos');
    Route::get('/notas', 'AdministradorController@notas')->name('notas');
    Route::get('/promociones', 'AdministradorController@promociones')->name('promociones');
    Route::get('/adeudos', 'AdministradorController@adeudos')->name('adeudos');
    Route::get('/entradas', 'AdministradorController@entradas')->name('entradas');
    Route::get('/libros', 'AdministradorController@libros')->name('libros');
    Route::get('/clientes', 'AdministradorController@clientes')->name('clientes');
    
});

Route::name('oficina.')->prefix('oficina')->middleware(['auth', 'role:Oficina'])->group(function () {
    Route::get('/remisiones', 'OficinaController@remisiones')->name('remisiones');
    Route::get('/remision', 'OficinaController@remision')->name('remision');
    Route::get('/pagos', 'OficinaController@pagos')->name('pagos');
    Route::get('/clientes', 'OficinaController@clientes')->name('clientes');
    Route::get('/libros', 'OficinaController@libros')->name('libros');
    Route::get('/adeudos', 'OficinaController@adeudos')->name('adeudos');
    Route::get('/entradas', 'OficinaController@entradas')->name('entradas');
});

// ALMACEN
Route::name('almacen.')->prefix('almacen')->middleware(['auth', 'role:Almacen'])->group(function () {
    Route::get('/entregas', 'AlmacenController@entregas')->name('entregas');
    Route::get('/pagos', 'AlmacenController@pagos')->name('pagos');
    Route::get('/remisiones', 'AlmacenController@remisiones')->name('remisiones');
    Route::get('/notas', 'AlmacenController@notas')->name('notas');
    Route::get('/promociones', 'AlmacenController@promociones')->name('promociones');
    Route::get('/adeudos', 'AlmacenController@adeudos')->name('adeudos');
    Route::get('/entradas', 'AlmacenController@entradas')->name('entradas');
    Route::get('/libros', 'AlmacenController@libros')->name('libros');
});

//CLIENTES
//Agregar cliente
Route::post('/new_client', 'ClienteController@store')->name('new_client');
//Buscar cliente
Route::get('/mostrarClientes', 'ClienteController@show')->name('mostrarClientes');
//Editar informacion de cliente
Route::put('editar_cliente', 'ClienteController@editar')->name('editar_cliente');
//Obtener todos los cliente
Route::get('/getTodo', 'ClienteController@getTodo')->name('getTodo'); 

//REMISIONES
//Buscar remision
Route::get('lista_datos', 'RemisionController@show')->name('lista_datos');
///Crear remision
Route::post('crear_remision', 'RemisionController@store')->name('crear_remision');
//Llenar tabla de vendidos
Route::put('vendidos_remision', 'RemisionController@registrar_vendidos')->name('vendidos_remision');
//Cancelar remision
Route::put('cancelar_remision', 'RemisionController@cancelar_remision')->name('cancelar_remision');
//Guardar deposito de remision
Route::post('deposito_remision', 'RemisionController@deposito_remision')->name('deposito_remision');

//REMISIONES -Listado
Route::get('todos_los_clientes', 'RemisionController@todos')->name('todos_los_clientes');
Route::get('buscar_por_numero', 'RemisionController@por_numero')->name('buscar_por_numero');
Route::get('buscar_por_cliente', 'RemisionController@por_cliente')->name('buscar_por_cliente');
Route::get('buscar_por_fecha', 'RemisionController@por_fecha')->name('buscar_por_fecha');
Route::get('buscar_por_estado', 'RemisionController@por_estado')->name('buscar_por_estado');

//Obtener todas las unidades pendientes
Route::get('obtener_vendidos', 'RemisionController@obtener_vendidos')->name('obtener_vendidos');
//Obtener por fecha
Route::get('obtener_por_fecha', 'RemisionController@obtener_por_fecha')->name('obtener_por_fecha');
//Obtener detalles de vendidos
Route::get('detalles_vendidos', 'RemisionController@detalles_vendidos')->name('detalles_vendidos');

//REMISIONES - Descargar
Route::get('/imprimirSalida/{id}', 'RemisionController@imprimirSalida')->name('imprimirSalida');

// DESCARGAR POR CLIENTE PDF
Route::get('/imprimirCliente/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirCliente')->name('imprimirCliente');
// DESCARGAR POR CLIENTE EXCEL
Route::get('/imprimirClienteEXC/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirClienteEXC')->name('imprimirClienteEXC');
// DESCARGAR POR CLIENTE DETALLADO EXCEL
Route::get('/imprimirClienteDet/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirClienteDet')->name('imprimirClienteDet');

// DESCARGAR POR FECHA PDF
Route::get('/imprimirFecha/{inicio}/{final}', 'RemisionController@imprimirFecha')->name('imprimirFecha');
// DESCARGAR POR FECHA EXCEL
Route::get('/imprimirFechaEXC/{inicio}/{final}', 'RemisionController@imprimirFechaEXC')->name('imprimirFechaEXC');
// DESCARGAR POR FECHA EXCEL
Route::get('/imprimirFechaDet/{inicio}/{final}', 'RemisionController@imprimirFechaDet')->name('imprimirFechaDet');

// DESCARGAR POR ESTADO PDF
Route::get('/imprimirEstado/{estado}/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirEstado')->name('imprimirEstado');
// DESCARGAR POR ESTADO EXCEL
Route::get('/imprimirEstadoEXC/{estado}/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirEstadoEXC')->name('imprimirEstadoEXC');
// DESCARGAR POR ESTADO DETALLADO EXCEL
Route::get('/imprimirEstadoDet/{estado}/{cliente_id}/{inicio}/{final}', 'RemisionController@imprimirEstadoDet')->name('imprimirEstadoDet');

//DEVOLUCIONES
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
//Mostrar libros vendidos buscados por editorial
Route::get('porEditorialVendidos', 'LibroController@porEditorialVendidos')->name('porEditorialVendidos');
// Descargar en formato excel todos los libros
Route::get('/downloadExcel/{editorial}', 'LibroController@downloadExcel')->name('downloadExcel');

//ENTRADAS
//Buscar editorial
Route::get('/mostrarEditoriales', 'EntradaController@mostrarEditoriales')->name('mostrarEditoriales');
//Buscar folio
Route::get('/buscarFolio', 'EntradaController@buscarFolio')->name('buscarFolio'); 
//Mostrar todas las entradas
Route::get('detalles_entrada', 'EntradaController@detalles_entrada')->name('detalles_entrada');
///Crear remision
Route::post('crear_entrada', 'EntradaController@store')->name('crear_entrada');
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

//NOTA
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
// Buscar nota por folio
Route::get('buscar_folio_note', 'NoteController@buscar_folio')->name('buscar_folio_note');
// Buscar por cliente
Route::get('buscar_cliente_notes', 'NoteController@buscar_cliente_notes')->name('buscar_cliente_notes');

//ADEUDO
//Guardar adeudo
Route::post('guardar_adeudo', 'AdeudoController@store')->name('guardar_adeudo');
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
//Mostrar departures
Route::get('obtener_departures', 'PromotionController@obtener_departures')->name('obtener_departures');
// Buscar promocion por folio
Route::get('buscar_folio_promo', 'PromotionController@buscar_folio')->name('buscar_folio_promo');
// Buscar promocion por plantel
Route::get('buscar_plantel', 'PromotionController@buscar_plantel')->name('buscar_plantel');

// DONACIONE
// GUARDAR DONACIONES
Route::post('guardar_donacion', 'DonacioneController@store')->name('guardar_donacion');