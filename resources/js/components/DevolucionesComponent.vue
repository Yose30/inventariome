<template>
    <div>
        <b-table v-if="!mostrarDetalles" :items="devoluciones" :fields="fields">
            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar }}</template>
            <template slot="estado" slot-scope="row">
                <b-badge variant="primary" v-if="row.item.estado == 'Proceso'">{{ row.item.estado }}</b-badge>
                <b-badge variant="success" v-if="row.item.estado == 'Terminado'">{{ row.item.estado }}</b-badge>
            </template>
            <template slot="detalles" slot-scope="row">
                <b-button variant="outline-info" @click="detallesRemision(row.item)"><i class="fa fa-eye"></i></b-button>
            </template>
        </b-table>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col><h4>Remisión n. {{ remision.id }}</h4></b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarDetalles = false"><i class="fa fa-mail-reply"></i> Devoluciones</b-button>
                    </div>
                </b-col>
            </b-row>
            <hr>
            <b-row>
                <b-col sm="10"><h4>Salida</h4></b-col>
                <b-button 
                    variant="link" 
                    :class="mostrarSalida ? 'collapsed' : null"
                    :aria-expanded="mostrarSalida ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarSalida = !mostrarSalida">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarSalida" class="mt-2">
                <b-table :items="remision.datos" :fields="fieldsSD">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
                </b-table>
                <h5 class="text-right">${{ remision.total }}</h5>
            </b-collapse>
            <hr>
            <b-row>
                <b-col sm="10"><h4>Devolución</h4></b-col>
                <b-button 
                    variant="link" 
                    :class="mostrarDevolucion ? 'collapsed' : null"
                    :aria-expanded="mostrarDevolucion ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarDevolucion = !mostrarDevolucion">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarDevolucion" class="mt-2">
                <b-table :items="remision.devoluciones" :fields="fieldsSD">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
                </b-table>
                <h5 class="text-right">${{ remision.total_devolucion }}</h5>
            </b-collapse>
            <hr>
            <b-row>
                <b-col sm="10"><h4>Remisión final</h4></b-col>
                <b-button 
                    variant="link" 
                    :class="mostrarFinal ? 'collapsed' : null"
                    :aria-expanded="mostrarFinal ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarFinal = !mostrarFinal">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarFinal" class="mt-2">
                <b-table :items="remision.devoluciones" :fields="fieldsSD">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                    <template slot="unidades" slot-scope="row">{{ row.item.unidades_resta }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total_resta }}</template>
                </b-table>
                <h5 class="text-right">${{ remision.total_pagar }}</h5>
            </b-collapse>
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
                    'fecha_devolucion', 
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'total_pagar', label: 'Final'},
                    'estado',
                    'detalles'
                ],
                fieldsSD: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    'unidades', 
                    'subtotal'],
                mostrarDetalles: false,
                remision: {
                    id: 0,
                    total: 0,
                    total_devolucion: 0,
                    total_pagar: 0,
                    datos: [],
                    devoluciones: []
                },
                mostrarSalida: true,
                mostrarDevolucion: true,
                mostrarFinal: true,
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
            detallesRemision(remision){
                this.remision = { id: 0, total: 0, total_devolucion: 0, total_pagar: 0, datos: [], devoluciones: [] };
                this.mostrarDetalles = true;
                this.remision.id = remision.id;
                this.remision.total = remision.total;
                this.remision.total_devolucion = remision.total_devolucion;
                this.remision.total_pagar = remision.total_pagar;
                axios.get('/datos_devolucion', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.datos = response.data.datos;
                    this.remision.devoluciones = response.data.devoluciones
                });
            }
        }
    }
</script>