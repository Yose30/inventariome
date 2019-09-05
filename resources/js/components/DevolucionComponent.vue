<template>
    <div>
        <div v-if="!mostrarDevolucion && !mostrarDetalles">
            <b-row>
                <b-col sm="3">
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remision</label>
                        </b-col>
                        <b-col sm="8">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="num_remision" 
                                @keyup.enter="porNumero">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="6">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes"
                            ></b-input>
                            <div class="list-group" v-if="resultsClientes.length">
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
                <b-col sm="3" align="right">
                    <b-button variant="info" @click="getTodo">Mostrar todo</b-button>
                </b-col>  
            </b-row> 
            <hr>
        
            <b-table 
                v-if="remisiones.length > 0 && !mostrarDevolucion && !mostrarDetalles" 
                :items="remisiones" 
                :fields="fields"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
                <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion | formatNumber }}</template>
                <template slot="pagos" slot-scope="row">${{ row.item.pagos | formatNumber }}</template>
                <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar | formatNumber }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button v-if="row.item.total_devolucion > 0" variant="info" @click="func_detalles(row.item)">Detalles</b-button>
                </template>
                <template slot="registrar_devolucion" slot-scope="row">
                    <b-button 
                        v-if="row.item.estado != 'Terminado' && row.item.total_pagar != 0" 
                        variant="primary" 
                        @click="registrarDevolucion(row.item, row.index)">Registrar devolución
                    </b-button>
                </template>
            </b-table>
            <b-pagination
                v-model="currentPage"
                :total-rows="remisiones.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="remisiones.length > 0"
            ></b-pagination>
        </div>
        <div v-if="mostrarDevolucion">
            <div class="row">
                <div class="col-md-6">
                    <h4>Remisión n. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </div>
                <div class="col-md-2">
                    <b-button 
                        variant="success" 
                        @click="guardar" 
                        v-if="btnGuardar">
                        <i class="fa fa-check"></i> Concluir
                    </b-button>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-3 text-right">
                    <b-button variant="outline-secondary" @click="mostrarDevolucion = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </div>
            </div>
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades pendientes</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.libro.ISBN }}</td>
                        <td>{{ devolucion.libro.titulo }}</td>
                        <td>$ {{ devolucion.dato.costo_unitario | formatNumber }}</td>
                        <td>{{ devolucion.unidades_resta | formatNumber }}</td>
                        <td>
                            <input 
                            type="number" 
                            v-model="devolucion.unidades"
                            min="1"
                            max="9999"
                            :disabled="inputUnidades"
                            @keyup.enter="guardarUnidades(devolucion, i)"/>
                        </td>
                        <td>$ {{ devolucion.total | formatNumber }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td><td></td>
                        <td><h5>$ {{ total_devolucion | formatNumber }}</h5></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="mostrarDetalles">
            <div class="row">
                <div class="col-md-9">
                    <h4>Remisión n. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </div>
                <div class="col-md-3 text-right">
                    <b-button variant="outline-secondary" @click="mostrarDetalles = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </div>
            </div>
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades devueltas</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.libro.ISBN }}</td>
                        <td>{{ devolucion.libro.titulo }}</td>
                        <td>$ {{ devolucion.dato.costo_unitario | formatNumber }}</td>
                        <td>{{ devolucion.unidades | formatNumber }}</td>
                        <td>$ {{ devolucion.total | formatNumber }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td>
                        <td><h5>$ {{ total_devolucion | formatNumber }}</h5></td>
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
                fields: [
                    {key: 'id', label: 'Remisión No.'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'pagos', label: 'Pagado'},
                    {key: 'total_pagar', label: 'Pagar'},
                    {key: 'detalles', label: ''},
                    {key: 'registrar_devolucion', label: ''},
                ],
                mostrarDetalles: false,
                btnGuardar: false, //Indica si se muestra el boton de guardar
                mostrarDevolucion: false,
                remision: {}, //Datos de la remision
                devoluciones: [], //Array de las devoluciones
                inputUnidades: false, //Indica si esta habilitado o no el de unidades
                disabled: false, //Indica si esta hablitidado o no el boton de registrar
                total_devolucion: 0,
                remisiones: [],
                posicion: 0,
                num_remision: null,
                queryCliente: '',
                resultsClientes: [],
                perPage: 15,
                currentPage: 1,
            }
        },
        // created: function(){
		// 	this.getTodo();
        // },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            porNumero(){
                axios.get('/num_pagos', {params: {remision_id: this.num_remision}}).then(response => {
                    if(response.data.id != undefined){
                        this.remisiones = [];
                        this.remisiones.push(response.data);
                    }
                    else{
                        this.makeToast('warning', 'No se puede consultar el numero de remisión ingresado');
                    }
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    this.getTodo();
                }
            },
            porCliente(cliente){
                axios.get('/all_pagos', {params: {cliente_id: cliente.id}}).then(response => {
                    this.remisiones = [];
                    this.remisiones = response.data;
                    this.resultsClientes = [];
                    this.queryCliente = cliente.name;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            getTodo(){
                axios.get('/all_devoluciones').then(response => {
                    this.remisiones = response.data;
                });
            },
            registrarDevolucion(remision, i){
                this.devoluciones = [];
                this.posicion = i;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.devoluciones = response.data.devoluciones;
                    this.remision = remision;
                    this.acumularFinal();
                    this.mostrarDevolucion = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            guardarUnidades(devolucion, i){
                if(devolucion.unidades > 0){
                    if(devolucion.unidades <= devolucion.unidades_resta){
                        this.devoluciones[i].total = devolucion.dato.costo_unitario * devolucion.unidades;
                        this.acumularFinal();
                        this.btnGuardar = true;
                    }
                    else{
                        this.item = devolucion.id;
                        this.makeToast('warning', 'Unidades mayor a unidades pendientes');
                    }
                }
                else{
                    this.makeToast('warning', 'Las unidades no pueden ser cero');
                }
            },
            guardar(){
                this.remision.devoluciones = this.devoluciones;
                axios.put('/concluir_remision', this.remision).then(response => {
                    this.remisiones[this.posicion].estado = response.data.estado;
                    this.remisiones[this.posicion].total_devolucion = response.data.total_devolucion;
                    this.remisiones[this.posicion].total_pagar = response.data.total_pagar;
                    this.mostrarDevolucion = false;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            acumularFinal(){
                this.total_devolucion = 0;
                this.total_pagar = 0;
                this.devoluciones.forEach(devolucion => {
                    this.total_devolucion += devolucion.total;
                    this.total_pagar += devolucion.total_resta;
                });
            },
            func_detalles(remision){
                this.devoluciones = [];
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.devoluciones = response.data.devoluciones;
                    this.mostrarDetalles = true;
                    this.remision = remision;
                    this.acumularFinal();
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar');
                });
            },
            makeToast(variant = null, descripcion) {
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variant,
                    solid: true
                })
            }
        },
    }
</script>