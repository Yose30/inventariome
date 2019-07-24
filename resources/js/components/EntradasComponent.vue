<template>
    <div>
        <b-row>
            <b-col sm="4">
                <b-table :items="entradas" :fields="fields">
                    <template slot="detalles" slot-scope="row">
                        <b-button variant="outline-info" @click="detallesEntrada(row.item)"><i class="fa fa-eye"></i></b-button>
                    </template>
                    <template slot="imprimir" slot-scope="row">
                        <b-button 
                            variant="info"
                            :href="'/imprimirEntrada/' + row.item.id">
                            <i class="fa fa-print"></i>
                        </b-button>
                    </template>
                </b-table>
            </b-col>
            <b-col sm="1"></b-col>
            <b-col sm="7">
                <b-table v-if="registros.length > 0" :items="registros" :fields="fieldsR">
                    <!-- <template slot="index" slot-scope="row">{{ row.index + 1 }}</template> -->
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                </b-table>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                entradas: [],
                registros: [],
                fields: [{key: 'id', label: 'N.'}, 'unidades', 'detalles', 'imprimir'],
                fieldsR: [{key: 'id', label: 'N.'}, 'libro', 'unidades'],
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/all_entradas').then(response => {
                    this.entradas = response.data;
                });
            },
            detallesEntrada(entrada){
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.registros = response.data;
                });
            }
        }
    }
</script>