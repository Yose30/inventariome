<template>
    <div>
        <div v-if="!mostrarDetalles && !mostrarPagos">
            <b-row>
                <b-col sm="3">
                    <!-- BUSCAR REMISIÓN POR NUMERO -->
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remision</label>
                        </b-col>
                        <b-col sm="8">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="remision_id" 
                                @keyup.enter="porNumero()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="6">
                    <!-- BUSCAR REMISIÓN POR CLIENTE -->
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()">
                            </b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listP">
                                <a 
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(result, i) in resultsClientes" 
                                    @click="pagosCliente(result)">
                                    {{ result.name }}
                                </a>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <!-- <b-col sm="3" align="right">
                    <b-button variant="info" :disabled="loadRegisters" @click="getTodo">
                        <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                    </b-button>
                </b-col> -->
            </b-row>
            <hr>
            <!-- LISTADO DE REMISIONES -->
            <b-table 
                responsive
                :items="remisiones" 
                :fields="fields" 
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage"
                v-if="remisiones.length > 0">
                <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion | formatNumber }}</template>
                <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar | formatNumber }}</template>
                <template slot="pagos" slot-scope="row">${{ row.item.pagos | formatNumber }}</template>
                <template slot="pagar" slot-scope="row">
                    <b-button 
                        v-if="row.item.total_pagar > 0 && role_id == 2"
                        v-b-modal.modal-registrar-deposito
                        variant="primary" 
                        @click="registrarDeposito(row.item, row.index)">Registrar pago
                    </b-button>
                </template>
                <template slot="ver_pagos" slot-scope="row">
                    <b-button v-if="row.item.pagos != 0" variant="info" @click="verPagos(row.item)">Ver pagos</b-button>
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
        <!-- MODADL PARA REGISTRAR DEPOSITO -->
        <b-modal id="modal-registrar-deposito" title="Registrar pago">
            <b-form @submit.prevent="guardarDeposito()">
                <b-row>
                    <b-col sm="2">
                        <label>Pago</label>
                    </b-col>
                    <b-col sm="5">
                        <b-form-input v-model="deposito.pago" autofocus :state="state" :disabled="load" required></b-form-input>
                    </b-col>
                    <b-col> 
                        <b-button type="submit" variant="success" :disabled="load">
                            <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                        </b-button>
                    </b-col>
                </b-row>
            </b-form>
            <div slot="modal-footer"></div>
        </b-modal>
        <div v-if="mostrarPagos">
            <b-row>
                <b-col>
                    <h5><b>Remisión No. {{ remision.id }}</b></h5>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarPagos = false">
                            <i class="fa fa-mail-reply"></i> Regresar
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <label><b>Cliente:</b> {{ remision.cliente.name }}</label>
            <hr>
            <b-table v-if="remision.depositos.length > 0" :items="remision.depositos" :fields="fieldsDep">
                <template slot="index" slot-scope="row">
                    {{ row.index + 1 }}
                </template>
                <template slot="pago" slot-scope="row">
                    ${{ row.item.pago | formatNumber }}
                </template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
                <template slot="user" slot-scope="row">Teresa Pérez</template>
            </b-table>
            <b-table v-if="remision.depositos.length == 0" :items="remision.vendidos" :fields="fieldsP">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario | formatNumber }}</template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button v-if="row.item.pagos.length > 0" variant="outline-info" @click="row.toggleDetails">
                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}}
                    </b-button>
                </template> 
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="4"></th>
                        <th>${{ remision.pagos | formatNumber }}</th>
                    </tr>
                </template>
                <template slot="row-details" slot-scope="row">
                    <b-card>
                        <b-table :items="row.item.pagos" :fields="fieldsD">
                            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                            <template slot="user_id" slot-scope="row">
                                <label v-if="row.item.user_id == 2">Teresa Pérez</label>
                                <label v-if="row.item.user_id == 3">Almacén</label>
                            </template>
                            <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                            <template slot="pago" slot-scope="row">$ {{ row.item.pago | formatNumber }}</template>
                            <template slot="created_at" slot-scope="row">{{ row.item.created_at | moment }}</template>
                        </b-table>
                    </b-card>
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
                remisiones: this.registersall,
                fields: [
                    {key: 'id', label: 'Remisión No.'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'total_devolucion', label: 'Devolución'}, 
                    {key: 'pagos', label: 'Pagado'},
                    {key: 'total_pagar', label: 'Pagar'}, 
                    {key: 'ver_pagos', label: ''},
                    {key: 'pagar', label: ''},
                ],
                fieldsRP: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades_resta', label: 'Unidades pendientes'},
                    {key: 'unidades_base', label: 'Unidades'}, 
                    'subtotal'
                ],
                fieldsP: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades', label: 'Unidades vendidas'}, 
                    'subtotal',
                    'detalles'
                ],
                fieldsDep: [
                    {key: 'index', label: 'No.'},
                    'pago',
                    {key: 'created_at', label: 'Fecha de pago'},
                    {key: 'user', label: 'Usuario'}
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    {key: 'user_id', label: 'Usuario'}, 
                    {key: 'unidades', label: 'Unidades vendidas'},
                    'pago', 
                    {key: 'created_at', label: 'Fecha'}, 
                ],
                mostrarDetalles: false,
                remision: {
                    id: 0,
                    cliente: {},
                    pagos: 0,
                    total_pagar: 0,
                    unidades: 0,
                    datos: [],
                    vendidos: [],
                    depositos: []
                },
                btnGuardar: false,
                total_vendido: 0,
                pos_remision: 0,
                mostrarPagos: false,
                state: null,
                load: false,
                deposito: {
                    remision_id: 0,
                    pago: null
                },
                queryCliente: '',
                resultsClientes: [],
                remision_id: null,
                perPage: 15,
                currentPage: 1,
                loadRegisters: false
            }
        },
        // created: function(){
		// 	this.getTodo();
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
            // BUSCAR REMISIÓN POR NUMERO
            porNumero(){
                if(this.remision_id > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.remision_id}}).then(response => {
                        if(response.data.remision.estado == 'Cancelado')
                            this.makeToast('warning', 'La remisión esta cancelada');
                        if(response.data.remision.total_pagar == 0 && (response.data.remision.estado == 'Proceso' || response.data.remision.estado == 'Terminado'))
                            this.makeToast('warning', 'La remisión ya se encuentra pagada. Consultar en el apartado de remisiones');
                        if(response.data.remision.total_pagar > 0){
                            this.remisiones = [];
                            this.remisiones.push(response.data.remision);
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Error al consultar el numero de remisión ingresado');
                    });
                }
            },
            // MOSTRAR CLIENTES POR COINCIDENCIA
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
            // MOSTRAR PAGOS DEL CLIENTE
            pagosCliente(cliente){
                axios.get('/all_pagos', {params: {cliente_id: cliente.id}}).then(response => {
                    this.remisiones = [];
                    this.remisiones = response.data;
                    this.resultsClientes = [];
                    this.queryCliente = cliente.name;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // INICIALIZAR PARA REGISTRAR PAGO
            registrarDeposito(remision, index){
                this.pos_remision = index;
                this.deposito.remision_id = remision.id;
                this.remision.total_pagar = remision.total_pagar;
                this.deposito.pago = null;
            },
            // LISTAR TODOS LOS PAGOS REALIZADOS
            verPagos(remision){
                this.remision.unidades = 0;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.id = remision.id;
                    this.remision.pagos = remision.pagos;
                    this.remision.vendidos = response.data.vendidos;
                    this.remision.cliente = remision.cliente;
                    this.remision.depositos = response.data.depositos;
                    this.remision.total_pagar = remision.total_pagar;
                    this.remision.vendidos.forEach(vendido => {
                        this.remision.unidades += vendido.unidades;
                    });
                    this.mostrarPagos = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            // GIUARDAR DEPOSITO
            guardarDeposito(){
                if(this.deposito.pago > 0){
                    if(this.deposito.pago <= this.remision.total_pagar){
                        this.state = null;
                        this.load = true; 
                        axios.post('/deposito_remision', this.deposito).then(response => {
                            this.load = false;
                            this.remisiones[this.pos_remision].pagos = response.data.pagos;
                            this.remisiones[this.pos_remision].total_pagar = response.data.total_pagar;
                            this.makeToast('success', 'El pago se guardo correctamente');
                            this.$bvModal.hide('modal-registrar-deposito');
                        })
                        .catch(error => {
                            this.load = false;
                            this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                        });
                    }
                    else{
                        this.state = false;
                        this.makeToast('warning', 'El pago es mayor al total a pagar');
                    }
                }
                else{
                    this.state = false;
                    this.makeToast('warning', 'El pago tiene que ser mayor a 0');
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

<style>
    #listP{
        position: absolute;
        z-index: 100
    }
</style>