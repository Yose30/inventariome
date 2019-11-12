<template>
    <div>
        <check-connection-component></check-connection-component>
        <div v-if="!mostrarDevolucion && !mostrarPagos && !mostrarDonacion">
            <b-row>
                <!-- BUSCAR REMISION POR NUMERO -->
                <b-col sm="4"> 
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remisión</label>
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
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()"
                            ></b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listaD">
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
            </b-row> 
            <hr>
            <!-- TABLA DE REMISIONES -->
            <b-alert v-if="remisiones.length === 0" show variant="secondary">
                <i class="fa fa-warning"></i> No se encontraron registros.
            </b-alert>
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="remisiones.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="remisiones.length > 0"
            ></b-pagination>
            <b-table 
                responsive
                v-if="remisiones.length > 0" 
                :items="remisiones" 
                :fields="fields"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
                <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion | formatNumber }}</template>
                <template slot="total_donacion" slot-scope="row">${{ row.item.total_donacion | formatNumber }}</template>
                <template slot="pagos" slot-scope="row">${{ row.item.pagos | formatNumber }}</template>
                <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar | formatNumber }}</template>
                <template slot="registrar_pago" slot-scope="row">
                    <b-button 
                        v-if="row.item.total_pagar > 0"
                        variant="secondary" 
                        @click="registrarPago(row.item, row.index)">Pago
                    </b-button>
                </template>
                <template slot="registrar_devolucion" slot-scope="row">
                    <b-button 
                        v-if="row.item.total_pagar > 0" 
                        variant="primary" 
                        @click="registrarDevolucion(row.item, row.index)">Devolución
                    </b-button>
                </template>
                <template slot="registrar_donacion" slot-scope="row">
                    <b-button 
                        v-if="row.item.total_pagar > 0"
                        @click="registrarDonacion(row.item, row.index)"
                        variant="info">Donación
                    </b-button>
                </template>
            </b-table>
        </div>
        <!-- REGISTRAR PAGO -->
        <div v-if="mostrarPagos">
            <h4 style="color: #170057">Registro de pago</h4>
            <hr>
            <b-row>
                <b-col sm="6"><h5><b>Remisión No. {{ remision.id }}</b></h5></b-col>
                <b-col sm="3">
                    <div class="text-right">
                        <b-button 
                            :disabled="load" 
                            variant="success" 
                            @click="guardarPago()">
                            <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                        </b-button>
                    </div>
                </b-col>
                <b-col sm="3">
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarPagos = false">
                            <i class="fa fa-mail-reply"></i> Regresar
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <label><b>Cliente:</b> {{ remision.cliente.name }}</label>
            <hr>
            <b-table :items="remision.vendidos" :fields="fieldsRP">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario | formatNumber }}</template>
                <template slot="unidades_base" slot-scope="row">
                    <b-input 
                        :id="`inpVend-${row.index}`"
                        type="number" 
                        :disabled="load"
                        @change="verificarUnidades(row.item.unidades_base, row.item.unidades_resta, row.item.dato.costo_unitario, row.index)" 
                        v-model="row.item.unidades_base"> 
                    </b-input>
                </template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total_base | formatNumber }}</template>
            </b-table>
            <h5 class="text-right">${{ total_vendido | formatNumber }}</h5>
        </div>
        <!-- REGISTRAR DATOS DE DEVOLUCION -->
        <div v-if="mostrarDevolucion">
            <h4 style="color: #170057">Registro de devolución</h4>
            <hr>
            <div class="row">
                <div class="col-md-6"><h5><b>Remisión No. {{ remision.id }}</b></h5></div>
                <div class="col-md-3 text-right">
                    <b-button 
                        :disabled="load" 
                        variant="success" 
                        @click="guardar()">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </div>
                <div class="col-md-3 text-right">
                    <b-button variant="outline-secondary" @click="mostrarDevolucion = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </div>
            </div>
            <label><b>Cliente:</b> {{ remision.cliente.name }}</label>
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
                            <b-input 
                                :id="`inpDev-${i}`"
                                type="number" 
                                v-model="devolucion.unidades_base"
                                :disabled="load"
                                @change="guardarUnidades(devolucion, i)"/>
                        </td>
                        <td>$ {{ devolucion.total_base | formatNumber }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td><td></td>
                        <td><h5>$ {{ total_devolucion | formatNumber }}</h5></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- REGISTRAR DONACION -->
        <div v-if="mostrarDonacion">
            <h4 style="color: #170057">Registro de donación</h4>
            <hr>
            <div class="row">
                <div class="col-md-6"><h4>Remisión n. {{ remision.id }}</h4></div>
                <div class="col-md-3">
                    <b-button
                        :disabled="load" 
                        variant="success"
                        @click="guardarDonacion()">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </div>
                <div class="col-md-3 text-right">
                    <b-button variant="outline-secondary" @click="mostrarDonacion = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </div>
            </div>
            <label>Cliente: {{ remision.cliente.name }}</label>
            <hr>
            <b-table :items="remision.vendidos" :fields="fieldsRP">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario | formatNumber }}</template>
                <template slot="unidades_base" slot-scope="row">
                    <b-input 
                        :id="`inpVend-${row.index}`"
                        type="number" 
                        :disabled="load"
                        @change="verificarUnidades(row.item.unidades_base, row.item.unidades_resta, row.item.dato.costo_unitario, row.index)" 
                        v-model="row.item.unidades_base"> 
                    </b-input>
                </template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total_base | formatNumber }}</template>
            </b-table>
            <h5 class="text-right">${{ total_vendido | formatNumber }}</h5>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['registersall'],
        data() {
            return {
                fields: [
                    {key: 'id', label: 'Folio'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'pagos', label: 'Pagado'},
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'total_donacion', label: 'Donación'},
                    {key: 'total_pagar', label: 'Pagar'},
                    {key: 'registrar_pago', label: ''},
                    {key: 'registrar_devolucion', label: ''},
                    {key: 'registrar_donacion', label: ''}
                ], // Columnas de la tabla principal donde se muestran las remisiones
                fieldsRP: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades_resta', label: 'Unidades pendientes'},
                    {key: 'unidades_base', label: 'Unidades'}, 
                    'subtotal'
                ], // Columnas donde se muestran los datos de las remisiones
                mostrarDevolucion: false, // Indicar si se muestra el apartado para registrar devolución
                remision: {}, //Datos de la remision
                devoluciones: [], //Array de las devoluciones
                total_devolucion: 0,
                remisiones: this.registersall,
                num_remision: null,
                queryCliente: '',
                resultsClientes: [],
                perPage: 10,
                currentPage: 1,
                pos_remision: 0,
                mostrarPagos: false,
                load: false,
                total_vendido: 0,
                mostrarDonacion: false,
                pagoRemision: {},
                devolucionRemision: {}
            }
        },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            // BUSCAR REMISIÓN POR NUMERO
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        if(response.data.remision.estado == 'Iniciado')
                            this.makeToast('warning', 'La remisión aún no ha sido marcada como entregada.');
                        if(response.data.remision.estado == 'Cancelado')
                            this.makeToast('warning', 'La remisión esta cancelada');
                        if(response.data.remision.total_pagar == 0 && (response.data.remision.estado == 'Proceso' || response.data.remision.estado == 'Terminado'))
                            this.makeToast('warning', 'La remisión ya se encuentra pagada. Consultar en el apartado de remisiones.');
                        if(response.data.remision.total_pagar > 0){
                            this.remision = response.data.remision;
                            this.remisiones = [];
                            this.remisiones.push(this.remision);
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Error al consultar el numero de remisión ingresado');
                    });
                }
            },
            // MOSTRAR CLIENTES
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
            // MOSTRAR LOS PAGOS QUE HA REALIZADO EL CLIENTE
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
            // REGISTRAR PAGO DE LA REMISIÓN
            registrarPago(remision, index){
                this.pos_remision = index;
                this.total_vendido = 0;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.id = remision.id;
                    this.remision.cliente = remision.cliente;
                    this.remision.vendidos = response.data.vendidos;
                    if(response.data.depositos.length == 0){
                        this.mostrarPagos = true;
                    }
                    else{
                        this.makeToast('warning', 'Los registros de pago de la remisión los esta realizando otro usuario');
                    } 
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // GUARDAR PAGO
            guardarPago () {
                this.load = true;
                this.pagoRemision = {
                    id: this.remision.id,
                    vendidos: this.remision.vendidos
                }
                axios.post('/registrar_pago', this.pagoRemision).then(response => {
                    this.remisiones[this.pos_remision].estado = response.data.estado;
                    this.remisiones[this.pos_remision].pagos = response.data.pagos;
                    this.remisiones[this.pos_remision].total_pagar = response.data.total_pagar;
                    this.makeToast('success', 'El pago se guardo correctamente');
                    this.mostrarPagos = false;
                    this.load = false;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    this.load = false;
                });
            },
            // REGISTRAR DEVOLUCIÓN DE LA REMISIÓN
            registrarDevolucion(remision, i){
                this.devoluciones = [];
                this.pos_remision = i;
                this.total_devolucion = 0;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.devoluciones = response.data.remision.devoluciones;
                    this.remision = remision;
                    this.acumularFinal();
                    this.mostrarDevolucion = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // GUARDAR DEVOLUCIÓN
            guardar(){
                if(this.total_devolucion <= this.remisiones[this.pos_remision].total_pagar){
                    this.load = true;
                    this.devolucionRemision.id = this.remision.id;
                    this.devolucionRemision.devoluciones = this.devoluciones;
                    axios.put('/concluir_remision', this.devolucionRemision).then(response => {
                        this.remisiones[this.pos_remision].estado = response.data.estado;
                        this.remisiones[this.pos_remision].total_devolucion = response.data.total_devolucion;
                        this.remisiones[this.pos_remision].total_pagar = response.data.total_pagar;
                        this.mostrarDevolucion = false;
                        this.load = false;
                        this.makeToast('success', 'La devolución se guardo correctamente');
                    }).catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                } else {    
                    this.makeToast('warning', 'La devolución no puede ser guardada. El total de la devolución es mayor al total por pagar.');
                }
            },
            // REGISTRAR DONACIÓN
            registrarDonacion (remision, i) {
                this.pos_remision = i;
                this.total_donacion = 0;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.id = remision.id;
                    this.remision.cliente = remision.cliente;
                    this.remision.vendidos = response.data.vendidos;
                    this.mostrarDonacion = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // GUARDAR DONACIÓN
            guardarDonacion () {
                this.load = true;
                this.pagoRemision = {
                    id: this.remision.id,
                    vendidos: this.remision.vendidos
                }
                axios.post('/guardar_donacion', this.pagoRemision).then(response => {
                    this.remisiones[this.pos_remision].estado = response.data.estado;
                    this.remisiones[this.pos_remision].total_donacion = response.data.total_donacion;
                    this.remisiones[this.pos_remision].total_pagar = response.data.total_pagar;
                    this.makeToast('success', 'El pago se guardo correctamente');
                    this.mostrarDonacion = false;
                    this.load = false;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    this.load = false;
                });
            },
            // VERIFICAR LAS UNIDADES INGRESADAS PARA OBTENER EL SUBTOTAL
            verificarUnidades (base, resta, costo, i) {
                if(base > resta){
                    this.makeToast('warning', 'Las unidades son mayor a las unidades pendientes');
                    this.remision.vendidos[i].unidades_base = 0;
                    this.remision.vendidos[i].total_base = 0;
                }
                if(base <= resta){
                    this.total_vendido = 0;
                    this.remision.vendidos[i].total_base = base * costo;
                    this.remision.vendidos.forEach(vendido => {
                        this.total_vendido += vendido.total_base;
                    });
                    if(i + 1 < this.remision.vendidos.length){
                        document.getElementById('inpVend-'+(i+1)).focus();
                        document.getElementById('inpVend-'+(i+1)).select();
                    }
                }
            },
            // VERIFICAR LAS UNIDADES INGRESADAS PARA OBTENER EL SUBTOTAL
            guardarUnidades(devolucion, i){
                // if(devolucion.unidades_base > 0){
                    if(devolucion.unidades_base <= devolucion.unidades_resta){
                        this.devoluciones[i].total_base = devolucion.dato.costo_unitario * devolucion.unidades_base;
                        this.acumularFinal();
                        if(i + 1 < this.devoluciones.length){
                            document.getElementById('inpDev-'+(i+1)).focus();
                            document.getElementById('inpDev-'+(i+1)).select();
                        }
                    }
                    else{
                        this.item = devolucion.id;
                        this.makeToast('warning', 'Unidades mayor a unidades pendientes');
                        this.devoluciones[i].unidades_base = 0;
                        this.devoluciones[i].total_base = 0;
                    }
                // }
                // else{
                //     this.makeToast('warning', 'Las unidades no pueden ser cero');
                // }
            },
            acumularFinal(){
                this.total_devolucion = 0;
                this.total_pagar = 0;
                this.devoluciones.forEach(devolucion => {
                    this.total_devolucion += devolucion.total_base;
                    this.total_pagar += devolucion.total_resta;
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

<style>
    #listaD{
        position: absolute;
        z-index: 100
    }
    #listaD a {
        background-color: #f2f8ff;
    }
</style>