<template>
    <div>
        <div v-if="!mostrarDetalles && remisiones.length > 0">
            <b-row>
                <b-col sm="3">
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remision</label>
                        </b-col>
                        <b-col sm="8">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="num_remision" 
                                @keyup.enter="porNumero">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="9">
                    <b-row class="my-1">
                        <b-col sm="1" align="right">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="11">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes"
                            ></b-input>
                            <div class="list-group" v-if="resultsClientes.length">
                                <a 
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(result, i) in resultsClientes" 
                                    @click="porCliente(result)">
                                    {{ result.name }}
                                </a>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>   
            </b-row> 
            <hr>
            <b-table :items="remisiones" :fields="fields">
                <template slot="cliente" slot-scope="row">
                    {{ row.item.cliente.name }}
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
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
                        :disabled="load"
                        v-on:click="entregaLibros(row.item, row.index)">
                        <i class="fa fa-check"></i> Marcar entrega
                        <b-spinner v-if="load" small></b-spinner>
                    </b-button>
                </template>
            </b-table>
        </div>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h4>Remisión No. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Unidades</b>: {{ total_unidades | formatNumber }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Total</b>: ${{ remision.total | formatNumber }}</label>
                    <br>
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
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                num_remision: null,
                queryCliente: '',
                resultsClientes: [],
                remisiones: [],
                fields: [
                    {key: 'id', label: 'No.'}, 
                    {key: 'fecha_creacion', label: 'Fecha de creación'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'},
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
                total_unidades: 0,
                load: false,
            }
        },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        if(response.data.remision.estado == 'Cancelado')
                            this.makeToast('warning', 'No se puede consultar el numero de remisión ingresado');
                        else{
                            this.remision = response.data.remision;
                            this.remisiones = [];
                            this.remisiones.push(this.remision);
                        }
                    }).catch(error => {
                        this.makeToast('warning', 'El numero de remisión no existe');
                    });
                }
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
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id, inicio: this.inicio, final: this.final}}).then(response => {
                    this.queryCliente = result.name;
                    this.resultsClientes = [];
                    this.remisiones = [];
                    response.data.forEach(data => {
                        if(data.estado != 'Cancelado'){
                            this.remisiones.push(data);
                        }
                    });
                });
            },
            getTodo(){
                axios.get('/get_iniciados').then(response => {
                    this.remisiones = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            entregaLibros(remision, i){
                this.load = true;
                axios.put('/vendidos_remision', remision).then(response => {
                    this.load = false;
                    this.remisiones[i].estado = response.data.remision.estado;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                }); 
            },
            viewDetalles(remision){
                this.remision.id = remision.id;
                this.remision.cliente = remision.cliente;
                this.remision.total = remision.total;
                this.total_unidades = 0;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.mostrarDetalles = true;
                    this.remision.datos = response.data.datos;
                    this.remision.datos.forEach(dato => {
                        this.total_unidades += dato.unidades;
                    });
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