<template>
    <div>
        <h4>Crear remisión</h4>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div class="row" v-if="mostrarBusqueda">
                    <label class="col-md-6">Nombre del cliente</label>
                    <div class="col-md-6">
                        <b-input
                        v-model="queryCliente"
                        @keyup="mostrarClientes"
                        ></b-input>
                        <div class="list-group" v-if="resultsClientes.length">
                            <a 
                                href="#" 
                                v-bind:key="i" 
                                class="list-group-item list-group-item-action" 
                                v-for="(result, i) in resultsClientes" 
                                @click="datosCliente(result)">
                                {{ result.name }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" align="right">
                <b-button 
                    @click="onSubmit" 
                    variant="success"
                    v-if="mostrarGuardar && !editar && items.length > 0">
                    <i class="fa fa-check"></i> Guardar
                </b-button>

                <b-button 
                    @click="actRemision" 
                    variant="success"
                    v-if="mostrarActualizar">
                    <i class="fa fa-check"></i> Guardar
                </b-button>
                
                <div class="row" align=right>
                    <b-button 
                        variant="warning" 
                        class="col-md-3"
                        @click="editarRemision"
                        v-if="mostrarOpciones">
                        <i class="fa fa-pencil"></i>
                    </b-button>
                    <b-button 
                        variant="primary" 
                        class="col-md-3"
                        @click="nuevaRemision"
                        v-if="mostrarOpciones">
                        <i class="fa fa-plus"></i>
                    </b-button>
                    <div class="col-md-1"></div>
                    <a 
                        class="btn btn-info col-md-3"
                        v-if="mostrarOpciones"
                        :href="'/imprimirSalida/' + bdremision.id">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div>
            <div class="row">
                <h6 class="col-md-11">Datos del cliente</h6>
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
                        <b-list-group-item><b>Correo electrónico:</b> {{ dato.email }}</b-list-group-item>
                        <b-list-group-item><b>Teléfono:</b></b> {{ dato.telefono }}</b-list-group-item>
                    </b-list-group>
                    <b-list-group class="col-md-6">
                        <b-list-group-item><b>Dirección:</b> {{dato.direccion  }}</b-list-group-item>
                        <b-list-group-item><b>Descuento:</b> {{ dato.descuento }} %</b-list-group-item>
                        <b-list-group-item><b>Condiciones de pago:</b> {{ dato.condiciones_pago }}</b-list-group-item>
                    </b-list-group>
                </div>
            </b-collapse>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6" v-if="mostrarForm">
                <label><b>Fecha de entrega</b></label>
                <input 
                    class="form-control" 
                    type="date" 
                    v-model="fecha" 
                    :disabled="inputFecha"
                    @change="sel_fecha">
                </input>
                <div class="text-danger">{{ respuestaFecha }}</div>
            </div>
            <div class="col-md-6" align="right">
                <label v-if="mostrarTotal"><b>Total:</b> ${{ total_remision }}</label>
            </div>
        </div>
        <hr>
        <div>
            <table class="table" v-if="mostrarForm">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Titulo</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Costo total</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, i) in items" v-bind:key="i">
                        <td>{{ item.isbn_libro }}</td>
                        <td>{{ item.titulo }}</td>
                        <td>{{ item.unidades }}</td>
                        <td>$ {{ item.costo_unitario }}</td>
                        <td>$ {{ item.total }}</td>
                        <td><button class="btn btn-danger" @click="eliminarRegistro(item, i)" v-if="botonEliminar"><i class="fa fa-minus-circle"></i></button></td>
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
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(libro, i) in resultslibros" 
                                    @click="datosLibro(libro)">
                                    {{ libro.titulo }}
                                </a>
                            </div>
                            <b v-if="!inputLibro">{{ temporal.titulo }}</b>
                        </td>
                        <td>
                            <input 
                            type="number" 
                            v-model="unidades"
                            v-if="inputUnidades"
                            min="1"
                            max="9999"
                            @keyup.enter="guardarRegistro()"
                            ></input>
                        </td>
                        <td v-if="inputUnidades">$ {{ temporal.costo_unitario }}</td>
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
                queryCliente: '', //Buscar cliente por nombre
                resultsClientes: [], //Mostrar los resultados de la busqueda de cliente
                dato: {}, //Guardar datos de cliente y mostrar
                mostrarForm: false, //Inidcar si se muestra o no la tabla, fecha y total
                isbn: '', //Buscar del libro por ISBN
                respuestaISBN: '', //Indicar si el ISBN es incorrecto
                inputISBN: true, //Mostrar o no el input de ISBN
                inputLibro: true, //Mostrar o no el input de busqueda por titulo
                inputUnidades: false, //Mostrar o no el input de
                queryTitulo: '', //Buscar libro por titulo
                resultslibros: [], //Mostrar resultados de la busqueda de libro
                temporal: {}, //Guardar temporalmente los datos de la busqueda del libro
                fecha: '', //seleccionar la fecha de fecha_entrega
                bdremision: {
                    id: 0,
                    cliente_id: 0,
                    total: 0,
                    fecha_entrega: ''
                }, //Para guardar todos los datos de la remision
                editar: false, //Indicar si la remision esta en forma de edicion
                items: [], //Registros guardados de la remision
                total_remision: 0, //Mostrar el total de la remision
                unidades: 0, //Asignar el numero de unidades
                botonEliminar: false, //Boton para poder eliminar un registro
                mostrarGuardar: false, //Para mostrar el boton de guardar
                mostrarTotal: false, //Para mostrar el total de la remision
                mostrarBusqueda: true, //Indicar si se muestra el apartado de buscar cliente
                mostrarOpciones: false, //Indicar si se muestran los botones de eliminar y editar
                respuestaFecha: '', //Indicar si la fecha no fue seleccionada
                mostrarActualizar: false, //Indicar si se muestra el boton de actualizar remision 
                mostrarDatos: false, //Indicar si se ocultan o muestran los datos del cliente
                inputFecha: false, //Indicar si se deshabilita o no el input de Fecha
            }
        },
        methods: {
            //Se repite en Listado
            //Buscar el cliente y mostrar resultados
            mostrarClientes(){
               if(this.queryCliente.length > 0){
                   axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    });
               } 
            },
            //Mostrar los datos del cliente seleccionado
            datosCliente(result){
                this.mostrarDatos = true;
                this.dato = result;
                this.queryCliente = '';
                this.resultsClientes = [];
                this.mostrarForm = true;
            },
            //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.inicializar();
                    this.temporal = response.data;
                }).catch(error => {
                    this.respuestaISBN = 'ISBN incorrecto';
                });
            },
            //Mostrar resultados de la busqueda por titulo del libro
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
               } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.inicializar();
                this.temporal = {
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: libro.costo_unitario,
                    unidades: 0,
                    total: 0
                };
            },
            //Inicializar los valores
            inicializar(){
                this.isbn = '';
                this.respuestaISBN = '';
                this.inputISBN = false;
                this.inputLibro = false;
                this.queryTitulo = '';
                this.resultslibros = [];
                this.inputUnidades = true;
            },
            //Asignar la fecha seleccionada
            sel_fecha(){
                this.bdremision.fecha_entrega = this.fecha;
            },
            //Guardar un registro de la remision
            guardarRegistro(){
                this.mostrarDatos = false;
                this.temporal.remision_id = 0;
                this.temporal.unidades = this.unidades;
                this.temporal.total = (this.unidades * this.temporal.costo_unitario) - (((this.unidades * this.temporal.costo_unitario) * this.dato.descuento) / 100);
                
                if(this.editar){
                    this.temporal.remision_id = this.bdremision.id;
                }

                axios.post('/registro_remision', this.temporal).then(response => {
                    this.items.push(response.data);
                    this.total_remision += response.data.total;
                    this.unidades = 0;
                    this.temporal = {};
                    this.inputISBN = true;
                    this.inputLibro = true;
                    this.inputUnidades = false;
                    this.botonEliminar = true;
                    this.mostrarGuardar = true;
                    this.mostrarTotal = true;
                    this.mostrarBusqueda = false;
                });
            },
            //Eliminar registro de la remision
            eliminarRegistro(item, i){
                axios.delete('/eliminar_registro', {params: {id: item.id}}).then(response => {
                    this.items.splice(i, 1);
                    this.total_remision = this.total_remision - item.total;
                    this.bdremision.total = this.total_remision;
                });
            },
            //Guardar toda la remision
            onSubmit(){
                this.bdremision.total = this.total_remision;
                this.bdremision.cliente_id = this.dato.id;
                if(this.bdremision.fecha_entrega != ''){
                    axios.post('/crear_remision', this.bdremision).then(response => {
                        this.bdremision.id = response.data.id;
                        this.respuestaFecha ='';
                        this.mostrarGuardar = false;
                        this.inicializar_guardar();
                    });
                }
                else{
                    this.respuestaFecha = 'Selecciona fecha de entrega';
                }
                
            },
            //Editar la remision
            editarRemision(){
                this.mostrarOpciones = false;
                this.mostrarActualizar = true;
                this.inputLibro = true;
                this.inputISBN = true;
                this.inputFecha = false;
                this.botonEliminar = true;
                this.editar = true;
            },
            //Guardar cambios de la remision
            actRemision(){
                axios.put('/actualizar_remision', this.bdremision).then(response => {
                    this.mostrarActualizar = false;
                    this.inicializar_guardar();
                });
            },
            //Inicializar valores
            inicializar_guardar(){
                this.temporal = {};
                this.isbn = '';
                this.queryTitulo = '',
                this.inputLibro = false;
                this.inputISBN = false;
                this.inputFecha = true;
                this.inputUnidades = false;
                this.mostrarOpciones = true;
                this.botonEliminar = false;
            },
            //Inicializar valores para crear una nueva remision
            nuevaRemision(){
                this.bdremision = {id: 0, cliente_id: 0, total: 0, fecha_entrega: ''};
                this.items = [];
                this.temporal = {};
                this.total_remision = 0;
                this.editar = false;
                this.fecha = '';
                this.unidades = 0;
                this.dato = {};
                this.inputLibro = true;
                this.inputISBN = true;
                this.fecha = '';
                this.inputFecha = false;
                this.inputUnidades = false;
                this.mostrarActualizar = false;
                this.mostrarBusqueda = true;
                this.mostrarForm = false;
                this.mostrarGuardar = true;
                this.mostrarOpciones = false;
                this.mostrarTotal = false;
            },
            imprimir(){
                axios.get('/imprimirSalida', {params: {remision_id: this.bdremision.id}}).then(response => {
                    console.log(response);
                });
            }, 
        }
    }
</script>