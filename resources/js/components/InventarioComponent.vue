<template>
    <div>
        <div class="row">
            <h4 class="col-md-4">Registrar entrada</h4>
            <div class="col-md-1">
                <b-button variant="success" @click="nuevaEntrada" v-if="btnNuevo"><i class="fa fa-plus"></i></b-button>
            </div>
            <div class="col-md-2">
                <b-button 
                    @click="onSubmit" 
                    v-if="mostrarGuardar && !editar && items.length > 0"
                    variant="success">
                    <i class="fa fa-check"></i> Guardar
                </b-button>
                <b-button 
                    @click="actRemision" 
                    variant="success"
                    v-if="mostrarActualizar && items.length > 0">
                    <i class="fa fa-check"></i> Guardar
                </b-button>
            </div>
            <div class="col-md-2">
                <b-button 
                    variant="warning" 
                    @click="editarTEntrada"
                    v-if="mostrarEditar">
                    <i class="fa fa-pencil"></i>
                </b-button>
            </div>
            <div class="col-md-1">
                <a 
                    class="btn btn-info"
                    v-if="mostrarEditar"
                    :href="'/imprimirEntrada/' + bdentrada.id">
                    <i class="fa fa-print"></i>
                </a>
            </div>
        </div>
        <hr>
        <div v-if="mostrarTabla">
            <div align="right">
                <label><b>Total:</b> ${{ total_entrada }}</label>
            </div>
            <div align="left">
                <label><b>Unidades:</b> {{ total_unidades }}</label>
            </div>
            <hr>
            <div>
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
                        <tr v-for="(item, i) in items" v-bind:key="i">
                            <td>{{ item.ISBN }}</td>
                            <td>{{ item.titulo }}</td>
                            <td>$ {{ item.costo_unitario }}</td>
                            <td>{{ item.unidades }}</td>
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
                                <b-form-input 
                                    @keyup.enter="guardarCosto()"
                                    v-model="costo_unitario" 
                                    v-if="inputCosto"
                                    type="number"
                                    required>
                                </b-form-input>
                                <b v-if="!inputCosto">$ {{ temporal.costo_unitario }}</b>
                            </td>
                            <td>
                                <b-form-input 
                                    @keyup.enter="guardarRegistro()"
                                    v-if="inputUnidades"
                                    v-model="unidades" 
                                    type="number"
                                    required>
                                </b-form-input>
                            </td>
                            <td></td>
                            <td>
                                <button 
                                    class="btn btn-secondary" 
                                    @click="eliminarTemporal" 
                                    v-if="inputUnidades">
                                    <i class="fa fa-minus-circle"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                btnNuevo: true,
                mostrarGuardar: false, //Para mostrar el boton de guardar
                mostrarActualizar: false, //Indicar si se muestra el boton de actualizar remision 
                mostrarEditar: false, //Indicar si se muestran los botones de eliminar y editar
                mostrarTabla: false,
                total_entrada: 0, //Mostrar el total de la remision
                total_unidades: 0,
                items: [], //Registros guardados de la remision
                botonEliminar: false, //Boton para poder eliminar un registro
                isbn: '', //Buscar del libro por ISBN
                respuestaISBN: '', //Indicar si el ISBN es incorrecto
                inputISBN: true, //Mostrar o no el input de ISBN
                inputLibro: true, //Mostrar o no el input de busqueda por titulo
                queryTitulo: '', //Buscar libro por titulo
                resultslibros: [], //Mostrar resultados de la busqueda de libro
                temporal: {}, //Guardar temporalmente los datos de la busqueda del libro
                inputUnidades: false, //Mostrar o no el input de
                unidades: 0, //Asignar el numero de unidades
                editar: false, //Indicar si la remision esta en forma de edicion
                bdentrada: {
                    id: 0,
                    unidades: 0,
                    total: 0,
                }, //Para guardar todos los datos de la remision
                costo_unitario: 0,
                inputCosto: false,
                respuestaCosto: '',
                respuestaUnidades: ''
            }
        },
        methods: {
            //Nueva entrada
            nuevaEntrada(){
                axios.get('/nueva_entrada').then(response => {
                    this.bdentrada = {id: 0, unidades: 0, total: 0,};
                    this.items = [];
                    this.temporal = {};
                    this.total_entrada = 0;
                    this.total_unidades = 0;
                    this.editar = false;
                    this.unidades = 0;
                    this.inputLibro = true;
                    this.inputISBN = true;
                    this.inputUnidades = false;
                    this.mostrarActualizar = false;
                    this.mostrarGuardar = false;
                    this.mostrarEditar = false;
                    this.btnNuevo = false;
                    this.mostrarTabla = true;
                });
            },
            //SE REPITEN
           //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.inicializar();
                    this.temporal = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'ISBN incorrecto');
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
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: 0,
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
                this.inputCosto = true;
            },
            //SE REPITEN FIN
            guardarCosto(){
                if(this.costo_unitario > 0){
                    this.temporal.costo_unitario = this.costo_unitario;
                    this.inputCosto = false;
                    this.inputUnidades = true;
                    this.respuestaCosto = '';
                }
                else{
                    this.makeToast('danger', 'Costo invalido');
                } 
            },
            //Guardar un registro de la entrada
            guardarRegistro(){
                if(this.unidades > 0){
                    this.temporal.entrada_id = 0;
                    this.temporal.unidades = this.unidades;
                    this.temporal.costo_unitario = this.costo_unitario;
                    this.temporal.total = this.unidades * this.temporal.costo_unitario;
                    if(this.editar){
                        this.temporal.entrada_id = this.bdentrada.id;
                    }

                    axios.post('/registro_entrada', this.temporal).then(response => {
                        this.temporal = {
                            id: response.data.registro.id,
                            ISBN: response.data.libro.ISBN,
                            titulo: response.data.libro.titulo,
                            costo_unitario: response.data.registro.costo_unitario,
                            unidades: response.data.registro.unidades,
                            total: response.data.registro.total
                        };
                        this.items.push(this.temporal);
                        this.total_entrada += response.data.registro.total;
                        this.total_unidades += parseInt(response.data.registro.unidades);
                        this.unidades = 0;
                        this.costo_unitario = 0;
                        this.temporal = {};
                        this.inputISBN = true;
                        this.inputLibro = true;
                        this.inputUnidades = false;
                        this.botonEliminar = true;
                        this.mostrarGuardar = true;
                    })
                    .catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentarlo');
                    });
                }
                else{
                    this.makeToast('danger', 'Unidades no validas');
                }
            },
            //Eliminar registro de la remision
            eliminarRegistro(item, i){
                axios.delete('/eliminar_registro_entrada', {params: {id: item.id}}).then(response => {
                    this.items.splice(i, 1);
                    this.total_entrada = this.total_entrada - item.total;
                    this.total_unidades = this.total_unidades - item.unidades;
                    this.bdentrada.total = this.total_entrada;
                    this.bdentrada.unidades = this.total_unidades;
                });
            },
            //Guardar toda la remision
            onSubmit(){
                this.bdentrada.total = this.total_entrada;
                this.bdentrada.unidades = this.total_unidades;
                axios.post('/crear_entrada', this.bdentrada).then(response => {
                    this.bdentrada.id = response.data.id;
                    this.mostrarGuardar = false;
                    this.mostrarEditar = true;
                    this.btnNuevo = true;
                    this.inicializar_guardar();
                });
            },
            //Editar la remision
            editarTEntrada(){
                this.mostrarEditar = false;
                this.mostrarActualizar = true;
                this.inputLibro = true;
                this.inputISBN = true;
                this.botonEliminar = true;
                this.editar = true;
                this.btnNuevo = false; 
            },
            //Guardar cambios de la remision
            actRemision(){
                this.bdentrada.total = this.total_entrada;
                this.bdentrada.unidades = this.total_unidades;
                axios.put('/actualizar_entrada', this.bdentrada).then(response => {
                    this.mostrarActualizar = false;
                    this.btnNuevo = true;
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
                this.inputUnidades = false;
                this.mostrarEditar = true;
                this.botonEliminar = false;
            },
            eliminarTemporal(){
                this.temporal = {};
                this.inputISBN = true;
                this.inputLibro = true;
                this.inputUnidades = false;
                this.queryTitulo = '';
                this.unidades = 0;
            },
            makeToast(variant = null, descripcion) {
                this.$bvToast.toast(descripcion, {
                    title: 'Error',
                    variant: variant,
                    solid: true
                })
            }
        }
    }
</script>