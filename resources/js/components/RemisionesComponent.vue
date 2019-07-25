<template>
    <div>
        <b-alert v-if="remisiones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="remisiones.length > 0" :items="remisiones" :fields="fields">
            <template slot="cliente" slot-scope="row">
                {{ row.item.cliente.name }}
            </template>
            <template slot="total" slot-scope="row">
                ${{ row.item.total }}
            </template>
            <template slot="estado" slot-scope="row">
                <b-badge variant="secondary" v-if="row.item.estado == 'Iniciado'">{{ row.item.estado }}</b-badge>
                <b-badge variant="primary" v-if="row.item.estado == 'Proceso'">Entregado</b-badge>
                <b-badge variant="success" v-if="row.item.estado == 'Terminado'">{{ row.item.estado }}</b-badge>
            </template>
            <template slot="registrar_entrega" slot-scope="row">
                <b-button 
                    variant="success" 
                    v-if="row.item.estado == 'Iniciado'"
                    v-on:click="entregaLibros(row.item, row.index)">
                    <i class="fa fa-check"></i>
                </b-button>
            </template>
        </b-table>
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
                    'estado',
                    {key: 'fecha_entrega', label: 'Fecha de entrega'},
                    {key: 'registrar_entrega', label: 'Marcar entrega'},
                ]
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
                axios.get('/devoluciones_remision', {params: {remision_id: remision.id}}).then(response => {
                    this.remisiones[i].estado = response.data.remision.estado;
                }).catch(error => {
                    this.$bvToast.toast('Ocurrio un error, vuelve a intentar', {
                        title: 'Error',
                        variant: 'danger',
                        solid: true
                    });
                });
            }
        }
    }
</script>