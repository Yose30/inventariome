<template>
    <div>
        <b-alert v-if="remisiones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="!mostrarDetalles && !mostrarPagos && remisiones.length > 0" :items="remisiones" :fields="fields">
            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar }}</template>
            <template slot="pagos" slot-scope="row">${{ row.item.pagos }}</template>
            <template slot="pagar" slot-scope="row">
                <b-button v-if="row.item.pagos < row.item.total_pagar" variant="outline-primary" @click="registrarPago(row.item, row.index)">Registrar pago</b-button>
            </template>
            <template slot="ver_pagos" slot-scope="row">
                <b-button variant="outline-info" @click="verPagos(row.item)">Ver pagos</b-button>
            </template>
        </b-table>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h4>Remisión n. {{ remision.id }}</h4>
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
            <b-table :items="remision.vendidos" :fields="fieldsSD">
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
                    <h4>Remisión n. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
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
                    {key: 'index', label: 'N.'}, 
                    // 'estado',
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'total_devolucion', label: 'Devolución'}, 
                    {key: 'total_pagar', label: 'Pagar'}, 
                    {key: 'pagos', label: 'Pagado'},
                    {key: 'pagar', label: ''},
                    {key: 'ver_pagos', label: ''},
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    {key: 'user_id', label: 'Usuario'}, 
                    'pago', 
                    {key: 'created_at', label: 'Fecha'}, 
                ],
                fieldsSD: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades_resta', label: 'Unidades pendientes'},
                    {key: 'unidades_base', label: 'Unidades'}, 
                    'subtotal'],
                fieldsP: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    // {key: 'unidades_resta', label: 'Unidades pendientes'},
                    {key: 'unidades', label: 'Unidades'}, 
                    'subtotal',
                    'detalles'],
                mostrarDetalles: false,
                remision: {
                    id: 0,
                    cliente: {},
                    datos: [],
                    vendidos: []
                },
                informacion: {},
                pago: 0,
                posicion: 0,
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
                });
            },
            registrarPago(remision, index){
                this.remision.id = remision.id;
                this.pos_remision = index;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.vendidos = response.data;
                    this.remision.cliente = remision.cliente;
                    this.mostrarDetalles = true;
                });
            },
            guardarUnidades(){
                axios.post('/registrar_pago', this.remision).then(response => {
                    this.mostrarDetalles = false;
                    this.makeToast('success', 'El pago se guardo correctamente');
                    this.remisiones[this.pos_remision].pagos = response.data.pagos;
                })
                .catch(error => {
                    this.makeToast('danger', 'Ocurrio un error, vuelve a intentarlo');
                });
            },
            verificarUnidades(base, resta, costo, i){
                if(base > resta){
                    this.makeToast('warning', 'Las unidades son mayor a lo pendiente');
                }
                // if(base == 0){
                //     this.makeToast('warning', 'Las unidades no pueden ser 0');
                // }
                if(base <= resta && base != 0){
                    this.total_vendido = 0;
                    this.remision.vendidos[i].total_base = base * costo;
                    this.btnGuardar = true;
                    this.remision.vendidos.forEach(vendido => {
                        this.total_vendido += vendido.total_base;
                    });
                }
            },
            verPagos(remision){
                this.remision.id = remision.id;
                axios.get('/datos_vendidos', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.vendidos = response.data;
                    this.remision.cliente = remision.cliente;
                    this.mostrarPagos = true;
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