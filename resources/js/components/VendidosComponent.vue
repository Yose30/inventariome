<template>
    <div>
        <b-row class="col-md-6">
            <b-col sm="5">Seleccionar fecha</b-col>
            <b-col>
                <b-input type="date" v-model="fecha" @change="porFecha"/>
            </b-col>
        </b-row>
        <hr>
        <b-table :items="vendidos" :fields="fields">
            <template slot="libro_id" slot-scope="row">
                {{ row.item.libro.titulo }}
            </template>
            <template slot="total_v" slot-scope="row">
                ${{ row.item.total_v }}
            </template>
            <template slot="total_r" slot-scope="row">
                ${{ row.item.total_r }}
            </template>
        </b-table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                fecha: '',
                vendidos: [],
                fields: [
                    {key: 'libro_id', label: 'Libro'}, 
                    {key: 'unidades_v', label: 'Unidades vendidas'},
                    {key: 'total_v', label: 'Subtotal'},
                    {key: 'unidades_r', label: 'Unidades pendientes'},
                    {key: 'total_r', label: 'Subtotal'},
                ]
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
                    // this.vendidos = response.data;
                    console.log(response.data);
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
