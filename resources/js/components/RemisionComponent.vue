<template>
    <div>
        <div class="row">
            <h4 style="color: #170057" class="col-md-4">Crear remisión</h4>
            <!-- GUARDAR LOS DATOS DE LA REMISIÓN -->
            <div class="col-md-6">
                <b-button 
                    :disabled="load"
                    @click="guardarRemision()" 
                    variant="success"
                    v-if="mostrarGuardar && items.length > 0">
                    <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                </b-button>
            </div>
            <!-- IMPRIMIR LA REMSIÓN -->
            <div class="col-md-2">
                <a 
                    class="btn btn-info"
                    v-if="mostrarOpciones"
                    :href="'/imprimirSalida/' + bdremision.id">
                    <i class="fa fa-download"></i> Descargar
                </a>
            </div>
        </div>
        <hr>
        <!-- SELECCIONAR CLIENTE PARA UNA NUEVA REMISIÓN -->
        <div align="center" v-if="mostrarBusqueda">
            <div v-if="clientes.length > 0">
                <b-row>
                    <b-col sm="4"><h6 align="left"><b>Seleccionar cliente</b></h6></b-col>
                    <b-col>
                        <b-row>
                            <b-col sm="2">
                                <label for="input-cliente"><i class="fa fa-search"></i> Buscar</label>
                            </b-col>
                            <b-col sm="10">
                                <b-input v-model="queryCliente" autofocus @keyup="mostrarClientes()"></b-input>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <br>
                <!-- PAGINACIÓN -->
                <b-pagination
                    v-model="currentPage"
                    :total-rows="clientes.length"
                    :per-page="perPage"
                    aria-controls="my-table"
                    align="right"
                ></b-pagination>
                <!-- LISTADO DE CLIENTES -->
                <b-table 
                    :items="clientes" 
                    :fields="fieldsClientes" 
                    id="my-table" 
                    :per-page="perPage" 
                    :current-page="currentPage">
                    <template slot="seleccion" slot-scope="row">
                        <b-button variant="success" @click="seleccionCliente(row.item)">
                            <i class="fa fa-check"></i>
                        </b-button>
                    </template>
                </b-table>
            </div>
        </div>
        <hr>
        <div v-if="!mostrarBusqueda">
            <!-- MOSTRAR DATOS DEL CLIENTE -->
            <div class="row">
                <h6 class="col-md-10"><b>Datos del cliente</b></h6>
                <div class="col-md-1">
                    <b-button 
                        id="btnEditar" 
                        variant="warning" 
                        :disabled="btnInformacion" 
                        @click="editarInformacion()"><i class="fa fa-pencil"></i>
                    </b-button>
                </div>
                <b-button 
                    variant="link" 
                    :class="mostrarDatos ? 'collapsed' : null"
                    :aria-expanded="mostrarDatos ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarDatos = !mostrarDatos">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-1" v-model="mostrarDatos" class="mt-2">
                <div class="row">
                    <b-list-group class="col-md-6">
                        <b-list-group-item><b>Nombre:</b> {{ dato.name }}</b-list-group-item>
                        <b-list-group-item><b>Dirección:</b> {{dato.direccion  }}</b-list-group-item>
                        <b-list-group-item><b>Condiciones de pago:</b> {{ dato.condiciones_pago }}</b-list-group-item>
                    </b-list-group>
                    <b-list-group class="col-md-6">
                        <b-list-group-item><b>Correo electrónico:</b> {{ dato.email }}</b-list-group-item>
                        <b-list-group-item><b>Teléfono:</b> {{ dato.telefono }}</b-list-group-item>
                    </b-list-group>
                </div>
            </b-collapse>
        </div>
        <hr>
        <div v-if="mostrarForm">
            <div class="row">
                <div class="col-md-6">
                    <label><b>Fecha de entrega</b></label>
                    <b-form-input 
                        type="date" 
                        v-model="fecha" 
                        :disabled="inputFecha"
                        @change="sel_fecha()">
                    </b-form-input>
                </div>
                <div class="col-md-6" align="right">
                    <label><b>Total:</b> ${{ total_remision }}</label>
                </div>
            </div>
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <b-input
                                v-model="isbn"
                                autofocus
                                @keyup.enter="buscarLibroISBN()"
                                v-if="inputISBN"
                            ></b-input>
                            <b v-if="!inputISBN">{{ temporal.ISBN }}</b>
                        </td>
                        <td>
                            <b-input
                                v-model="queryTitulo"
                                autofocus
                                @keyup="mostrarLibros()"
                                v-if="inputLibro"
                            ></b-input>
                            <div class="list-group" v-if="resultslibros.length" id="listaL">
                                <a 
                                    class="list-group-item list-group-item-action" 
                                    href="#" 
                                    v-bind:key="i" 
                                    v-for="(libro, i) in resultslibros" 
                                    @click="datosLibro(libro)">
                                    {{ libro.titulo }}
                                </a>
                            </div>
                            <b v-if="!inputLibro">{{ temporal.titulo }}</b>
                        </td>
                        <td>
                            <b-input 
                                type="number" 
                                autofocus
                                v-model="costo_unitario"
                                v-if="inputCosto"
                                min="1"
                                max="9999"
                                @keyup.enter="guardarCosto()">
                            </b-input>
                        </td>
                        <td>
                            <b-input 
                            autofocus
                            type="number" 
                            v-model="unidades"
                            v-if="inputUnidades"
                            min="1"
                            max="9999"
                            @keyup.enter="guardarRegistro()"
                            ></b-input>
                        </td>
                        <td></td>
                        <td>
                            <b-button 
                                variant="secondary"
                                @click="eliminarTemporal()" 
                                v-if="inputCosto || inputUnidades">
                                <i class="fa fa-minus-circle"></i>
                            </b-button>
                        </td>
                    </tr>
                    <tr v-for="(item, i) in items" v-bind:key="i">
                        <td>{{ item.ISBN }}</td>
                        <td>{{ item.titulo }}</td>
                        <td>$ {{ item.costo_unitario }}</td>
                        <td>{{ item.unidades }}</td>
                        <td>$ {{ item.total }}</td>
                        <td>
                            <b-button 
                                variant="danger" 
                                @click="eliminarRegistro(item, i)" 
                                v-if="botonEliminar">
                                <i class="fa fa-minus-circle"></i>
                            </b-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['registersall'],
        data() {
            return {
                load: false,
                queryCliente: '', //Buscar cliente por nombre
                resultsClientes: [], //Mostrar los resultados de la busqueda de cliente
                dato: {}, //Guardar datos de cliente y mostrar
                mostrarForm: false, //Indicar si se muestra o no la tabla, fecha y total
                isbn: '', //Buscar del libro por ISBN
                inputISBN: true, //Mostrar o no el input de ISBN
                inputLibro: true, //Mostrar o no el input de busqueda por titulo
                inputUnidades: false, //Mostrar o no el input de unidades
                inputCosto: false, //Mostrar el input de costo
                queryTitulo: '', //Buscar libro por titulo
                resultslibros: [], //Mostrar resultados de la busqueda de libro
                temporal: {}, //Guardar temporalmente los datos de la busqueda del libro
                fecha: '', //seleccionar la fecha de fecha_entrega
                bdremision: {
                    id: 0,
                    cliente_id: 0,
                    total: 0,
                    fecha_entrega: '',
                    registros: []
                }, //Para guardar todos los datos de la remision
                items: [], //Registros guardados de la remision
                total_remision: 0, //Mostrar el total de la remision
                unidades: null, //Asignar el numero de unidades
                costo_unitario: null, //Asignar el costo unitario
                botonEliminar: false, //Boton para poder eliminar un registro
                mostrarGuardar: false, //Para mostrar el boton de guardar
                mostrarBusqueda: true, //Indicar si se muestra el apartado de buscar cliente
                mostrarOpciones: false, //Indicar si se muestran los botones de eliminar y editar
                mostrarDatos: false, //Indicar si se ocultan o muestran los datos del cliente
                inputFecha: false, //Indicar si se deshabilita o no el input de Fecha
                btnInformacion: false, //Habilitar o deshabilitar boton de editar informacion
                clientes: this.registersall,
                fieldsClientes: [
                    {key: 'name', label: 'Nombre'},
                    {key: 'direccion', label: 'Dirección'}, 
                    {key: 'seleccion', label: ''}
                ],
                perPage: 10,
                currentPage: 1,
            }
        },
        created: function() {
            this.ini_1();
            this.ini_2();
            this.ini_4();
        },
        methods: {
            // GUARDAR DATOS DE REMISIÓN
            guardarRemision(){
                this.load = true;
                this.bdremision.total = this.total_remision;
                this.bdremision.cliente_id = this.dato.id;
                this.bdremision.registros = this.items;
                if(this.bdremision.fecha_entrega != ''){
                    axios.post('/crear_remision', this.bdremision).then(response => {
                        this.bdremision.id = response.data.id;
                        this.mostrarGuardar = false;
                        this.load = false;
                        this.makeToast('success', 'La remisión se creo correctamente.');
                        this.inicializar_guardar();
                    })
                    .catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.makeToast('warning', 'Selecciona fecha de entrega');
                    this.load = false;
                }
            },
            // MOSTRAR COINCIDENCIA DE CLIENTES
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.clientes = response.data;
                    }); 
                }
                else{
                    this.getTodo();
                }
            },
            // OBTENER TODOS LOS CLIENTES
            getTodo(){
                axios.get('/getTodo').then(response => {
                    this.clientes = response.data;
                }).catch(error => {
                   this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // ASIGNAR DATOS DE CLIENTE SELECCIONADO
            seleccionCliente(cliente){
                this.inicializar_editar(cliente);
            },
            // INICIALIZAR PARA CAMBIAR CLIENTE
            editarInformacion(){
                this.mostrarBusqueda = true;
                this.mostrarDatos = false;
                this.mostrarForm = false;
            },
            // ASIGNAR FECHA SELECCIONADA
            sel_fecha(){
                this.bdremision.fecha_entrega = this.fecha;
            },
            // ELIMINAR REGISTRO DE ARRAY
            eliminarRegistro(item, i){
                this.items.splice(i, 1);
                this.total_remision = this.total_remision - item.total;
                this.bdremision.total = this.total_remision;
            },
            // BUSCAR LIBRO POR ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.inicializar();
                    this.temporal = response.data;
                }).catch(error => {
                   this.makeToast('warning', 'El ISBN no existe');
                });
            },
            // MOSTRAR LIBROS POR COINCIDENCIA
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
                } 
                else{
                    this.resultslibros = [];
                }
            },
            // ASIGNAR DATOS DE LIBRO SELECCIONADO
            datosLibro(libro){
                this.inicializar();
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: null,
                    unidades: null,
                    total: 0,
                    piezas: libro.piezas
                };
            },
            // VERIFICAR EL COSTO INGRESADO
            guardarCosto(){
                if(this.costo_unitario > 0){
                    this.temporal.costo_unitario = this.costo_unitario;
                    this.inputUnidades = true;
                }
                else{
                    this.makeToast('warning', 'El costo unitario debe ser mayor a 0');
                    
                } 
            },
            // GUARDAR REGISTRO TEMPORAL
            guardarRegistro(){
                if(this.unidades > 0){
                    if(this.unidades <= this.temporal.piezas){
                        if(this.costo_unitario > 0){
                            this.mostrarDatos = false;
                            this.temporal.unidades = this.unidades;
                            this.temporal.total = this.unidades * this.temporal.costo_unitario;
                            this.items.push(this.temporal);
                            this.total_remision += this.temporal.total;
                            this.inicializar_registro();
                        }
                        else{
                            this.makeToast('warning', 'El costo unitario debe ser mayor a 0');
                        } 
                    }
                    else{
                        this.makeToast('warning', `${this.temporal.piezas} piezas en existencia`);
                    }
                }
                else{
                    this.makeToast('warning', 'Ñas unidades deben ser mayor a 0');
                }
            },
            // ELIMINAR REGISTRO TEMPORAL
            eliminarTemporal(){
                this.ini_1();
                this.ini_2();
                this.inputCosto = false;
                this.queryTitulo = '';
                this.costo_unitario = null;
            },
            //Cerrar la remisión (ELIMINADO)
            cancelarRemision(){
                this.ini_4();
            },
            //Inicializar los valores
            inicializar(){
                this.ini_3();
                this.resultslibros = [];
                this.costo_unitario = null;
                this.inputCosto = true;
                
            },
            //Inicializar los valores
            inicializar_registro(){
                this.ini_1();
                this.ini_2();
                this.costo_unitario = null;
                this.inputCosto = false;
                this.botonEliminar = true;
                this.mostrarGuardar = true;
                this.mostrarBusqueda = false;
            },
            //Inicializar cuando se edito la información del cliente
            inicializar_editar(dato){
                this.mostrarBusqueda = false;
                this.mostrarDatos = true;
                this.dato = dato;
                this.mostrarForm = true;
            },
            //Inicializar valores
            inicializar_guardar(){
                this.ini_3();
                this.temporal = {}; 
                this.inputUnidades = false;
                this.inputCosto = false;
                this.inputFecha = true;
                this.mostrarOpciones = true;
                this.botonEliminar = false;
                this.btnInformacion = true;
            },
            ini_1(){
                this.mostrarOpciones = false;
                this.inputLibro = true;
                this.inputISBN = true;
                this.btnInformacion = false;
            }, 
            ini_2(){
                this.temporal = {};
                this.inputUnidades = false;
                this.unidades = null;
            },
            ini_3(){
                this.isbn = '';
                this.queryTitulo = '';
                this.inputISBN = false;
                this.inputLibro = false;
            },
            ini_4(){
                this.bdremision = {id: 0, cliente_id: 0, total: 0, fecha_entrega: ''};
                this.items = [];
                this.dato = {};
                this.total_remision = 0;
                this.fecha = '';
                this.inputFecha = false;
                this.mostrarForm = false;
                this.mostrarGuardar = true;
                this.mostrarBusqueda = true;
            },
            makeToast(variant = null, descripcion) {
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variant,
                    solid: true
                })
            }
        }
    }
</script>

<style>
    #btnCancelar {
        color: red;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #btnEditar {
        color: #ffb300;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #listaL{
        position: absolute;
        z-index: 100
    }
</style>