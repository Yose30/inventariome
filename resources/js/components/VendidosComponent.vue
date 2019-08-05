<template>
    <div>
       <div v-if="listadoLibros"> 
            <b-row class="col-md-6">
                <b-col sm="4">
                    <b>Seleccionar fecha</b>
                </b-col>
                <b-col sm="8">
                    <b-input type="date" v-model="fecha" @change="porFecha"/>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="vendidos" :fields="fields">
                <template slot="total_vendido" slot-scope="row">
                    ${{ row.item.total_vendido }}
                </template>
                <template slot="total_pendiente" slot-scope="row">
                    ${{ row.item.total_pendiente }}
                </template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="func_detalles(row.item)">Detalles</b-button>
                </template>
            </b-table>
        </div>
        <div v-if="mostrarDetalles">
            <div class="text-right">
                <b-button variant="outline-secondary" @click="mostrarDetalles = false; listadoLibros = true;">
                    <i class="fa fa-mail-reply"></i> Regresar
                </b-button>
                <hr>
                <b-table :items="registros"></b-table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                fecha: null,
                vendidos: [],
                fields: [
                    'libro', 
                    {key: 'unidades_vendido', label: 'Unidades vendidas'},
                    {key: 'total_vendido', label: 'Subtotal'},
                    {key: 'unidades_pendiente', label: 'Unidades pendientes'},
                    {key: 'total_pendiente', label: 'Subtotal'},
                    {key: 'detalles', label: ''}
                ],
                listadoLibros: true,
                mostrarDetalles: false,
                registros: []
            }
        },
        created: function(){
			this.getTodo();
        },
        methods: {
            getTodo(){
                axios.get('/obtener_vendidos').then(response => {
                    this.vendidos = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            porFecha(){
                axios.get('/obtener_por_fecha', {params: {fecha: this.fecha}}).then(response => {
                    this.vendidos = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            func_detalles(vendido){
                console.log(vendido);
                this.listadoLibros = false;
                this.mostrarDetalles = true;
                axios.get('/detalles_vendidos', {params: {fecha: this.fecha, libro_id: vendido.libro_id}}).then(response => {
                    this.registros = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            makeToast(variante = null, descripcion){
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variante,
                    solid: true
                });
            }
        }
    }
</script>
