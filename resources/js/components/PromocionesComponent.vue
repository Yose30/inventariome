<template>
    <div>
        <div v-if="listadoPromociones">
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
                <b-col sm="6">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-plantel">Plantel</label>
                        </b-col>
                        <b-col sm="9">
                            <b-input v-model="queryPlantel" @keyup="porPlantel"></b-input>
                        </b-col>
                    </b-row>
                </b-col> 
                <b-col sm="3">
                    <div align="right">
                        <b-button v-if="role_id == 3" variant="success" @click="registrarPromocion">
                            <i class="fa fa-plus"></i> Registrar promoción
                        </b-button>
                    </div>
                </b-col>  
            </b-row> 
            <hr>
        </div>

        <div v-if="listadoPromociones">
            <b-table :items="promotions" :fields="fields">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="created_at" slot-scope="row">{{ row.item.created_at | moment }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="detallesPromotion(row.item)">Detalles</b-button>
                </template>
            </b-table>
        </div>

        <div v-if="mostrarRegistrar">
            <b-row>
                <b-col align="right">
                    <b-button v-if="registros.length > 0" variant="success" @click="guardarPromocion" :disabled="load">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </b-col>
                <b-col align="right">
                    <b-button variant="secondary" @click="listadoPromociones = true; mostrarRegistrar = false;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-row>
                <b-col sm="4">
                    <label><b>Plantel</b>: <b id="txtObligatorio">*</b></label><br>
                    <label><b>Descripción (Opcional)</b>:</label><br>
                </b-col>
                <b-col>
                    <b-input type="text" v-model="promocion.plantel" :state="state"></b-input>
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
            </b-table>
            <b-row>
                <b-col sm="4">
                    <label for="input-isbn">ISBN</label>
                    <b-input
                        id="input-isbn"
                        v-model="temporal.ISBN"
                        @keyup.enter="buscarLibroISBN"
                        v-if="inputISBN"
                        :disabled="load"
                    ></b-input>
                    <br><b v-if="!inputISBN">{{ temporal.ISBN }}</b>
                </b-col>
                <b-col sm="4">
                    <label for="input-libro">Libro</label>
                    <b-input
                        id="input-libro"
                        v-model="temporal.titulo"
                        @keyup="mostrarLibros"
                        v-if="inputLibro"
                        :disabled="load">
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
                    <br><b v-if="!inputLibro">{{ temporal.titulo }}</b>
                </b-col>
                <b-col sm="2">
                    <label for="input-unidades">Unidades</label>
                    <b-form-input 
                        id="input-unidades"
                        @keyup.enter="guardarRegistro"
                        v-if="inputUnidades"
                        v-model="temporal.unidades" 
                        type="number"
                        required
                        :disabled="load">
                    </b-form-input>
                </b-col>
                <b-col sm="2">
                    <b-button 
                        variant="secondary"
                        @click="eliminarTemporal" 
                        v-if="inputUnidades"
                        :disabled="load">
                        <i class="fa fa-minus-circle"></i> Eliminar
                    </b-button>
                </b-col>
            </b-row>
        </div>

        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
        
                </b-col>
                <b-col align="right">
                    <b-button variant="secondary" @click="listadoPromociones = true; mostrarDetalles = false;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <b-table :items="promocion.departures" :fields="fieldsD">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                listadoPromociones: true,
                mostrarRegistrar: false,
                promotions: [],
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
                    unidades: 0,
                    piezas: 0
                },
                promocion: {
                    plantel: '',
                    descripcion: '',
                    unidades: 0,
                    departures: []
                },
                inputISBN: true,
                inputLibro: true,
                inputUnidades: false,
                resultslibros: [],
                state: null,
                mostrarDetalles: false,
                folio: null,
                queryPlantel: ''

            }
        },
        created: function(){
			this.obtenerPromotions();
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            }
        },
        methods: {
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
            porPlantel(){
                if(this.queryPlantel != ''){
                    axios.get('/buscar_plantel', {params: {queryPlantel: this.queryPlantel}}).then(response => {
                        this.promotions = [];
                        this.promotions = response.data;
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.obtenerPromotions();
                }
            },
            obtenerPromotions(){
                axios.get('/obtener_promociones').then(response => {
                    this.promotions = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            registrarPromocion(){
                this.listadoPromociones = false;
                this.eliminarTemporal();
                this.promocion = {
                    plantel: '',
                    descripcion: '',
                    unidades: 0,
                    departures: []
                };
                this.registros = [];
                this.mostrarRegistrar = true;
            },
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
            eliminarRegistro(i){
                this.registros.splice(i, 1);
            },
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
            mostrarLibros(){
                if(this.temporal.titulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.temporal.titulo}}).then(response => {
                        this.resultslibros = response.data;
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
               }
            },
            datosLibro(libro){
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    unidades: 0,
                    piezas: libro.piezas
                };
                this.resultslibros = [];
                this.inputISBN = false;
                this.inputLibro = false;
                this.inputUnidades = true;
            },
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
            eliminarTemporal(){
                this.temporal = {
                    id: 0,
                    ISBN: '',
                    titulo: '',
                    unidades: 0,
                    piezas: 0
                };
                this.inputUnidades = false;
                this.inputLibro = true;
                this.inputISBN = true;
            },
            detallesPromotion(promotion){
                this.promocion.departures = [];
                axios.get('/obtener_departures', {params: {promotion_id: promotion.id}}).then(response => {
                    this.promocion.departures = response.data;
                    this.listadoPromociones = false;
                    this.mostrarDetalles = true;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
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

<style>
    #txtObligatorio {
        color: red;
    }
</style>