<template>
    <div>
        <b-alert v-if="entradas.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay entradas
        </b-alert>
        <b-table v-if="!mostrarDetalles && !mostrarEditar && entradas.length > 0" :items="entradas" :fields="fields">
            <template slot="detalles" slot-scope="row">
                <b-button variant="info" @click="detallesEntrada(row.item)">Ver detalles</b-button>
            </template>
            <template slot="descargar" slot-scope="row">
                <b-button 
                    variant="primary"
                    :href="'/imprimirEntrada/' + row.item.id">
                    Descargar
                </b-button>
            </template>
            <template slot="created_at" slot-scope="row">
                {{ row.item.created_at | moment }}
            </template>
            <template slot="editar" slot-scope="row">
                <b-button 
                    @click="editarEntrada(row.item, row.index)"
                    variant="warning" 
                    v-if="role_id == 3 && fechaFinal.diff(row.item.created_at, 'days') < 5">
                    <i class="fa fa-pencil"></i> Editar
                </b-button>
            </template>
        </b-table>
        <div v-if="mostrarDetalles">
            <div class="text-right">
                <b-button variant="secondary" @click="mostrarDetalles = false"><i class="fa fa-mail-reply"></i> Entradas</b-button>
            </div>
            <hr>
            <b-table v-if="registros.length > 0" :items="registros" :fields="fieldsR">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
            </b-table>
        </div>
        <div v-if="mostrarEditar">
            <div class="text-right">
                <b-button variant="secondary" @click="mostrarEditar = false"><i class="fa fa-mail-reply"></i> Entradas</b-button>
            </div>
            <hr>
            <b-row>
                <b-col sm="1">
                    <label>Folio</label><br>
                    <label>Editorial</label>
                </b-col>
                <b-col sm="5">
                    <b-form-input v-model="entrada.folio" :state="stateN" @change="guardarNum"></b-form-input>
                    <b-form-input v-model="entrada.editorial" :state="stateE"></b-form-input>
                </b-col>
                <b-col sm="3" align="right">
                    <label><b>Unidades:</b> {{ total_unidades }}</label>
                </b-col>
                <b-col sm="3" class="text-right">
                    <b-button 
                        @click="actRemision" 
                        variant="success"
                        :disabled="load"
                        v-if="registros.length > 0">
                        <i class="fa fa-check"></i> {{ !load ? 'Actualizar' : 'Actualizando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros" :fields="fieldsRE">
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="eliminar" slot-scope="row">
                    <b-button variant="danger" @click="eliminarRegistro(row.item, row.index)">
                        <i class="fa fa-minus-circle"></i>
                    </b-button>
                </template>
            </b-table>
            <b-row>
                <b-col sm="1"></b-col>
                <b-col sm="3">
                    <b-input
                        v-model="isbn"
                        @keyup.enter="buscarLibroISBN()"
                        v-if="inputISBN"
                    ></b-input>
                    <b v-if="!inputISBN">{{ temporal.ISBN }}</b>
                </b-col>
                <b-col sm="5">
                    <b-input
                        v-model="queryTitulo"
                        @keyup="mostrarLibros"
                        v-if="inputLibro">
                    </b-input>
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
                </b-col>
                <b-col sm="2">
                    <b-form-input 
                        @keyup.enter="guardarRegistro()"
                        v-if="inputUnidades"
                        v-model="unidades" 
                        type="number"
                        required>
                    </b-form-input>
                </b-col>
                <b-col sm="1">
                    <b-button 
                        variant="secondary"
                        @click="eliminarTemporal" 
                        v-if="inputUnidades">
                        <i class="fa fa-minus-circle"></i>
                    </b-button>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
    // moment.locale('es');
    export default {
        props: ['role_id'],
        data() {
            return {
                entradas: [],
                registros: [],
                fields: [
                    {key: 'id', label: 'N.'}, 
                    'folio',
                    'editorial',
                    {key: 'created_at', label: 'Fecha de creación'},
                    {key: 'detalles', label: ''},  
                    {key: 'descargar', label: ''}, 
                    {key: 'editar', label: ''}
                ],
                fieldsR: [{key: 'id', label: 'N.'}, {key: 'isbn', label: 'ISBN'}, {key: 'titulo', label: 'Libro'}, 'unidades'],
                fieldsRE: [{key: 'id', label: 'N.'}, {key: 'ISBN', label: 'ISBN'}, {key: 'titulo', label: 'Libro'}, 'unidades', 'eliminar'],
                mostrarDetalles: false,
                fechaFinal: '',
                entrada: {},
                mostrarEditar: false,
                isbn: '',
                inputISBN: true,
                temporal: {},
                queryTitulo: '',
                inputLibro: true,
                resultslibros: [],
                inputUnidades: false,
                unidades: 0,
                total_unidades: 0,
                stateN: null,
                stateE: null,
                load: false,
                posicion: null,
            }
        },
        created: function(){
            this.getTodo();
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            }
        }, 
        methods: {
            getTodo(){
                var ffinal = moment();
                this.fechaFinal = ffinal;
                axios.get('/all_entradas').then(response => {
                    this.entradas = response.data;
                });
            },
            detallesEntrada(entrada){
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.mostrarDetalles = true;
                    this.registros = response.data.registros;
                });
            },
            editarEntrada(entrada, i){
                this.posicion = i;
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.mostrarEditar = true;
                    this.entrada = response.data.entrada;
                    this.total_unidades = this.entrada.unidades;
                    this.registros = response.data.registros;
                });
            },
            //Eliminar registro de la entrada
            eliminarRegistro(item, i){
                axios.delete('/eliminar_registro_entrada', {params: {id: item.id}}).then(response => {
                    this.registros.splice(i, 1);
                    this.total_unidades = this.total_unidades - item.unidades;
                    this.entrada.unidades = this.total_unidades;
                });
            },
            //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.temporal = response.data;
                    this.ini_1();
                }).catch(error => {
                    this.makeToast('danger', 'ISBN incorrecto');
                });
            }, 
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
               } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.ini_1();
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: 0,
                    unidades: 0,
                    total: 0
                };
            },
            ini_1(){
                this.inputLibro = false;
                this.inputISBN = false;
                this.inputUnidades = true;
                this.resultslibros = [];
            },
            //Guardar un registro de la entrada
            guardarRegistro(){
                if(this.unidades > 0){
                    this.temporal.entrada_id = this.entrada.id;
                    this.temporal.unidades = this.unidades;
                    axios.post('/registro_entrada', this.temporal).then(response => {
                        this.temporal = {
                            id: response.data.registro.id,
                            libro: {
                                ISBN: response.data.libro.ISBN,
                                titulo: response.data.libro.titulo,
                            },
                            unidades: response.data.registro.unidades,
                        };
                        this.registros.push(this.temporal);
                        this.total_unidades += parseInt(response.data.registro.unidades);
                        this.unidades = 0;
                        this.temporal = {};
                        this.isbn = '';
                        this.queryTitulo = '',
                        this.inputISBN = true;
                        this.inputLibro = true;
                        this.inputUnidades = false;
                        // this.botonEliminar = true;
                    })
                    .catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentarlo');
                    });
                }
                else{
                    this.makeToast('danger', 'Unidades no validas');
                }
            },
            actRemision(){
                this.entrada.unidades = this.total_unidades;
                if(this.entrada.editorial.length > 0){
                    this.load = true;
                    this.stateE = null;
                    axios.put('/actualizar_entrada', this.entrada).then(response => {
                        this.makeToast('success', 'La entrada se ha actualizado');
                        this.load = false;
                        this.entradas[this.posicion] = response.data;
                        this.mostrarEditar = false;
                    });
                }
                else{
                    this.stateE = false;
                    this.makeToast('danger', 'Definir editorial');
                }
            },
            eliminarTemporal(){
                this.temporal = {};
                this.inputISBN = true;
                this.inputLibro = true;
                this.inputUnidades = false;
                this.queryTitulo = '';
                this.unidades = 0;
            },
            guardarNum(){
                if(this.entrada.folio.length > 0){
                    axios.get('/buscarFolio', {params: {folio: this.entrada.folio}}).then(response => {
                        if(response.data.id != undefined){
                            this.stateN = false;
                            this.makeToast('danger', 'El folio ya existe');
                        }
                        else{
                            this.stateN = null;
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.stateN = false;
                    this.makeToast('danger', 'Definir folio');
                }
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