<template>
    <div>
        <b-alert v-if="remisiones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table 
            v-if="!mostrarDetalles && !mostrarPagos && remisiones.length > 0" 
            :items="remisiones" :fields="fields">
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar }}</template>
            <template slot="pagos" slot-scope="row">${{ row.item.pagos }}</template>
            <template slot="pagar" slot-scope="row">
                <b-button v-if="row.item.total_pagar > 0" variant="primary" @click="registrarPago(row.item, row.index)">Registrar pago</b-button>
            </template>
            <template slot="ver_pagos" slot-scope="row">
                <b-button v-if="row.item.pagos != 0" variant="info" @click="verPagos(row.item)">Ver pagos</b-button>
            </template>
        </b-table>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h4>Remisión No. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button v-if="btnGuardar" variant="success" @click="guardarUnidades">
                            <i class="fa fa-check"></i> Guardar
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
                    <label><b>Unidades vendidas</b>: {{ remision.unidades }}</label>
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
            <b-table :items="remision.vendidos" :fields="fieldsP">
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
                    unidades: 0,
                    datos: [],
                    vendidos: []
                },
                btnGuardar: false,
                total_vendido: 0,
                pos_remision: 0,
                mostrarPagos: false,
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
            registrarPago(remision, index){
                this.pos_remision = index;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.id = remision.id;
                    this.remision.cliente = remision.cliente;
                    this.remision.vendidos = response.data;
                    this.mostrarDetalles = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            guardarUnidades(){
                axios.post('/registrar_pago', this.remision).then(response => {
                    this.remisiones[this.pos_remision].pagos = response.data.pagos;
                    this.remisiones[this.pos_remision].total_pagar = response.data.total_pagar;
                    this.makeToast('success', 'El pago se guardo correctamente');
                     this.mostrarDetalles = false;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            verificarUnidades(base, resta, costo, i){
                if(base > resta){
                    this.makeToast('warning', 'Las unidades son mayor a las unidades pendientes');
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
                    this.remision.vendidos = response.data;
                    this.remision.cliente = remision.cliente;
                    this.remision.vendidos.forEach(vendido => {
                        this.remision.unidades += vendido.unidades;
                    });
                    this.mostrarPagos = true;
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