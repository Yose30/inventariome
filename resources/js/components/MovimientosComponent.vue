<template>
    <div>
        <check-connection-component></check-connection-component>
        <b-row>
            <b-col sm="5">
                <b-row>
                    <b-col sm="2"><label>Editorial</label></b-col>
                    <b-col sm="10">
                        <b-form-select v-model="queryEMov" :options="options" @change="editorialMov()"></b-form-select>
                    </b-col>
                </b-row><br>
                <b-row>
                    <b-col sm="3"><label>Lista por</label></b-col>
                    <b-col sm="9">
                        <b-form-select v-model="selected" :options="tipos" @change="editorialMov()"></b-form-select>
                    </b-col>
                </b-row>
            </b-col>
            <b-col sm="5">
                <b-row>
                    <b-col sm="3"><label>Categorias</label></b-col>
                    <b-col sm="9">
                        <b-form-select :state="stateCategoria" v-model="selectCategoria" :options="categorias" @change="porCategoriaFecha()"></b-form-select>
                    </b-col>
                </b-row><br>
                <b-row>
                    <b-col sm="1">
                        <label for="input-inicio">De:</label>
                    </b-col>
                    <b-col sm="11">
                        <input 
                            class="form-control" 
                            type="date" 
                            :state="stateDate"
                            v-model="inicio"
                            @change="porCategoriaFecha()">
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="1">
                        <label for="input-final">A: </label>
                    </b-col>
                    <b-col sm="11">
                        <input 
                            class="form-control" 
                            type="date" 
                            v-model="final"
                            @change="porCategoriaFecha()">
                    </b-col>
                </b-row>
            </b-col>
            <b-col sm="2" class="text-right">
                <b-button variant="dark" v-if="(tablaUnidades || tablaMonto) && movimientos.length > 0" :href="`/download_movimientos/${queryEMov}/${selected}`">
                    <i class="fa fa-download"></i> Descargar
                </b-button>
                <b-button variant="dark" v-if="tablaCategoria && movimientos.length > 0" :href="`/down_fechaCategoria/${inicio}/${final}/${selectCategoria}`">
                    <i class="fa fa-download"></i> Descargar
                </b-button>
            </b-col>
        </b-row><br>
        <b-table
            id="table-mov"
            responsive
            :per-page="perPage"
            v-if="tablaUnidades && movimientos.length > 0"
            :current-page="currentPage"
            :items="movimientos"
            :fields="fieldsU"
            :tbody-tr-class="rowClass">
            <template slot="entradas" slot-scope="row">
                {{ row.item.entradas | formatNumber }}
            </template>
            <template slot="devoluciones" slot-scope="row">
                {{ row.item.devoluciones | formatNumber }}
            </template>
            <template slot="notas_entrada" slot-scope="row">
                {{ row.item.notas_entrada | formatNumber }}
            </template>
            <template slot="remisiones" slot-scope="row">
                {{ row.item.remisiones | formatNumber }}
            </template>
            <template slot="notas_salida" slot-scope="row">
                {{ row.item.notas_salida | formatNumber }}
            </template>
            <template slot="pedidos" slot-scope="row">
                {{ row.item.pedidos | formatNumber }}
            </template>
            <template slot="promociones" slot-scope="row">
                {{ row.item.promociones | formatNumber }}
            </template>
            <template slot="donaciones" slot-scope="row">
                {{ row.item.donaciones | formatNumber }}
            </template>
            <template slot="existencia" slot-scope="row">
                {{ row.item.existencia | formatNumber }}
            </template>
        </b-table>
        <b-table
            id="table-mov"
            responsive
            :per-page="perPage"
            v-if="tablaMonto && movimientos.length > 0"
            :current-page="currentPage"
            :items="movimientos"
            :fields="fieldsM"
            :tbody-tr-class="rowClass">
            <template slot="entradas" slot-scope="row">
                ${{ row.item.entradas | formatNumber }}
            </template>
            <template slot="devoluciones" slot-scope="row">
                ${{ row.item.devoluciones | formatNumber }}
            </template>
            <template slot="notas_entrada" slot-scope="row">
                ${{ row.item.notas_entrada | formatNumber }}
            </template>
            <template slot="remisiones" slot-scope="row">
                ${{ row.item.remisiones | formatNumber }}
            </template>
            <template slot="notas_salida" slot-scope="row">
                ${{ row.item.notas_salida | formatNumber }}
            </template>
            <template slot="pedidos" slot-scope="row">
                ${{ row.item.pedidos | formatNumber }}
            </template>
        </b-table>
        <b-table
            id="table-mov"
            responsive
            v-if="tablaCategoria && movimientos.length > 0"
            :per-page="perPage"
            :current-page="currentPage"
            :items="movimientos">
            <template slot="unidades" slot-scope="row">
                {{ row.item.unidades | formatNumber }}
            </template>
            <template slot="total" slot-scope="row">
                ${{ row.item.total | formatNumber }}
            </template>
        </b-table>
        <!-- PAGINACIÓN -->
        <b-pagination
            v-model="currentPage"
            :total-rows="movimientos.length"
            :per-page="perPage"
            v-if="movimientos.length > 0"
            aria-controls="table-mov">
        </b-pagination>
        <b-alert v-if="movimientos.length === 0" show variant="secondary">
            <i class="fa fa-warning"></i> No se encontraron registros.
        </b-alert>
        <!-- MODAL DE AYUDA MOVIMIENTOS-->
        <b-modal id="modal-ayudaMov" hide-backdrop hide-footer title="Ayuda">
            <p>
                En este apartado apareceran solo los libros que hayan tenido entradas y/o salidas.<br>
                Las columnas de Entradas y Devoluciones hacen referencia a las entradas al inventario y
                las columnas de Remisiones, Notas y Promociones hacen referencia las salidas del inventario.<br>
            </p>
            <h5 id="titleA"><b>Búsqueda por editorial</b></h5>
            <p>Elegir la editorial que desee y aparecerán todos los libros relacionados a esta.</p>
            <h5 id="titleA"><b>Descargar lista completa</b></h5>
            <p>
                Si la opción de TODOS LOS LIBROS esta activa en la búsqueda por editorial, se descargará la lista completa de libros en formato EXCEL.
            </p>
            <h5 id="titleA"><b>Descargar lista por editorial</b></h5>
            <p>
                Si alguna editorial esta activa en la búsqueda por editorial se descargará la lista de libros relacionados a esta en formato EXCEL.
            </p>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['editoriales'],
        data() {
            return {
                options: [],
                tipos: [
                    { value: 'unidades', text: 'UNIDADES' },
                    { value: 'monto', text: 'MONTO' }
                ],
                categorias: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'ENTRADAS', text: 'ENTRADAS' },
                    { value: 'REMISIONES', text: 'REMISIONES'},
                    { value: 'DEVOLUCIONES', text: 'DEVOLUCIONES' },
                    { value: 'NOTAS', text: 'NOTAS (SALIDA)'},
                    { value: 'NOTASDEV', text: 'NOTAS (DEVOLUCIÓN)'},
                    { value: 'PEDIDOS', text: 'PEDIDOS'},
                    { value: 'PROMOCIONES', text: 'PROMOCIONES'},
                    { value: 'DONACIONES', text: 'DONACIONES'}
                ],
                fieldsU: [
                    'libro', 'entradas', 'devoluciones',
                    {key: 'notas_entrada', label: 'Notas (Devolución)'},
                    'remisiones',
                    {key: 'notas_salida', label: 'Notas (Salida)'},
                    'pedidos', 'promociones', 'donaciones', 'existencia'
                ],
                fieldsM: [
                    'libro', 'entradas', 'remisiones', 'devoluciones',
                    {key: 'notas_entrada', label: 'Notas (Devolución)'},
                    {key: 'notas_salida', label: 'Notas (Salida)'},
                    'pedidos'
                ],
                queryEMov: 'TODO',
                posicion: 0,
                perPage: 10,
                currentPage: 1,
                movimientos: [],
                selected: 'unidades',
                stateDate: null,
                inicio: '0000-00-00',
                final: '0000-00-00',
                selectCategoria: null,
                stateCategoria: null,
                tablaUnidades: true,
                tablaMonto: false,
                tablaCategoria: false
            }
        },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        mounted: function(){
            this.movimientosLibros();
            this.assign_editorial();
        },
        methods: {
            assign_editorial(){
                this.options.push({
                    value: 'TODO',
                    text: 'MOSTRAR TODO'
                });
                this.editoriales.forEach(editorial => {
                    this.options.push({
                        value: editorial.editorial,
                        text: editorial.editorial
                    });
                });
            },
            porCategoriaFecha(){
                if(this.selectCategoria !== null){
                    this.stateCategoria = null;
                    if(this.final != '0000-00-00'){
                        if(this.inicio != '0000-00-00'){
                            axios.get('/movimientos_por_fecha', {
                                params: {inicio: this.inicio, final: this.final, categoria: this.selectCategoria}}).then(response => {
                                this.movimientos = [];
                                this.movimientos = response.data;
                                this.queryEMov = 'TODO';
                                this.selected = 'unidades';

                                this.show_tables(false, false, true);
                            });
                        } else {
                            this.stateDate = false;
                            this.makeToast('warning', 'Es necesario seleccionar la fecha de inicio');
                        }
                    }
                } else{
                    this.stateCategoria = false;
                    this.makeToast('warning', 'Es necesario seleccionar primero la categoria.');
                }
            },
            // MOSTRAR MOVIMIENTOS POR EDITORIAL
            editorialMov(){
                axios.get('/movimientos_por_edit', {params: {queryEMov: this.queryEMov, tipo: this.selected}}).then(response => {
                    this.movimientos = [];
                    this.movimientos = response.data;
                    this.inicio = '0000-00-00';
                    this.final = '0000-00-00';
                    this.selectCategoria = null;
                    if(this.selected === 'unidades')
                        this.show_tables(true, false, false);
                    if(this.selected === 'monto')
                        this.show_tables(false, true, false);
                        
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            show_tables(unidades, monto, categoria){
                this.tablaUnidades = unidades;
                this.tablaMonto = monto;
                this.tablaCategoria = categoria;
            },
            rowClass(item, type) {
                if(this.selected === 'unidades'){
                    var entradas = parseInt(item.entradas) + parseInt(item.devoluciones) + parseInt(item.notas_entrada);
                    var salidas = parseInt(item.remisiones) + parseInt(item.notas_salida) + parseInt(item.pedidos) + parseInt(item.promociones) + parseInt(item.donaciones);
                    
                    var resultado = parseInt(entradas) - parseInt(salidas);
                    var piezas = parseInt(item.existencia);
                    
                    var diferencia = parseInt(piezas) - parseInt(resultado);
                    if(!item) return
                    if(diferencia !== 0) return 'table-danger';
                }
                if(this.selected === 'monto'){
                    if(!item) return
                    if(item.entradas === 0) return 'table-warning';
                }
            },
            movimientosLibros(){
                axios.get('/movimientos_todos').then(response => {
                    this.movimientos = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
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