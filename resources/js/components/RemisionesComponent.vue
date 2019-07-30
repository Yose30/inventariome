<template>
    <div>
        <b-alert v-if="remisiones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="!mostrarDetalles && remisiones.length > 0" :items="remisiones" :fields="fields">
            <template slot="cliente" slot-scope="row">
                {{ row.item.cliente.name }}
            </template>
            <template slot="total" slot-scope="row">
                ${{ row.item.total }}
            </template>
            <!-- <template slot="estado" slot-scope="row">
                <b-badge variant="secondary" v-if="row.item.estado == 'Iniciado'">{{ row.item.estado }}</b-badge>
                <b-badge variant="primary" v-if="row.item.estado == 'Proceso'">Entregado</b-badge>
                <b-badge variant="success" v-if="row.item.estado == 'Terminado'">{{ row.item.estado }}</b-badge>
            </template> -->
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
                    v-on:click="entregaLibros(row.item, row.index)">
                    <i class="fa fa-check"></i> Marcar entrega
                </b-button>
            </template>
        </b-table>

        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h4>Remisión n. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Unidades</b>: {{ total_unidades }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Total</b>: ${{ remision.total }}</label><br>
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
            <b-table :items="remision.datos" :fields="fieldsD">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
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
                    {key: 'id', label: 'Folio'}, 
                    {key: 'fecha_creacion', label: 'Fecha de creación'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'},
                    // 'estado',
                    // {key: 'fecha_entrega', label: 'Fecha de entrega'},
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
                total_unidades: 0

            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/todos_los_clientes').then(response => {
                    this.remisiones = response.data;
                });
            },
            entregaLibros(remision, i){
                axios.put('/vendidos_remision', remision).then(response => {
                    this.remisiones[i].estado = response.data.remision.estado;
                }).catch(error => {
                    this.$bvToast.toast('Ocurrio un problema, vuelve a intentar', {
                        title: 'Mensaje',
                        variant: 'danger',
                        solid: true
                    });
                });
            },
            viewDetalles(remision){
                this.remision.id = remision.id;
                this.remision.cliente = remision.cliente;
                this.remision.total = remision.total;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.mostrarDetalles = true;
                    this.remision.datos = response.data.datos;
                    this.remision.datos.forEach(dato => {
                        this.total_unidades += dato.unidades;
                    });
                });
                
            }
        }
    }
</script>