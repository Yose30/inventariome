<template>
    <div>
        <b-alert v-if="devoluciones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="!mostrarDetalles && devoluciones.length > 0" :items="devoluciones" :fields="fields">
            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total }}</template>
            <template slot="pagos" slot-scope="row">${{ row.item.pagos }}</template>
            <template slot="estado" slot-scope="row">
                <b-badge variant="primary" v-if="row.item.estado == 'Proceso'">Entregado</b-badge>
                <b-badge variant="success" v-if="row.item.estado == 'Terminado'">{{ row.item.estado }}</b-badge>
            </template>
            <template slot="pagar" slot-scope="row">
                <b-button v-if="row.item.estado != 'Terminado' && row.item.pagos < row.item.total" variant="outline-primary" @click="registrarPago(row.item, row.index)">Registrar pago</b-button>
            </template>
            <template slot="ver_pagos" slot-scope="row">
                <b-button variant="outline-info">Ver pagos</b-button>
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
    </div>
</template>

<script>
    export default {
        data() {
            return {
                devoluciones: [],
                fields: [
                    {key: 'index', label: 'N.'}, 
                    'estado',
                    'cliente', 
                    {key: 'total_pagar', label: 'Total'}, 
                    {key: 'pagos', label: 'Pagado'},
                    {key: 'pagar', label: ''},
                    {key: 'ver_pagos', label: ''},
                ],
                fieldsSD: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades_resta', label: 'Unidades pendientes'},
                    {key: 'unidades_base', label: 'Unidades'}, 
                    'subtotal'],
                mostrarDetalles: false,
                remision: {
                    id: 0,
                    cliente: {},
                    datos: [],
                    vendidos: []
                },
                mostrarSalida: true,
                mostrarDevolucion: true,
                mostrarFinal: true,
                informacion: {},
                pago: 0,
                posicion: 0,
                btnGuardar: false,
                total_vendido: 0,
                pos_remision: 0,
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/all_devoluciones').then(response => {
                    this.devoluciones = response.data;
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
                    this.devoluciones[this.pos_remision].pagos = response.data.pagos;
                })
                .catch(error => {
                    this.makeToast('danger', 'Ocurrio un error, vuelve a intentarlo');
                });
            },
            verificarUnidades(base, resta, costo, i){
                if(base > resta){
                    this.makeToast('warning', 'Las unidades son mayor a lo pendiente');
                }
                if(base == 0){
                    this.makeToast('warning', 'Las unidades no pueden ser 0');
                }
                if(base <= resta && base != 0){
                    this.total_vendido = 0;
                    this.remision.vendidos[i].total_base = base * costo;
                    this.btnGuardar = true;
                    this.remision.vendidos.forEach(vendido => {
                        this.total_vendido += vendido.total_base;
                    });
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