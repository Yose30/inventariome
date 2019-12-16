<template>
    <div>
        <check-connection-component></check-connection-component>
        <div v-if="!mostrarDetalles">
            <b-row>
                <b-col sm="4">
                    <!-- BUSCAR REMISION POR NUMERO -->
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remisión</label>
                        </b-col>
                        <b-col sm="8">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="num_remision" 
                                @keyup.enter="porNumero()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <!-- BUSCAR REMISION POR CLIENTE -->
                <b-col sm="6">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()"></b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listR">
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
                <b-col sm="2">
                    <!-- <b-button variant="info" pill v-b-modal.modal-ayudaE><i class="fa fa-info-circle"></i> Ayuda</b-button> -->
                </b-col>
            </b-row> 
            <br>
            <!-- LISTADO DE REMISIONES -->
            <b-alert v-if="remisiones.length === 0" show variant="secondary">
                <i class="fa fa-warning"></i> No se encontraron registros.
            </b-alert>
            <b-table 
                responsive
                :items="remisiones" 
                :fields="fields"
                v-if="remisiones.length > 0"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
                <template slot="cliente" slot-scope="row">
                    {{ row.item.cliente.name }}
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button 
                        variant="info"
                        @click="viewDetalles(row.item)">
                        Detalles
                    </b-button>
                </template>
                <template slot="registrar_entrega" slot-scope="row">
                    <b-button 
                        variant="success" 
                        v-if="row.item.estado == 'Iniciado'"
                        :disabled="load"
                        v-b-modal.modal-marcar
                        v-on:click="inicializar_entrega(row.item, row.index)">
                        <i class="fa fa-check"></i> Marcar entrega
                    </b-button>
                </template>
            </b-table>
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="remisiones.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="remisiones.length > 0"
            ></b-pagination>
        </div>
        <!-- DETALLES DE LA REMISIÓN -->
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h5><b>Remisión No. {{ remision.id }}</b></h5>
                    <label><b>Fecha de creación:</b> {{ remision.created_at | moment }}</label>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="secondary" @click="mostrarDetalles = false">
                            <i class="fa fa-mail-reply"></i> Regresar
                        </b-button>
                    </div>
                </b-col>
            </b-row>
            <label><b>Cliente:</b> {{ remision.cliente.name }}</label>
            <b-table :items="remision.datos" :fields="fieldsD">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="subtotal" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="3"></th>
                        <th>{{ total_unidades | formatNumber }}</th>
                        <th>${{ remision.total | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
        </div>
        <!-- MODALS -->
        <!-- ELEGIR RESPONSABLE DE LA ENTREGA -->
        <b-modal ref="modalMarcarE" id="modal-marcar" title="Responsable de la entrega">
            <b-row>
                <b-col sm="8">
                    <b-form-select :state="stateResp" :disabled="load" v-model="responsable" :options="options"></b-form-select>
                </b-col>
                <b-col sm="4">
                    <b-button v-on:click="entregaLibros()" :disabled="load" variant="success">
                        <i class="fa fa-check"></i> Guardar <b-spinner v-if="load" small></b-spinner>
                    </b-button>
                </b-col>
            </b-row>
            <template v-slot:modal-footer>
                <b-alert show variant="info">
                    <i class="fa fa-exclamation-circle"></i> Verificar los datos antes de presionar <b>Guardar</b>, ya que después no se podrán realizar cambios.
                </b-alert>
            </template>
        </b-modal>
        <!-- INFORMACIÓN ACERCA DEL APARTADO -->
        <b-modal id="modal-ayudaE" hide-backdrop hide-footer title="Ayuda">
            En este apartado solo aparecerán las remisiones que <b>NO</b> han sido marcadas como entregadas.
            <hr>
            <h5 id="titleA"><b>Búsqueda de remisiones</b></h5>
            <p>
                <ul>
                    <li>
                        <b>Búsqueda por remisión: </b>
                        Ingresar el número de folio de la remisión que se desea buscar y presionar <label id="ctrlS">Enter</label>.
                    </li>
                    <li><b>Búsqueda por cliente: </b> Escribir el nombre del cliente y elegir entre las opciones que aparezcan.</li>
                </ul>
            </p>
            <hr>
            <h5 id="titleA"><b>Detalles de la remisión</b></h5>
            <p>En <b id="titleA">Detalles</b> podrá consultar los datos de la remisión.</p>
            <hr>
            <h5 id="titleA"><b>Marcar entrega</b></h5>
            <p>Al presionar <b id="titleA">Marcar entrega</b> de una remisión, aparecerá una ventana emergente, donde tendrá que elegir el responsable de la entrega, es decir quien entregara el pedido.</p>
            <b><i class="fa fa-info-circle"></i> Nota: </b>Verificar antes de marcar una entrega, ya que después no se podrán realizar cambios.
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['registersall', 'listresponsables'],
        data() {
            return {
                num_remision: null,
                queryCliente: '',
                resultsClientes: [],
                remisiones: this.registersall,
                options: [],
                responsable: null,
                fields: [
                    {key: 'id', label: 'Folio'}, 
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
                    'subtotal'
                ],
                mostrarDetalles: false,
                remision: {},
                total_unidades: 0,
                load: false,
                perPage: 15,
                currentPage: 1,
                entrega: {},
                position: null,
                stateResp: null
            }
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            },
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        created: function(){
            this.assign_responsables();
        },
        methods: {
            assign_responsables(){
                this.options.push({
                    value: null,
                    text: 'Selecciona una opción',
                    disabled: true
                });
                this.listresponsables.forEach(responsable => {
                    this.options.push({
                        value: responsable.responsable,
                        text: responsable.responsable
                    });
                });
            },
            // BUSCAR REMISIÓN POR NUMERO
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        if(response.data.remision.estado == 'Iniciado'){
                            this.remision = response.data.remision;
                            this.remisiones = [];
                            this.remisiones.push(this.remision);
                        }
                        if(response.data.remision.estado == 'Cancelado')
                            this.makeToast('warning', 'La remisión esta cancelada.');
                        if(response.data.remision.estado == 'Proceso' || response.data.remision.estado == 'Terminado')
                            this.makeToast('warning', 'La remisión ya fue marcada como entregada. Consultar en el apartado de remisiones.');
                    }).catch(error => {
                        this.makeToast('danger', 'Error al consultar el numero de remisión ingresado.');
                    });
                }
            },
            // MOSTRAR LOS CLIENTES
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    this.resultsClientes = [];
                }
            },
            // MOSTRAR REMISIONES INICIADAS POR CLIENTE
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id}}).then(response => {
                    this.queryCliente = result.name;
                    this.resultsClientes = [];
                    this.remisiones = [];
                    response.data.forEach(data => {
                        if(data.estado == 'Iniciado')
                            this.remisiones.push(data);
                    });
                });
            },
            // INICIALIZAR VALORES DE ENTREGA
            inicializar_entrega (remision, i){
                this.entrega = {
                    remision: remision.id,
                    responsable: null
                };
                this.position = i;
            },
            // MARCAR ENTREGA DE REMISIÓN
            entregaLibros(){
                if(this.responsable != null){
                    this.load = true;
                    this.stateResp = null;
                    this.entrega.responsable = this.responsable;
                    axios.put('/vendidos_remision', this.entrega).then(response => {
                        this.load = false;
                        this.remisiones[this.position].estado = response.data.remision.estado;
                        this.$refs['modalMarcarE'].hide();
                        this.makeToast('success', 'La remisión ha sido marcada como entregada');
                    }).catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                    });
                } else {
                    this.stateResp = false;
                    this.makeToast('warning', 'Seleccionar responsable para poder continuar.');
                }
            },
            // VER DETALLES DE LA REMISIÓN
            viewDetalles(remision){
                this.remision.id = remision.id;
                this.remision.cliente = remision.cliente;
                this.remision.total = remision.total;
                this.total_unidades = 0;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.mostrarDetalles = true;
                    this.remision.datos = response.data.remision.datos;
                    this.remision.datos.forEach(dato => {
                        this.total_unidades += dato.unidades;
                    });
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
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

<style>
    #listR{
        position: absolute;
        z-index: 100;
    }
    #listR a {
        /* background-color: #f2f8ff; */
    }
    #titleA, #ctrlS{
        font-style: oblique;
    }
    #ctrlS {
        color: #10013d;
    }
</style>