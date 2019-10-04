<template>
    <div>
        <div v-if="listadoEntradas">
            <b-row>
                <b-col sm="3">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-folio">Folio</label>
                        </b-col>
                        <b-col sm="10">
                            <b-form-input 
                                id="input-folio" 
                                v-model="folio" 
                                @keyup.enter="porFolio">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="3">
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-editorial">Editorial</label>
                        </b-col>
                        <b-col sm="9">
                            <b-form-select v-model="editorial" :options="options" @change="mostrarEditoriales"></b-form-select>
                        </b-col> 
                    </b-row>
                </b-col>
                <b-col sm="3">
                    <b-button v-if="role_id == 3" variant="success" @click="nuevaEntrada"><i class="fa fa-plus"></i> Registrar entrada</b-button>
                </b-col>
                <b-col sm="3" align="right">
                    <b-button variant="info" :disabled="loadRegisters" @click="getTodo">
                        <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table 
                v-if="!mostrarDetalles && !mostrarEA && entradas.length > 0" 
                :items="entradas" 
                :fields="fields"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
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
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
                <template slot="editar" slot-scope="row">
                    <b-button 
                        @click="editarEntrada(row.item, row.index)"
                        v-if="role_id == 3"
                        variant="warning">
                        <i class="fa fa-pencil"></i> Editar
                    </b-button>
                </template>
            </b-table>
            <b-pagination
                v-model="currentPage"
                :total-rows="entradas.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="entradas.length > 0"
            ></b-pagination>
        </div>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col sm="1">
                    <label><b>Folio:</b></label><br>
                    <label><b>Editorial:</b></label>
                </b-col>
                <b-col sm="4">
                    <label>{{entrada.folio}}</label><br>
                    <label>{{entrada.editorial}}</label>
                </b-col>
                <b-col sm="4">
                    <label><b>Unidades:</b> {{ entrada.unidades | formatNumber }}</label>
                </b-col>
                <b-col sm="3" align="right">
                    <b-button 
                        variant="secondary" 
                        @click="mostrarDetalles = false; listadoEntradas = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table v-if="registros.length > 0" :items="registros" :fields="fieldsR">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
            </b-table>
        </div>
        <div v-if="mostrarEA">
            <div class="text-right">
                <b-button variant="secondary" @click="mostrarEA = false; listadoEntradas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
            </div>
            <hr>
            <b-row>
                <b-col sm="1">
                    <label>Folio</label><br>
                    <label>Editorial</label>
                </b-col>
                <b-col sm="5">
                    <b-form-input v-model="entrada.folio" autofocus :disabled="load" :state="stateN" @change="guardarNum"></b-form-input>
                    <b-form-select v-model="entrada.editorial" autofocus :disabled="load" :state="stateE" :options="options"></b-form-select>
                </b-col>
                <b-col sm="3" align="right">
                    <label><b>Unidades:</b> {{ total_unidades | formatNumber }}</label>
                </b-col>
                <b-col sm="3" class="text-right">
                    <b-button 
                        @click="actEntrada" 
                        variant="success"
                        :disabled="load"
                        v-if="registros.length > 0 && agregar == false">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar  cambios' : 'Guardando' }}
                    </b-button>
                    <b-button 
                        @click="onSubmit" 
                        variant="success"
                        :disabled="load"
                        v-if="registros.length > 0 && agregar == true && entrada.folio.length > 0">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros" :fields="fieldsRE">
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                <!-- <template slot="eliminar" slot-scope="row">
                    <b-button v-if="row.item.entrada_id != 1" variant="danger" @click="eliminarRegistro(row.item, row.index)">
                        <i class="fa fa-minus-circle"></i>
                    </b-button>
                </template> -->
            </b-table>
            <b-row>
                <b-col sm="1"></b-col>
                <b-col sm="3">
                    <b-input
                        autofocus
                        v-model="isbn"
                        @keyup.enter="buscarLibroISBN()"
                        v-if="inputISBN"
                    ></b-input>
                    <b v-if="!inputISBN">{{ temporal.libro.ISBN }}</b>
                </b-col>
                <b-col sm="5">
                    <b-input
                        autofocus
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
                    <b v-if="!inputLibro">{{ temporal.libro.titulo }}</b>
                </b-col>
                <b-col sm="2">
                    <b-form-input 
                        autofocus
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
    export default {
        props: ['role_id'],
        data() {
            return {
                entradas: [],
                registros: [],
                editorial: null,
                options: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'CAMBRIDGE', text: 'CAMBRIDGE' },
                    { value: 'CENGAGE', text: 'CENGAGE' },
                    { value: 'EMPRESER', text: 'EMPRESER' },
                    { value: 'EXPRESS PUBLISHING', text: 'EXPRESS PUBLISHING'},
                    { value: 'HELBLING LANGUAGES', text: 'HELBLING LANGUAGES'},
                    { value: 'MAJESTIC', text: 'MAJESTIC'},
                    { value: 'MC GRAW - MAJESTIC', text: 'MC GRAW - MAJESTIC'},
                    { value: 'MCGRAW HILL', text: 'MCGRAW HILL'},
                    { value: 'RICHMOND', text: 'RICHMOND'},
                    { value: 'IMPRESOS DE CALIDAD', text: 'IMPRESOS DE CALIDAD'},
                    { value: 'ENGLISH TEXBOOK', text: 'ENGLISH TEXBOOK'},
                    { value: 'BOOKMART MÉXICO', text: 'BOOKMART MÉXICO' },
                    { value: 'ANGLO PUBLISHING', text: 'ANGLO PUBLISHING' },
                    { value: 'LAROUSSE', text: 'LAROUSSE' },
                    { value: 'TODAS', text: 'MOSTRAR TODO'},
                ],
                fields: [
                    {key: 'id', label: 'N.'}, 
                    'folio',
                    'editorial',
                    'unidades',
                    {key: 'created_at', label: 'Fecha de creación'},
                    {key: 'detalles', label: ''},  
                    {key: 'descargar', label: ''}, 
                    {key: 'editar', label: ''}
                ],
                fieldsR: [
                    {key: 'id', label: 'N.'}, 
                    {key: 'isbn', label: 'ISBN'}, 
                    {key: 'titulo', label: 'Libro'}, 
                    'unidades',
                ],
                fieldsRE: [
                    {key: 'id', label: 'N.'}, 
                    {key: 'ISBN', label: 'ISBN'}, 
                    {key: 'titulo', label: 'Libro'}, 
                    'unidades', 
                    {key: 'eliminar', label: ''}
                ],
                mostrarDetalles: false,
                fechaFinal: '',
                entrada: {
                    id: 0,
                    unidades: 0,
                    total: 0,
                    folio: '',
                    editorial: null,
                    items: [],
                    nuevos: []
                },
                mostrarEA: false,
                isbn: '',
                inputISBN: true,
                temporal: {},
                queryTitulo: '',
                inputLibro: true,
                resultslibros: [],
                inputUnidades: false,
                unidades: null,
                total_unidades: 0,
                stateN: null,
                stateE: null,
                load: false,
                posicion: null,
                listadoEntradas: true,
                agregar: false,
                nuevos: [],
                total: 0,
                estado: false,
                folio: '',
                perPage: 15,
                currentPage: 1,
                loadRegisters: false
            }
        },
        // created: function(){
        //     this.getTodo();
        // },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            },
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        }, 
        methods: {
            porFolio(){
                axios.get('/buscarFolio', {params: {folio: this.folio}}).then(response => {
                    if(response.data.id != undefined){
                        this.entradas = [];
                        this.entradas.push(response.data);
                    }
                    else{
                        this.makeToast('warning', 'El folio no existe');
                    }
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            nuevaEntrada(){
                this.listadoEntradas = false;
                this.agregar = true;
                this.mostrarEA = true;
                this.entrada = {
                    id: 0,
                    unidades: 0,
                    total: 0,
                    folio: '',
                    editorial: '',
                    items: [],
                    nuevos: []
                };
                this.registros = [];
                this.eliminarTemporal();
                this.total_unidades = 0;
            },
            onSubmit(){
                this.load = true;
                this.entrada.unidades = this.total_unidades;
                this.entrada.items = this.registros;
                if(this.entrada.editorial.length > 3){
                    this.stateE = null;
                    axios.post('/crear_entrada', this.entrada).then(response => {
                        this.entradas.push(response.data);
                        this.load = false;
                        this.mostrarEA = false;
                        this.listadoEntradas = true;
                        this.makeToast('success', 'La entrada se creo correctamente');
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.stateE = false;
                    this.load = false;
                    this.makeToast('danger', 'Seleccionar editorial');
                }
            },
            getTodo(){
                var ffinal = moment();
                this.fechaFinal = ffinal;
                this.loadRegisters = true;
                axios.get('/all_entradas').then(response => {
                    this.entradas = response.data;
                    this.loadRegisters = false;
                    this.acumular();
                }).catch(error => {
                    this.loadRegisters = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            detallesEntrada(entrada){
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.listadoEntradas = false;
                    this.mostrarDetalles = true;
                    this.entrada.folio = response.data.entrada.folio;
                    this.entrada.editorial = response.data.entrada.editorial;
                    this.entrada.total = response.data.entrada.total;
                    this.entrada.unidades = response.data.entrada.unidades;
                    this.registros = response.data.registros;
                });
            },
            editarEntrada(entrada, i){
                this.posicion = i;
                this.agregar = false;
                this.stateN = null;
                this.entrada.nuevos = [];
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.eliminarTemporal();
                    this.listadoEntradas = false;
                    this.mostrarEA = true;
                    this.entrada.id = response.data.entrada.id;
                    this.entrada.folio = response.data.entrada.folio;
                    this.entrada.editorial = response.data.entrada.editorial;
                    this.entrada.total = response.data.entrada.total;
                    this.entrada.unidades = response.data.entrada.unidades;
                    this.total_unidades = this.entrada.unidades;
                    this.registros = response.data.registros;
                });
            },
            //Eliminar registro de la entrada
            eliminarRegistro(item, i){
                if(this.agregar == false){
                    axios.delete('/eliminar_registro_entrada', {params: {id: item.id}}).then(response => {
                        this.restasUnidades(item, i);
                    });
                } 
                else{
                    this.restasUnidades(item, i);
                }
                
            },
            restasUnidades(item, i){
                this.registros.splice(i, 1);
                this.total_unidades = this.total_unidades - item.unidades;
                this.entrada.unidades = this.total_unidades;
            },
            //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.datosLibro(response.data);
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
                    libro: {
                        ISBN: libro.ISBN,
                        titulo: libro.titulo,
                    },
                    unidades: 0,
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
                    this.temporal.unidades = this.unidades;
                    if(this.agregar == false){
                        this.nuevos.push(this.temporal);
                    }
                    this.registros.push(this.temporal);
                    this.total_unidades += parseInt(this.temporal.unidades);
                    this.unidades = null;
                    this.temporal = {};
                    this.isbn = '';
                    this.queryTitulo = '',
                    this.inputISBN = true;
                    this.inputLibro = true;
                    this.inputUnidades = false;
                }
                else{
                    this.makeToast('danger', 'Unidades no validas');
                }
            },
            actEntrada(){
                this.entrada.unidades = this.total_unidades;
                this.entrada.nuevos = this.nuevos;
                this.load = true;
                this.stateE = null;
                axios.put('/actualizar_entrada', this.entrada).then(response => {
                    this.makeToast('success', 'La entrada se ha actualizado');
                    this.load = false;
                    this.entradas[this.posicion] = response.data;
                    this.mostrarEA = false;
                    this.listadoEntradas = true;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            eliminarTemporal(){
                this.temporal = {};
                this.inputISBN = true;
                this.inputLibro = true;
                this.inputUnidades = false;
                this.queryTitulo = '';
                this.unidades = null;
                this.isbn = '';
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
            mostrarEditoriales(){
                if(this.editorial.length > 0){
                    axios.get('/mostrarEditoriales', {params: {editorial: this.editorial}}).then(response => {
                        this.entradas = [];
                        this.entradas = response.data;
                        this.acumular();
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.getTodo();
                } 
            },
            acumular(){
                this.total = 0;
                this.entradas.forEach(entrada => {
                    this.total += entrada.unidades;
                });
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