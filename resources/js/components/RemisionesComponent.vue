<template>
    <div>
        <div v-if="!mostrarDetalles">
            <b-row>
                <b-col sm="4">
                    <!-- BUSCAR REMISION POR NUMERO -->
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remision</label>
                        </b-col>
                        <b-col sm="8">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="num_remision" 
                                @keyup.enter="porNumero()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <!-- BUSCAR REMISION POR CLIENTE -->
                <b-col sm="8">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()"></b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listR">
                                <a 
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(result, i) in resultsClientes" 
                                    @click="porCliente(result)">
                                    {{ result.name }}
                                </a>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <!-- MOSTRAR TODAS LAS REMISIONES -->
                <!-- <b-col sm="3" align="right"> 
                    <b-button variant="info" :disabled="loadRegisters" @click="getTodo">
                        <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                    </b-button>
                </b-col>   -->
            </b-row> 
            <hr>
            <!-- LISTADO DE REMISIONES -->
            <b-table 
                responsive
                :items="remisiones" 
                :fields="fields"
                v-if="remisiones.length > 0"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
                <template slot="cliente" slot-scope="row">
                    {{ row.item.cliente.name }}
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button 
                        variant="outline-info"
                        @click="viewDetalles(row.item)">
                        Detalles
                    </b-button>
                </template>
                <template slot="registrar_entrega" slot-scope="row">
                    <b-button 
                        variant="success" 
                        v-if="row.item.estado == 'Iniciado'"
                        :disabled="load"
                        v-on:click="entregaLibros(row.item, row.index)">
                        <i class="fa fa-check"></i> Marcar entrega
                        <b-spinner v-if="load" small></b-spinner>
                    </b-button>
                </template>
            </b-table>
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="remisiones.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="remisiones.length > 0"
            ></b-pagination>
        </div>
        <!-- DETALLES DE LA REMISIÓN -->
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h5><b>Remisión No. {{ remision.id }}</b></h5>
                    <label><b>Fecha de creación:</b> {{ remision.created_at | moment }}</label>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarDetalles = false">
                            <i class="fa fa-mail-reply"></i> Regresar
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <label>Cliente: {{ remision.cliente.name }}</label>
            <hr>
            <b-table :items="remision.datos" :fields="fieldsD">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="3"></th>
                        <th>{{ total_unidades | formatNumber }}</th>
                        <th>${{ remision.total | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['registersall'],
        data() {
            return {
                num_remision: null,
                queryCliente: '',
                resultsClientes: [],
                remisiones: this.registersall,
                fields: [
                    {key: 'id', label: 'No.'}, 
                    {key: 'fecha_creacion', label: 'Fecha de creación'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'},
                    {key: 'detalles', label: ''},
                    {key: 'registrar_entrega', label: ''},
                ],
                fieldsD: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades', label: 'Unidades'}, 
                    'subtotal',],
                mostrarDetalles: false,
                remision: {},
                total_unidades: 0,
                load: false,
                perPage: 15,
                currentPage: 1,
                loadRegisters: false
            }
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            },
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            // BUSCAR REMISIÓN POR NUMERO
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        if(response.data.remision.estado == 'Cancelado')
                            this.makeToast('warning', 'La remisión esta cancelada');
                        if(response.data.remision.estado == 'Proceso' || response.data.remision.estado == 'Terminado')
                            this.makeToast('warning', 'La remisión ya fue marcada como entregada. Consultar en el apartado de remisiones');
                        else{
                            this.remision = response.data.remision;
                            this.remisiones = [];
                            this.remisiones.push(this.remision);
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Error al consultar el numero de remisión ingresado');
                    });
                }
            },
            // MOSTRAR LOS CLIENTES
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    this.resultsClientes = [];
                }
            },
            // MOSTRAR REMISIONES INICIADAS POR CLIENTE
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id, inicio: this.inicio, final: this.final}}).then(response => {
                    this.queryCliente = result.name;
                    this.resultsClientes = [];
                    this.remisiones = [];
                    response.data.forEach(data => {
                        if(data.estado == 'Iniciado'){
                            this.remisiones.push(data);
                        }
                    });
                });
            },
            // MARCAR ENTREGA DE REMISIÓN
            entregaLibros(remision, i){
                this.load = true;
                axios.put('/vendidos_remision', remision).then(response => {
                    this.load = false;
                    this.remisiones[i].estado = response.data.remision.estado;
                    // this.remisiones.splice(i,1);
                    this.makeToast('success', 'La remisión ha sido marcada como entregada');
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                }); 
            },
            // VER DETALLES DE LA REMISIÓN
            viewDetalles(remision){
                this.remision.id = remision.id;
                this.remision.cliente = remision.cliente;
                this.remision.total = remision.total;
                this.total_unidades = 0;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.mostrarDetalles = true;
                    this.remision.datos = response.data.remision.datos;
                    this.remision.datos.forEach(dato => {
                        this.total_unidades += dato.unidades;
                    });
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

<style>
    #listR{
        position: absolute;
        z-index: 100
    }
</style>