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
    Route::get('/pedidos', 'AdministradorController@pedidos')->name('pedidos');
    Route::get('/donaciones', 'AdministradorController@donaciones')->name('donaciones');
    Route::get('/movimientos', 'AdministradorController@movimientos')->name('movimientos');
});

// OFICINA
Route::name('oficina.')->prefix('oficina')->middleware(['auth', 'role:Oficina'])->group(function () {
    Route::get('/remisiones', 'OficinaController@remisiones')->name('remisiones');
    Route::get('/pedidos', 'OficinaController@pedidos')->name('pedidos');
    Route::get('/pagos', 'OficinaController@pagos')->name('pagos');
    Route::get('/clientes', 'OficinaController@clientes')->name('clientes');
    Route::get('/libros', 'OficinaController@libros')->name('libros');
    Route::get('/adeudos', 'OficinaController@adeudos')->name('adeudos');
    Route::get('/entradas', 'OficinaController@entradas')->name('entradas');
    Route::get('/donaciones', 'OficinaController@donaciones')->name('donaciones');
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
    Route::get('/pedidos', 'AlmacenController@pedidos')->name('pedidos');
    Route::get('/donaciones', 'AlmacenController@donaciones')->name('donaciones');
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
// Detalles del cliente
Route::get('/detallesCliente', 'ClienteController@detallesCliente')->name('detallesCliente'); 

//REMISIONES
//Buscar remision
Route::get('lista_datos', 'RemisionController@show')->name('lista_datos');
///Crear remision
Route::post('crear_remision', 'RemisionController@store')->name('crear_remision');
//Llenar tabla de vendidos
Route::put('vendidos_remision', 'RemisionController@registrar_vendidos')->name('vendidos_remision');
//Cancelar remision
Route::put('cancelar_remision', 'RemisionController@cancelar_remision')->name('cancelar_remision');

// REMISIONES BUSQUEDA
// Buscar remisiones por cliente
Route::get('buscar_por_cliente', 'RemisionController@buscar_por_cliente')->name('buscar_por_cliente');
// Buscar remisiones por estado
Route::get('buscar_por_estado', 'RemisionController@buscar_por_estado')->name('buscar_por_estado');
// Buscar remisiones por fecha y cliente / estado
Route::get('buscar_por_fecha', 'RemisionController@buscar_por_fecha')->name('buscar_por_fecha');

//REMISIONES -Listado
Route::get('todos_los_clientes', 'RemisionController@todos')->name('todos_los_clientes');
Route::get('buscar_por_numero', 'RemisionController@por_numero')->name('buscar_por_numero');


//REMISIONES - Descargar
Route::get('/imprimirSalida/{id}', 'RemisionController@imprimirSalida')->name('imprimirSalida');

Route::get('/download_remision/{id}', 'RemisionController@download_remision')->name('download_remision');

// DESCARGAR TODO EN EXCEL DETALLADO
Route::get('/down_remisiones_excel/{cliente_id}/{inicio}/{final}/{estado}', 'RemisionController@down_remisiones_excel')->name('down_remisiones_excel');
// DESCARGAR TODO EN EXCEL GENERAL
Route::get('/down_gral_excel/{cliente_id}/{inicio}/{final}/{estado}', 'RemisionController@down_gral_excel')->name('down_gral_excel');
// DESCARGAR TODO EN PDF
Route::get('/down_remisiones_pdf/{cliente_id}/{inicio}/{final}/{estado}', 'RemisionController@down_remisiones_pdf')->name('down_remisiones_pdf');

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
// Mostrar libros por editorial
Route::get('/libros_por_editorial', 'LibroController@libros_por_editorial')->name('libros_por_editorial');
//Buscar libro por editorial
Route::get('/mostrarPorEditorial', 'LibroController@porEditorial')->name('mostrarPorEditorial');
//Datos del libro
Route::get('/buscarISBN', 'LibroController@show')->name('buscarISBN'); 
// Buscar libro por ISBN y editorial
Route::get('/isbn_por_editorial', 'LibroController@isbn_por_editorial')->name('isbn_por_editorial'); 
//Obtener todos los libros
Route::get('allLibros', 'LibroController@allLibros')->name('allLibros');
// Descargar en formato excel todos los libros
Route::get('/downloadExcel/{editorial}', 'LibroController@downloadExcel')->name('downloadExcel');
// Mostrar entradas por libro
Route::get('movimientos_todos', 'LibroController@movimientos_todos')->name('movimientos_todos');
// Mostrar entradas por libro
Route::get('movimientos_por_edit', 'LibroController@movimientos_por_edit')->name('movimientos_por_edit');
// Descargar movimientos por libro
Route::get('/download_movimientos/{editorial}/{tipo}', 'LibroController@download_movimientos')->name('download_movimientos');
// Mostrar libros por tipo
Route::get('obtener_movimientos', 'LibroController@obtener_movimientos')->name('obtener_movimientos');
// Obtener movimientos por fecha
Route::get('movimientos_por_fecha', 'LibroController@movimientos_por_fecha')->name('movimientos_por_fecha');
// Descargar movimientos por fecha y categoria
Route::get('/down_fechaCategoria/{incio}/{final}/{categoria}', 'LibroController@down_fechaCategoria')->name('down_fechaCategoria');


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
Route::get('/downloadEntrada/{id}', 'EntradaController@downloadEntrada')->name('downloadEntrada');
//Actualizar costos unitarios
Route::put('actualizar_costos', 'EntradaController@actualizar_costos')->name('actualizar_costos');
//Mostrarentradas por fecha
Route::get('fecha_entradas', 'EntradaController@fecha_entradas')->name('fecha_entradas');
//Mostrarentradas por fecha
Route::put('pago_entrada', 'EntradaController@pago_entrada')->name('pago_entrada');
// Descargar reporte de entradas en PDF
Route::get('/downEntradas/{inicio}/{final}/{editorial}', 'EntradaController@downEntradas')->name('downEntradas');
// Descargar reporte de entradas en EXCEL
Route::get('/downEntradasEXC/{inicio}/{final}/{editorial}/{tipo}', 'EntradaController@downEntradasEXC')->name('downEntradasEXC');


//PAGOS
//Guardar pago por unidades
Route::post('registrar_pago', 'PagoController@store')->name('registrar_pago');
//Guardar deposito de remision
Route::post('deposito_remision', 'PagoController@deposito_remision')->name('deposito_remision');
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
// Buscar notas por fecha
Route::get('buscar_fecha_notes', 'NoteController@buscar_fecha_notes')->name('buscar_fecha_notes');
// Descargar reporte de notas
Route::get('/download_note/{cliente}/{inicio}/{final}/{tipo}', 'NoteController@download_note')->name('download_note');
// Descargar nota
Route::get('/download_nota/{id}', 'NoteController@download_nota')->name('download_nota');

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
// Buscar promociones por fecha
Route::get('buscar_fecha_promo', 'PromotionController@buscar_fecha_promo')->name('buscar_fecha_promo');
// Descargar el reporte de promoción
Route::get('download_promotion/{plantel}/{inicio}/{final}/{tipo}', 'PromotionController@download_promotion')->name('download_promotion');
// Descargar nota de la promocion
Route::get('/download_promocion/{id}', 'PromotionController@download_promocion')->name('download_promocion');

// DONACIONE
// GUARDAR DONACIONES
Route::post('guardar_donacion', 'DonacioneController@store')->name('guardar_donacion');
// Obtener detalles de la donación
Route::get('detalles_donacion', 'DonacioneController@detalles_donacion')->name('detalles_donacion');
// Obtener donaciones por plantel
Route::get('buscar_plantel_regalo', 'DonacioneController@buscar_plantel_regalo')->name('buscar_plantel_regalo');
// Obtener donaciones por fecha
Route::get('buscar_fecha_regalo', 'DonacioneController@buscar_fecha_regalo')->name('buscar_fecha_regalo');
// Descargar el reporte de promoción
Route::get('download_donacion/{plantel}/{inicio}/{final}/{tipo}', 'DonacioneController@download_donacion')->name('download_donacion');
// Marcar como entregada la donación
Route::put('entrega_donacion', 'DonacioneController@entrega_donacion')->name('entrega_donacion');
// Descargar nota de la donacion
Route::get('/download_regalo/{id}', 'DonacioneController@download_regalo')->name('download_regalo');

// Guardar comentario de la remisión
Route::post('guardar_comentario', 'RemisionController@guardar_comentario')->name('guardar_comentario');

// VENDIDO
// Obtener todas los libros vendidos
Route::get('obtener_vendidos', 'VendidoController@obtener_vendidos')->name('obtener_vendidos');
// Obtener por fecha
Route::get('obtener_por_fecha', 'VendidoController@obtener_por_fecha')->name('obtener_por_fecha');
// Obtener unidades vendidas por libro
Route::get('obtener_libro', 'VendidoController@obtener_libro')->name('obtener_libro');
//Obtener por libros y fecha
Route::get('libro_por_fecha', 'VendidoController@libro_por_fecha')->name('libro_por_fecha');
// Obtener libros vendidos por cliente
Route::get('obtener_cliente', 'VendidoController@obtener_cliente')->name('obtener_cliente');
// Obtener libros vendidos por cliente y fecha
Route::get('cliente_por_fecha', 'VendidoController@cliente_por_fecha')->name('cliente_por_fecha');
// Obtener libros vendidos por editorial
Route::get('obtener_editorial', 'VendidoController@obtener_editorial')->name('obtener_editorial');
// Obtener por editorial y fecha
Route::get('editorial_por_fecha', 'VendidoController@editorial_por_fecha')->name('editorial_por_fecha');
// Obtener detalles de vendidos
Route::get('detalles_vendidos', 'VendidoController@detalles_vendidos')->name('detalles_vendidos');

// DESCARGAR REPORTES
// Descargar reporte de libros vendidos por cliente
Route::get('/downClienteEX/{cliente_id}/{fecha1}/{fecha2}', 'VendidoController@downClienteEX')->name('downClienteEX');
// Descargar reporte por libro
Route::get('/downLibroEX/{libro_id}/{fecha1}/{fecha2}', 'VendidoController@downLibroEX')->name('downLibroEX');
// Descargar reporte de libros vendidos por editorial
Route::get('/downEditorialEX/{editorial}/{fecha1}/{fecha2}', 'VendidoController@downEditorialEX')->name('downEditorialEX');
// Descargar reporte detallado de libros vendidos
Route::get('/downDetalladoEX/{fecha1}/{fecha2}', 'VendidoController@downDetalladoEX')->name('downDetalladoEX');


// PEDIDOS
// Guardar nuevo pedido
Route::post('guardar_compra', 'CompraController@store')->name('guardar_compra');
// Mostrar detalles de la compra
Route::get('detalles_compra', 'CompraController@detalles_compra')->name('detalles_compra');
// Buscar compra por numero de pedido
Route::get('buscar_n_pedido', 'CompraController@buscar_n_pedido')->name('buscar_n_pedido');
// Buscar compras por usuario
Route::get('buscar_usuario_p', 'CompraController@buscar_usuario_p')->name('buscar_usuario_p');
// Buscar compras por fecha
Route::get('buscar_fecha_p', 'CompraController@buscar_fecha_p')->name('buscar_fecha_p');
// Descargar reporte
Route::get('/download_compra/{usuario}/{inicio}/{final}/{tipo}', 'CompraController@download_compra')->name('download_compra');
// Marcar la entrega del pedido
Route::put('marcar_pedido', 'CompraController@marcar_pedido')->name('marcar_pedido');
// Descargar nota
Route::get('/download_pedido/{id}', 'CompraController@download_pedido')->name('download_pedido');