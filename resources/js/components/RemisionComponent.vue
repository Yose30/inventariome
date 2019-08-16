<template>
    <div>
        <div class="row">
            <h4 class="col-md-4">{{ !editar ? 'Crear remisión': `Editar remisión N. ${bdremision.id}` }}</h4>
            <div class="col-md-2"> 
                <b-button 
                    variant="success" 
                    @click="nuevaRemision" 
                    v-if="btnNuevo">
                    <i class="fa fa-plus"></i>
                </b-button>
                <div>
                    <b-button 
                        variant="danger"
                        @click="show=true" 
                        v-if="!btnNuevo">
                        <i class="fa fa-close"></i>
                    </b-button>
                    <b-modal v-model="show" title="Cerrar remisión">
                        <p><b><i class="fa fa-exclamation-triangle"></i> No se guardara ningún cambio</b></p>
                        <div slot="modal-footer">
                            <b-button @click="cancelarRemision">OK</b-button>
                        </div>
                    </b-modal>
                </div>
            </div>
            <div class="col-md-4">
                <b-button 
                    :disabled="load"
                    @click="guardarRemision" 
                    variant="success"
                    v-if="mostrarGuardar && !editar && items.length > 0">
                    <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                </b-button>
                <!-- <b-button 
                    @click="actRemision" 
                    variant="success"
                    v-if="mostrarActualizar && items.length > 0">
                    <i class="fa fa-check"></i> Guardar
                </b-button> -->
            </div>
            <!-- <div class="col-md-2">
                <b-button 
                    variant="warning" 
                    @click="editarRemision"
                    v-if="mostrarOpciones">
                    <i class="fa fa-pencil"></i>
                </b-button>
            </div> -->
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
        <div align="center" v-if="mostrarBusqueda && !btnNuevo">
            <!-- <div align="left">
                <button 
                    class="btn btn-light" 
                    v-if="clientes.length > 0 && !listaClientes" 
                    @click="listaClientes = true;">
                    <i class="fa fa-users"></i> Clientes
                </button>
            </div>
            <div align="right">
                <button 
                    id="btnCancelar" 
                    class="btn btn-danger" 
                    v-if="btnEditarInf" 
                    @click="mostrarBusqueda = false; mostrarDatos = true; mostrarForm = true;">
                    <i class="fa fa-close"></i>
                </button>
            </div> -->
            <div v-if="listaClientes">
                <b-alert v-if="clientes.length == 0" show variant="secondary">
                    <i class="fa fa-exclamation-triangle"></i> No hay clientes registrados, ir al apartado de <b>Agregar cliente</b> para poder continuar.
                </b-alert>
                <div v-if="clientes.length > 0">
                    <h6 align="left">Seleccionar cliente</h6>
                    <hr>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Dirección</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cliente, i) in clientes" v-bind:key="i">
                                <td>{{ cliente.name }}</td>
                                <td>{{ cliente.email }}</td>
                                <td>{{ cliente.direccion }}</td>
                                <td>
                                    <b-button variant="success" @click="seleccionCliente(cliente)">
                                        <i class="fa fa-check"></i>
                                    </b-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <hr>
        <div v-if="!mostrarBusqueda">
            <div class="row">
                <h6 class="col-md-10">Datos del cliente</h6>
                <div class="col-md-1">
                    <b-button 
                        id="btnEditar" 
                        variant="warning" 
                        :disabled="btnInformacion" 
                        @click="editarInformacion"><i class="fa fa-pencil"></i>
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
                        <b-list-group-item><b>Teléfono:</b></b> {{ dato.telefono }}</b-list-group-item>
                    </b-list-group>
                </div>
            </b-collapse>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6" v-if="mostrarForm">
                <label><b>Fecha de entrega</b></label>
                <b-form-input 
                    type="date" 
                    v-model="fecha" 
                    :disabled="inputFecha"
                    @change="sel_fecha">
                </b-form-input>
                <div class="text-danger">{{ respuestaFecha }}</div>
            </div>
            <div class="col-md-6" align="right" v-if="mostrarTotal">
                <label><b>Total:</b> ${{ total_remision }}</label>
            </div>
        </div>
        <hr>
        <div>
            <table class="table" v-if="mostrarForm">
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
                    <tr>
                        <td>
                            <b-input
                                v-model="isbn"
                                @keyup.enter="buscarLibroISBN()"
                                v-if="inputISBN"
                            ></b-input>
                            <div class="text-danger">{{ respuestaISBN }}</div>
                            <b v-if="!inputISBN">{{ temporal.ISBN }}</b>
                        </td>
                        <td>
                            <b-input
                                v-model="queryTitulo"
                                @keyup="mostrarLibros"
                                v-if="inputLibro"
                            ></b-input>
                            <div class="list-group" v-if="resultslibros.length">
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
                                v-model="costo_unitario"
                                v-if="inputCosto"
                                min="1"
                                max="9999"
                                @keyup.enter="guardarCosto">
                            </b-input> 
                            <!-- <b v-if="!inputCosto">$ {{ temporal.costo_unitario }}</b> -->
                        </td>
                        <td>
                            <b-input 
                            type="number" 
                            v-model="unidades"
                            v-if="inputUnidades"
                            min="1"
                            max="9999"
                            @keyup.enter="guardarRegistro"
                            ></b-input>
                        </td>
                        <td></td>
                        <td>
                            <b-button 
                                variant="secondary"
                                @click="eliminarTemporal" 
                                v-if="inputCosto || inputUnidades">
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
        data() {
            return {
                load: false,
                btnNuevo: true, //Para determinar si se muestra el boton de nueva remisión
                show: false, //Para determinar si se muestra el modal de cancelar
                queryCliente: '', //Buscar cliente por nombre
                resultsClientes: [], //Mostrar los resultados de la busqueda de cliente
                dato: {}, //Guardar datos de cliente y mostrar
                mostrarForm: false, //Inidcar si se muestra o no la tabla, fecha y total
                isbn: '', //Buscar del libro por ISBN
                respuestaISBN: '', //Indicar si el ISBN es incorrecto
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
                editar: false, //Indicar si la remision esta en forma de edicion
                items: [], //Registros guardados de la remision
                total_remision: 0, //Mostrar el total de la remision
                unidades: 0, //Asignar el numero de unidades
                costo_unitario: 0, //Asignar el costo unitario
                botonEliminar: false, //Boton para poder eliminar un registro
                mostrarGuardar: false, //Para mostrar el boton de guardar
                mostrarTotal: false, //Para mostrar el total de la remision
                mostrarBusqueda: true, //Indicar si se muestra el apartado de buscar cliente
                mostrarOpciones: false, //Indicar si se muestran los botones de eliminar y editar
                respuestaFecha: '', //Indicar si la fecha no fue seleccionada
                mostrarActualizar: false, //Indicar si se muestra el boton de actualizar remision 
                mostrarDatos: false, //Indicar si se ocultan o muestran los datos del cliente
                inputFecha: false, //Indicar si se deshabilita o no el input de Fecha
                respuestaUnidades: '', //Indicar si el valor es correcto
                respuestaCosto: '', //Indicar si el valor es correcto
                btnInformacion: false, //Habilitar o deshabilitar boton de editar informacion
                //Formulario para agregar cliente
                form: {},
                errors: {},
                btnEditarInf: false,
                descuento: 0,
                pagar: 0,
                clientes: [],
                listaClientes: false,
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            //Inicializar valores para crear una nueva remision
            nuevaRemision(){
                this.btnEditarInf = false;
                this.listaClientes = true;
                this.ini_1();
                this.ini_2();
                this.ini_4();
            },
            //Cerrar la remisión
            cancelarRemision(){
                this.ini_4();
                this.show = false;
                this.btnEditarInf = false;
                this.listaClientes = false;
                this.btnNuevo = true;
            },
            //Obtener todos los clientes registrados
            getTodo(){
                axios.get('/getTodo').then(response => {
                    this.clientes = response.data;
                });
            },
            //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.inicializar();
                    this.temporal = response.data;
                }).catch(error => {
                   this.makeToast('danger', 'ISBN incorrecto');
                });
            },
            //Buscar libro por titulo, Mostrar resultados de la busqueda
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
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.inicializar();
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: 0,
                    unidades: 0,
                    total: 0,
                    piezas: libro.piezas
                };
            },
            //Asignar la fecha seleccionada
            sel_fecha(){
                this.bdremision.fecha_entrega = this.fecha;
            },
            //Guardar un registro de la remision
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
                            this.makeToast('danger', 'El costo debe ser mayor a 0');
                        } 
                    }
                    else{
                        this.makeToast('danger', `${this.temporal.piezas} piezas en existencia`);
                    }
                }
                else{
                    this.makeToast('danger', 'Unidades invalidas');
                }
            },
            //Eliminar registro de la remision
            eliminarRegistro(item, i){
                this.items.splice(i, 1);
                this.total_remision = this.total_remision - item.total;
                this.bdremision.total = this.total_remision;
            },
            //Guardar toda la remision
            guardarRemision(){
                this.load = true;
                this.bdremision.total = this.total_remision;
                this.bdremision.cliente_id = this.dato.id;
                this.bdremision.registros = this.items;
                if(this.bdremision.fecha_entrega != ''){
                    axios.post('/crear_remision', this.bdremision).then(response => {
                        this.bdremision.id = response.data.id;
                        this.respuestaFecha ='';
                        this.mostrarGuardar = false;
                        this.load = false;
                        this.makeToast('success', 'La remisión se creo correctamente');
                        this.inicializar_guardar();
                    })
                    .catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.makeToast('danger', 'Selecciona fecha de entrega');
                    this.load = false;
                }
            },
            //Editar la remision
            // editarRemision(){
            //     this.ini_1();
            //     this.mostrarActualizar = true;
            //     this.inputFecha = false;
            //     this.botonEliminar = true;
            //     this.editar = true;
            // },
            //Guardar cambios de la remision
            // actRemision(){
            //     this.bdremision.total = this.total_remision;
            //     axios.put('/actualizar_remision', this.bdremision).then(response => {
            //         this.mostrarActualizar = false;
            //         this.inicializar_guardar();
            //     });
            // }, 
            eliminarTemporal(){
                this.ini_1();
                this.ini_2();
                this.inputCosto = false;
                this.queryTitulo = '';
                this.respuestaUnidades = '';
                this.respuestaCosto = '';
                this.costo_unitario = 0;
            },
            guardarCosto(){
                if(this.costo_unitario > 0){
                    this.temporal.costo_unitario = this.costo_unitario;
                    // this.inputCosto = false;
                    this.inputUnidades = true;
                    this.respuestaCosto = '';
                }
                else{
                    this.makeToast('danger', 'El costo debe ser mayor a 0');
                    
                } 
            },
            //Editar información del cliente
            editarInformacion(){
                this.listaClientes = true;
                this.mostrarBusqueda = true;
                this.mostrarDatos = false;
                this.btnEditarInf = true;
                this.form = this.dato;
                this.mostrarForm = false;
            }, 
            seleccionCliente(cliente){
                this.inicializar_editar(cliente); 
                this.listaClientes = false;
            },
            //Inicializar los valores
            inicializar(){
                this.ini_3();
                this.respuestaISBN = '';
                this.resultslibros = [];
                this.costo_unitario = 0;
                this.inputCosto = true;
            },
            //Inicializar los valores
            inicializar_registro(){
                this.ini_1();
                this.ini_2();
                this.costo_unitario = 0;
                this.inputCosto = false;
                this.respuestaUnidades = '';
                this.botonEliminar = true;
                this.mostrarTotal = true;
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
                this.btnNuevo = true;
                this.btnInformacion = true;
            },
            ini_1(){
                this.mostrarOpciones = false;
                this.inputLibro = true;
                this.inputISBN = true;
                this.btnNuevo = false;
                this.btnInformacion = false;
            }, 
            ini_2(){
                this.temporal = {};
                this.inputUnidades = false;
                this.unidades = 0;
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
                this.form = {};
                this.total_remision = 0;
                this.fecha = '';
                this.editar = false;
                this.inputFecha = false;
                this.mostrarActualizar = false;
                this.mostrarForm = false;
                this.mostrarGuardar = true;
                this.mostrarTotal = false;
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
</style>