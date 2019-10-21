<template>
    <div>
       <div v-if="listadoLibros"> 
            <b-row>
                <!-- MOSTRAR LIBROS POR EDITORIALES -->
                <b-col sm="5">
                    <b-row>
                        <b-col sm="2">
                            <label for="input-editorial">Editorial</label>
                        </b-col>
                        <b-col sm="10">
                            <b-form-select v-model="editorial" :options="options" @change="mostrarEditoriales()"></b-form-select>
                        </b-col> 
                    </b-row>
                </b-col>
                <!-- MOSTRAR LIBROS POR FECHA -->
                <b-col sm="7">
                    <b-row>
                        <b-col align="right" sm="4">
                            Seleccionar fecha
                        </b-col>
                        <b-col sm="8">
                            <b-input type="date" v-model="fecha" @change="porFecha()"/>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <hr>
            <!-- LISTADO DE DATOS -->
            <b-table 
                responsive
                :items="vendidos" 
                :fields="fields" 
                v-if="vendidos.length > 0" 
                :per-page="perPage"
                :current-page="currentPage"
                id="my-table">
                <template slot="unidades_vendido" slot-scope="row">
                    {{ row.item.unidades_vendido | formatNumber }}
                </template>
                <template slot="total_vendido" slot-scope="row">
                    ${{ row.item.total_vendido | formatNumber }}
                </template>
                <template slot="unidades_pendiente" slot-scope="row">
                    {{ row.item.unidades_pendiente | formatNumber }}
                </template>
                <template slot="total_pendiente" slot-scope="row">
                    ${{ row.item.total_pendiente | formatNumber }}
                </template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="func_detalles(row.item)">Detalles</b-button>
                </template>
            </b-table>
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="vendidos.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="vendidos.length > 0">
            </b-pagination>
        </div>
        <!-- DETALLES -->
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col sm="10"><b>{{ libro }}</b></b-col>
                <b-col sm="2">
                    <b-button variant="secondary" @click="mostrarDetalles = false; listadoLibros = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros"></b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['registersall'],
        data() {
            return {
                fecha: null,
                vendidos: this.registersall,
                fields: [
                    'libro', 
                    {key: 'unidades_vendido', label: 'Unidades vendidas'},
                    {key: 'total_vendido', label: 'Subtotal'},
                    {key: 'unidades_pendiente', label: 'Unidades pendientes'},
                    {key: 'total_pendiente', label: 'Subtotal'},
                    {key: 'detalles', label: ''}
                ],
                listadoLibros: true,
                mostrarDetalles: false,
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
                    { value: '', text: 'MOSTRAR TODO'},
                ],
                perPage: 15,
                currentPage: 1,
                libro: ''
            }
        },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            // MOSTRAR LIBROS POR EDITORIAL
            mostrarEditoriales(){
                if(this.editorial.length > 0){
                    axios.get('/porEditorialVendidos', {params: {editorial: this.editorial}}).then(response => {
                        this.vendidos = [];
                        this.vendidos = response.data
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.getTodo();
                } 
            },
            // OBTENER POR FECHA
            porFecha(){
                axios.get('/obtener_por_fecha', {params: {fecha: this.fecha}}).then(response => {
                    this.vendidos = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // OBTENER DETALLES DEL LIBRO VENDIDO
            func_detalles(vendido){
                this.listadoLibros = false;
                this.mostrarDetalles = true;
                axios.get('/detalles_vendidos', {params: {fecha: this.fecha, libro_id: vendido.libro_id}}).then(response => {
                    this.libro = vendido.libro;
                    this.registros = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // OBTENER TODOS LOS LIBROS VENDIDOS
            getTodo(){
                axios.get('/obtener_vendidos').then(response => {
                    this.vendidos = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            makeToast(variante = null, descripcion){
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variante,
                    solid: true
                });
            }
        }
    }
</script>
