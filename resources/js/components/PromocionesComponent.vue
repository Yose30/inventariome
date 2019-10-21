<template>
    <div>
        <div v-if="listadoPromociones">
            <b-row>
                <!-- BUSCAR PROMOCION POR FOLIO -->
                <b-col sm="3">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-folio">Folio</label>
                        </b-col>
                        <b-col sm="10">
                            <b-form-input 
                                id="input-folio" 
                                v-model="folio" 
                                @keyup.enter="porFolio()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <!-- BUSCAR PROMOCION POR PLANTEL -->
                <b-col sm="6">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-plantel">Plantel</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryPlantel" @keyup="porPlantel()"></b-input>
                        </b-col>
                    </b-row>
                </b-col> 
                <!-- CREAR UNA PROMOCION -->
                <b-col sm="3">
                    <b-button v-if="role_id == 3" variant="success" @click="registrarPromocion()">
                        <i class="fa fa-plus"></i> Registrar promoción
                    </b-button>
                </b-col>  
                <!-- <b-col sm="3" align="right">
                    <b-button variant="info" :disabled="loadRegisters" @click="obtenerPromotions">
                        <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                    </b-button>
                </b-col> -->
            </b-row> 
            <hr>
        </div>
        <!-- LISTADO DE PROMOCIONES -->
        <div v-if="listadoPromociones">
            <b-table 
                responsive
                :items="promotions" 
                :fields="fields"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage"
                v-if="promotions.length > 0">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="created_at" slot-scope="row">{{ row.item.created_at | moment }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="detallesPromotion(row.item)">Detalles</b-button>
                </template>
            </b-table>
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="promotions.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="promotions.length > 0">
            </b-pagination>
        </div>
        <!-- MOSTRAR DETALLES DE LA PROMOCIÓN -->
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h6><b>Folio</b>: {{ promocion.folio }}</h6>
                    <h6><b>Plantel</b>: {{ promocion.plantel }}</h6>
                    <h6><b>Fecha</b>: {{ promocion.created_at | moment }}</h6>
                </b-col>
                <b-col sm="3" align="right">
                    <b-button variant="secondary" @click="listadoPromociones = true; mostrarDetalles = false;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <h6 v-if="promocion.descripcion != null"><b>Descripción</b>: {{ promocion.descripcion }}</h6>
            <b-table :items="promocion.departures" :fields="fieldsD">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="3"></th>
                        <th>{{ promocion.unidades }}</th>
                    </tr>
                </template>
            </b-table>
        </div>
        <!-- CREAR UNA PROMOCIÓN -->
        <div v-if="mostrarRegistrar">
            <b-row>
                <b-col sm="6"><h4 style="color: #170057">Registrar promoción</h4></b-col>
                <b-col sm="3" align="right">
                    <b-button v-if="registros.length > 0" variant="success" @click="guardarPromocion()" :disabled="load">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </b-col>
                <b-col sm="3" align="right">
                    <b-button variant="secondary" @click="listadoPromociones = true; mostrarRegistrar = false;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-row class="col-md-10">
                <b-col sm="4">
                    <label><b>Plantel</b>: <b id="txtObligatorio">*</b></label><br>
                    <label><b>Descripción (Opcional)</b>:</label><br>
                </b-col>
                <b-col>
                    <b-input type="text" autofocus v-model="promocion.plantel" :state="state"></b-input>
                    <b-input type="text" v-model="promocion.descripcion"></b-input>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros" :fields="fieldsR">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.titulo }}</template>
                <template slot="eliminar" slot-scope="row">
                    <b-button variant="danger" @click="eliminarRegistro(row.index)">
                        <i class="fa fa-minus-circle"></i>
                    </b-button>
                </template>
                <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="1"></th>
                            <th>ISBN</th>
                            <th>Libro</th>
                            <th>Unidades</th>
                        </tr>
                        <tr>
                            <th colspan="1"></th>
                            <th>
                                <b-input
                                    id="input-isbn"
                                    autofocus
                                    v-model="temporal.ISBN"
                                    @keyup.enter="buscarLibroISBN()"
                                    v-if="inputISBN"
                                    :disabled="load"
                                ></b-input>
                                <label v-if="!inputISBN">{{ temporal.ISBN }}</label>
                            </th>
                            <th>
                                <b-input
                                    id="input-libro"
                                    autofocus
                                    v-model="temporal.titulo"
                                    @keyup="mostrarLibros()"
                                    v-if="inputLibro"
                                    :disabled="load">
                                </b-input>
                                <div class="list-group" v-if="resultslibros.length" id="listaBL">
                                    <a 
                                        href="#" 
                                        v-bind:key="i" 
                                        class="list-group-item list-group-item-action" 
                                        v-for="(libro, i) in resultslibros" 
                                        @click="datosLibro(libro)">
                                        {{ libro.titulo }}
                                    </a>
                                </div>
                                <label v-if="!inputLibro">{{ temporal.titulo }}</label>
                            </th>
                            <th>
                                <b-form-input 
                                    id="input-unidades"
                                    autofocus
                                    @keyup.enter="guardarRegistro()"
                                    v-if="inputUnidades"
                                    v-model="temporal.unidades" 
                                    type="number"
                                    required
                                    :disabled="load">
                                </b-form-input>
                            </th>
                            <th>
                                <b-button 
                                    variant="secondary"
                                    @click="eliminarTemporal()" 
                                    v-if="inputUnidades"
                                    :disabled="load">
                                    <i class="fa fa-minus-circle"></i>
                                </b-button>
                            </th>
                        </tr>
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['role_id', 'registersall'],
        data() {
            return {
                listadoPromociones: true,
                mostrarRegistrar: false,
                promotions: this.registersall,
                fields: [
                    {key: 'index', label: 'N.'}, 
                    'folio', 
                    'plantel', 
                    'unidades',
                    {key: 'created_at', label: 'Fecha de creación'},
                    {key: 'detalles', label: ''}
                ],
                load: false,
                registros: [],
                fieldsR: [
                    {key: 'index', label: 'N.'}, 
                    {key: 'ISBN', label: 'ISBN'}, 
                    {key: 'titulo', label: 'Libro'}, 
                    'unidades', 'eliminar'
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'}, 
                    {key: 'ISBN', label: 'ISBN'}, 
                    {key: 'titulo', label: 'Libro'}, 
                    'unidades'
                ],
                temporal: {
                    id: 0,
                    ISBN: '',
                    titulo: '',
                    unidades: null,
                    piezas: 0
                },
                promocion: {
                    folio: '',
                    plantel: '',
                    descripcion: '',
                    unidades: 0,
                    created_at: '',
                    departures: []
                },
                inputISBN: true,
                inputLibro: true,
                inputUnidades: false,
                resultslibros: [],
                state: null,
                mostrarDetalles: false,
                folio: null,
                queryPlantel: '',
                perPage: 15,
                currentPage: 1,
                loadRegisters: false
            }
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            }
        },
        methods: {
            // BUSCAR PROMOCIÓN POR FOLIO
            porFolio(){
                axios.get('/buscar_folio_promo', {params: {folio: this.folio}}).then(response => {
                    if(response.data.id != undefined){
                        this.promotions = [];
                        this.promotions.push(response.data);
                    }
                    else{
                        this.makeToast('warning', 'El folio no existe');
                    }
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // BUSCAR POR PLANTEL
            porPlantel(){
                if(this.queryPlantel != ''){
                    axios.get('/buscar_plantel', {params: {queryPlantel: this.queryPlantel}}).then(response => {
                        this.promotions = [];
                        this.promotions = response.data;
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
            },
            // INICIALIZAR PARA CREAR UNA PROMOCIÓN
            registrarPromocion(){
                this.listadoPromociones = false;
                this.eliminarTemporal();
                this.promocion = {
                    folio: '',
                    plantel: '',
                    descripcion: '',
                    unidades: 0,
                    created_at: '',
                    departures: []
                };
                this.registros = [];
                this.mostrarRegistrar = true;
            },
            // MOSTRAR DETALLES DE LA PROMOCIÓN
            detallesPromotion(promotion){
                this.promocion.departures = [];
                axios.get('/obtener_departures', {params: {promotion_id: promotion.id}}).then(response => {
                    this.promocion.departures = response.data.departures;
                    this.promocion.folio = response.data.folio;
                    this.promocion.plantel = response.data.plantel;
                    this.promocion.unidades = response.data.unidades;
                    this.promocion.descripcion = response.data.descripcion;
                    this.promocion.created_at = response.data.created_at;
                    this.listadoPromociones = false;
                    this.mostrarDetalles = true;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // GUARDAR LA PROMOCIÓN
            guardarPromocion(){
                if(this.promocion.plantel.length > 2){
                    this.state = null;
                    this.load = true;
                    this.promocion.departures = this.registros;
                    axios.post('/guardar_promocion', this.promocion).then(response => {
                        this.load = false;
                        this.promotions.push(response.data);
                        this.mostrarRegistrar = false;
                        this.listadoPromociones = true;
                    })
                    .catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.state = false;
                    this.makeToast('warning', 'Campo obligatorio');
                }
            },
            // ELIMINAR REGISTRO DEL ARRAY
            eliminarRegistro(i){
                this.registros.splice(i, 1);
            },
            // BUSCAR LIBRO POR ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.temporal.ISBN}}).then(response => {
                    this.temporal.id = response.data.id;
                    this.temporal.ISBN = response.data.ISBN;
                    this.temporal.titulo = response.data.titulo;
                    this.temporal.piezas = response.data.piezas;
                    this.inputISBN = false;
                    this.inputLibro = false;
                    this.inputUnidades = true;
                }).catch(error => {
                    this.makeToast('danger', 'ISBN incorrecto');
                });
            },
            // MOSTRAR COINCIDENCIA DE LIBROS
            mostrarLibros(){
                if(this.temporal.titulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.temporal.titulo}}).then(response => {
                        this.resultslibros = response.data;
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
               }
            },
            // SELECCIONAR LIBRO
            datosLibro(libro){
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    unidades: null,
                    piezas: libro.piezas
                };
                this.resultslibros = [];
                this.inputISBN = false;
                this.inputLibro = false;
                this.inputUnidades = true;
            },
            // VERIFICAR UNIDADES
            guardarRegistro(){
                if(this.temporal.unidades > 0){
                    if(this.temporal.unidades <= this.temporal.piezas){
                        this.registros.push(this.temporal);
                        this.eliminarTemporal();
                    }
                    else{
                        this.makeToast('warning', `${this.temporal.piezas} unidades en existencia`);
                    }
                }
                else{
                    this.makeToast('warning', 'Unidades invalidas');
                }
            },
            // ELIMINAR REGISTRO TEMPORAL
            eliminarTemporal(){
                this.temporal = {
                    id: 0,
                    ISBN: '',
                    titulo: '',
                    unidades: null,
                    piezas: 0
                };
                this.inputUnidades = false;
                this.inputLibro = true;
                this.inputISBN = true;
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
    #txtObligatorio {
        color: red;
    }
    #listaBL{
        position: absolute;
        z-index: 100
    }
</style>