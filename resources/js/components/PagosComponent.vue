<template>
    <div>
        <b-row>
            <b-col sm="4">
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-numero">Remision</label>
                    </b-col>
                    <b-col sm="9">
                        <b-form-input 
                            id="input-numero" 
                            type="number" 
                            v-model="remision_id" 
                            @keyup.enter="porNumero">
                        </b-form-input>
                    </b-col>
                </b-row>
            </b-col>
            <b-col sm="8">
                <b-row class="my-1">
                    <b-col sm="2">
                        <label for="input-cliente">Cliente</label>
                    </b-col>
                    <b-col sm="10">
                        <b-input
                        v-model="queryCliente"
                        @keyup="mostrarClientes">
                        </b-input>
                        <div class="list-group" v-if="resultsClientes.length">
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
        </b-row>
        <hr>
        <b-table 
            v-if="!mostrarDetalles && !mostrarPagos && remisiones.length > 0" 
            :items="remisiones" :fields="fields">
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar }}</template>
            <template slot="pagos" slot-scope="row">${{ row.item.pagos }}</template>
            <template slot="pagar" slot-scope="row">
                <b-button 
                    v-if="row.item.total_pagar > 0 && role_id == 2"
                    v-b-modal.modal-registrar-deposito
                    variant="primary" 
                    @click="registrarDeposito(row.item, row.index)">Registrar pago
                </b-button>
                <b-button 
                    v-if="row.item.total_pagar > 0 && role_id == 3"
                    variant="primary" 
                    @click="registrarPago(row.item, row.index)">Registrar pago
                </b-button>
            </template>
            <template slot="ver_pagos" slot-scope="row">
                <b-button v-if="row.item.pagos != 0" variant="info" @click="verPagos(row.item)">Ver pagos</b-button>
            </template>
        </b-table>

        <b-modal id="modal-registrar-deposito" title="Registrar pago">
            <b-form @submit.prevent="guardarDeposito">
                <b-row>
                    <b-col sm="2">
                        <label>Pago</label>
                    </b-col>
                    <b-col sm="5">
                        <b-form-input v-model="deposito.pago" :state="state" :disabled="load" type="number" required></b-form-input>
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

        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h4>Remisión No. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button 
                            v-if="btnGuardar" 
                            :disabled="load" 
                            variant="success" 
                            @click="guardarUnidades">
                            <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                        </b-button>
                    </div>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarDetalles = false">
                            <i class="fa fa-mail-reply"></i> Regresar
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="remision.vendidos" :fields="fieldsRP">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                <template slot="unidades_base" slot-scope="row">
                    <b-input 
                        type="number" 
                        :disabled="load"
                        @change="verificarUnidades(row.item.unidades_base, row.item.unidades_resta, row.item.dato.costo_unitario, row.index)" 
                        v-model="row.item.unidades_base">
                    </b-input>
                </template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total_base }}</template>
            </b-table>
            <h5 class="text-right">${{ total_vendido }}</h5>
        </div>
        <div v-if="mostrarPagos">
            <b-row>
                <b-col>
                    <h4>Remisión No. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label v-if="remision.total_pagar == 0"><b>Unidades vendidas</b>: {{ remision.unidades }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Total</b>: ${{ remision.pagos }}</label><br>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarPagos = false">
                            <i class="fa fa-mail-reply"></i> Regresar
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <hr>
            <b-table v-if="remision.depositos.length > 0" :items="remision.depositos" :fields="fieldsDep">
                <template slot="index" slot-scope="row">
                    {{ row.index + 1 }}
                </template>
                <template slot="pago" slot-scope="row">
                    ${{ row.item.pago }}
                </template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
                <template slot="user" slot-scope="row">Teresa Pérez</template>
            </b-table>
            <b-table v-if="remision.depositos.length == 0" :items="remision.vendidos" :fields="fieldsP">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button v-if="row.item.pagos.length > 0" variant="outline-info" @click="row.toggleDetails">
                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}}
                    </b-button>
                </template>
                <template slot="row-details" slot-scope="row">
                    <b-card>
                        <b-table :items="row.item.pagos" :fields="fieldsD">
                            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                            <template slot="user_id" slot-scope="row">
                                <label v-if="row.item.user_id == 2">Teresa Pérez</label>
                                <label v-if="row.item.user_id == 3">Almacén</label>
                            </template>
                            <template slot="unidades" slot-scope="row">{{ row.item.unidades }}</template>
                            <template slot="pago" slot-scope="row">$ {{ row.item.pago }}</template>
                            <template slot="created_at" slot-scope="row">{{ row.created_at | moment }}</template>
                        </b-table>
                    </b-card>
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                remisiones: [],
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
                    pago: 0
                },
                queryCliente: '',
                resultsClientes: [],
                remision_id: null,
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
                axios.get('/all_devoluciones').then(response => {
                    this.remisiones = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            registrarDeposito(remision, index){
                this.pos_remision = index;
                this.deposito.remision_id = remision.id;
                this.remision.total_pagar = remision.total_pagar;
                this.deposito.pago = 0;
                // axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                //     if(response.data.depositos.length > 0){
                //         this.$bvModal.hide('modal-registrar-deposito');
                //         this.makeToast('warning', 'Los registros de pago de la remisión los esta realizando otro usuario');
                //     }
                // }).catch(error => {
                //     this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                // });
            },
            registrarPago(remision, index){
                this.pos_remision = index;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.id = remision.id;
                    this.remision.descuento = remision.descuento;
                    this.remision.cliente = remision.cliente;
                    this.remision.vendidos = response.data.vendidos;
                    if(response.data.depositos.length == 0){
                        this.mostrarDetalles = true;
                    }
                    else{
                        this.makeToast('warning', 'Los registros de pago de la remisión los esta realizando otro usuario');
                    }
                    
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
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
            guardarUnidades(){
                this.load = true;
                axios.post('/registrar_pago', this.remision).then(response => {
                    this.remisiones[this.pos_remision].pagos = response.data.pagos;
                    this.remisiones[this.pos_remision].total_pagar = response.data.total_pagar;
                    this.makeToast('success', 'El pago se guardo correctamente');
                    this.mostrarDetalles = false;
                    this.load = false;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    this.load = false;
                });
            },
            verificarUnidades(base, resta, costo, i){
                if(base > resta){
                    this.makeToast('warning', 'Las unidades son mayor a las unidades pendientes');
                    this.remision.vendidos[i].unidades_base = 0;
                    this.remision.vendidos[i].total_base = 0;
                }
                if(base <= resta){
                    this.total_vendido = 0;
                    this.remision.vendidos[i].total_base = base * costo;
                    this.btnGuardar = true;
                    this.remision.vendidos.forEach(vendido => {
                        this.total_vendido += vendido.total_base;
                    });
                }
            },
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
            pagosCliente(cliente){
                axios.get('/all_pagos', {params: {cliente_id: cliente.id}}).then(response => {
                    this.remisiones = response.data;
                    this.resultsClientes = [];
                    this.queryCliente = '';
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            porNumero(){
                axios.get('/num_pagos', {params: {remision_id: this.remision_id}}).then(response => {
                    if(response.data.id != undefined){
                        this.remisiones = [];
                        this.remisiones.push(response.data);
                    }
                    else{
                        this.makeToast('warning', 'Numero de remisión incorrecto');
                    }
                }).catch(error => {
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