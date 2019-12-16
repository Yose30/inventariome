<template>
    <div>
        <check-connection-component></check-connection-component>
        <!-- LISTADO DE NOTAS -->
        <div v-if="listadoCompras">
            <b-row>
                <!-- BUSCAR POR NUMERO DE PEDIDO -->
                <b-col sm="3">
                    <b-row>
                        <b-col class="text-right" sm="6">Num. pedido</b-col>
                        <b-col sm="6"><b-form-input v-model="query_pedido" @keyup.enter="porPedido()"></b-form-input></b-col>
                    </b-row>
                </b-col>
                <!-- BUSCAR NOTA POR CLIENTE -->
                <b-col sm="5">
                    <b-row>
                        <b-col sm="2"><label>Cliente</label></b-col>
                        <b-col sm="10">
                            <b-input style="text-transform:uppercase;" v-model="queryCliente" @keyup="porUsuario()"></b-input>
                        </b-col>
                    </b-row>
                </b-col> 
                <b-col sm="4">
                    <b-row>
                        <b-col sm="2" class="text-right">De:</b-col>
                        <b-col sm="10">
                            <input 
                                class="form-control" 
                                type="date"
                                :state="stateDate"
                                v-model="inicio"
                                @change="porFecha()">
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col sm="2" class="text-right">A:</b-col>
                        <b-col sm="10">
                            <input class="form-control" type="date" v-model="final" @change="porFecha()">
                        </b-col>
                    </b-row>
                </b-col>
            </b-row> 
            <hr>
            <b-row>
                <b-col sm="6">
                    <!-- PAGINACIÓN -->
                    <b-pagination
                        v-model="currentPage"
                        :total-rows="compras.length"
                        :per-page="perPage"
                        aria-controls="my-table"
                        v-if="compras.length > 0"
                    ></b-pagination>
                </b-col>
                <b-col class="text-right">
                    <a 
                        v-if="compras.length > 0"
                        class="btn btn-dark"
                        :href="'/download_compra/' + queryCliente + '/' + inicio + '/' + final + '/general'">
                        <i class="fa fa-download"></i> General
                    </a>
                    <a 
                        v-if="role_id === 1 && compras.length > 0"
                        class="btn btn-dark"
                        :href="'/download_compra/' + queryCliente + '/' + inicio + '/' + final + '/detallado'">
                        <i class="fa fa-download"></i> Detallado
                    </a>
                </b-col>
                <b-col sm="3" class="text-right">
                    <b-button v-if="role_id == 2" variant="success" @click="nueva_compra()">
                        <i class="fa fa-plus"></i> Nuevo pedido
                    </b-button>
                </b-col>
            </b-row>
            <div v-if="compras.length > 0">
                <b-table 
                    id="my-table" 
                    responsive
                    hover
                    :tbody-tr-class="rowClass"
                    :items="compras" :fields="fields"
                    :per-page="perPage" 
                    :current-page="currentPage">
                    <template slot="created_at" slot-scope="row">{{ row.item.created_at | moment }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                    <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                    <template slot="detalles" slot-scope="row">
                        <b-button variant="info" @click="detalles_compra(row.item)">Detalles</b-button>
                    </template>
                    <template slot="entregado" slot-scope="row">
                        <b-button 
                            v-if="!row.item.entregado && role_id === 3"
                            variant="success" 
                            :disabled="load"
                            v-on:click="marcar_entrega(row.item, row.index)">
                            <i class="fa fa-check"></i> Marcar entrega
                        </b-button>
                    </template>
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="3"></th>
                            <th>{{ total_unidades | formatNumber }}</th>
                            <th>${{ total | formatNumber }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </template>
                </b-table>
                <!-- MODALS -->
                <!-- ELEGIR RESPONSABLE DE LA ENTREGA -->
                <b-modal ref="modal-entrega-pedido" title="Responsable de la entrega">
                    <b-row>
                        <b-col sm="8">
                            <b-form-select :state="stateResp" v-model="pedido.entregado_por" :options="responsables"></b-form-select>
                        </b-col>
                        <b-col sm="4">
                            <b-button :disabled="load" v-on:click="guardar_entrega()" variant="success">
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
            </div>
            <div v-else>
                <br>
                <b-alert show variant="dark"><i class="fa fa-warning"></i> No se encontraron registros</b-alert>
            </div>
        </div>
        <!-- MOSTRAR DETALLES DE LA NOTA -->
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h5><b>N. de pedido: {{ pedido.num_pedido }}</b></h5>
                    <label><b>Fecha de creación:</b> {{ pedido.created_at | moment }}</label><br>
                    <label><b>Cliente:</b> {{ pedido.cliente }}</label>
                </b-col>
                <b-col>
                    <label><b>Forma de pago:</b> {{ pedido.tipo_pago }}</label><br>
                    <label v-if="pedido.entregado"><b>Entregado por:</b> {{ pedido.entregado_por }}</label>
                </b-col>
                <b-col sm="2">
                    <b-button variant="dark" :href="`/download_pedido/${pedido.id}`"><i class="fa fa-download"></i> Descargar</b-button>
                </b-col>
                <b-col sm="2" align="right">
                    <b-button variant="secondary" @click="mostrarDetalles = false; listadoCompras = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <b-table :items="pedido.pedidos" :fields="fieldsD">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="4"></th>
                        <th>{{ pedido.total_unidades | formatNumber }}</th>
                        <th>${{ pedido.total | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
        </div>
        <!-- CREAR UNA COMPRA -->
        <div v-if="mostrarCrearCompra">
            <b-row>
                <b-col sm="3">
                    <h4 style="color: #170057">Nuevo pedido</h4>
                </b-col>
                <b-col sm="6" align="right">
                    <b-button 
                        variant="success" 
                        :disabled="load"
                        @click="confirmarCompra()">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </b-col>
                <b-col sm="3" align="right">
                    <b-button variant="secondary" @click="mostrarCrearCompra = false; listadoCompras = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <hr>
            <b-row>
                <b-col>
                    <b-row>
                        <b-col sm="4">Num. de pedido <b id="txtObligatorio">*</b></b-col>
                        <b-col sm="8">
                            <b-form-input 
                                v-model="pedido.num_pedido" 
                                autofocus 
                                :disabled="load"
                                :state="stateP"
                                @change="verificarNPedido()"></b-form-input>
                        </b-col>
                    </b-row>
                    <br>
                    <b-row>
                        <b-col sm="3">Cliente <b id="txtObligatorio">*</b></b-col>
                        <b-col sm="9">
                            <b-form-input 
                                style="text-transform:uppercase;"
                                v-model="pedido.cliente"
                                :disabled="load" 
                                :state="stateC"
                                @change="verificarUsuario()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col>
                    <b-row>
                        <b-col sm="4">Forma de pago <b id="txtObligatorio">*</b></b-col>
                        <b-col sm="8">
                            <b-form-select v-model="pedido.tipo_pago" :options="options"></b-form-select>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <br>
            <b-table :items="pedido.pedidos" :fields="fieldsN">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="eliminar" slot-scope="row">
                    <b-button 
                        variant="danger"
                        @click="eliminarRegistro(row.index)" 
                        :disabled="load">
                        <i class="fa fa-minus-circle"></i>
                    </b-button>
                </template>
                <template slot="thead-top" slot-scope="row">
                    <tr><th colspan="6">Datos del libro</th></tr>
                    <tr>
                        <th colspan="1"></th>
                        <th>
                            <b-input
                                id="input-isbn"
                                autofocus
                                v-model="isbn"
                                @keyup.enter="buscarLibroISBN()"
                                v-if="inputISBN"
                                :disabled="load"
                            ></b-input>
                            <label v-if="!inputISBN">{{ temporal.libro.ISBN }}</label>
                        </th>
                        <th>
                            <b-input
                                style="text-transform:uppercase;"
                                id="input-libro"
                                autofocus
                                v-model="queryTitulo"
                                @keyup="mostrarLibros()"
                                v-if="inputLibro"
                                :disabled="load">
                            </b-input>
                            <div class="list-group" v-if="resultslibros.length" id="listaBL">
                                <a 
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(libro, i) in resultslibros" 
                                    @click="datosLibro(libro)">
                                    {{ libro.titulo }}
                                </a>
                            </div>
                            <label v-if="!inputLibro">{{ temporal.libro.titulo }}</label>
                        </th>
                        <th>
                            <b-form-input 
                                id="input-costo"
                                type="number" 
                                autofocus
                                v-model="costo_unitario"
                                v-if="inputCosto"
                                @keyup.enter="guardarCosto()"
                                :disabled="load">
                            </b-form-input> 
                        </th>
                        <th>
                            <b-form-input 
                                autofocus
                                id="input-unidades"
                                @keyup.enter="guardarRegistro()"
                                v-if="inputUnidades"
                                v-model="unidades" 
                                type="number"
                                required
                                :disabled="load">
                            </b-form-input>
                        </th>
                        <th>
                            <b-button 
                                variant="secondary"
                                @click="eliminarTemporal()" 
                                v-if="inputCosto"
                                :disabled="load">
                                <i class="fa fa-minus-circle"></i>
                            </b-button>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4"></th>
                        <th>{{ pedido.total_unidades | formatNumber }}</th>
                        <th>{{ pedido.total | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
            <!-- MODAL CONFIRMAR -->
            <b-modal ref="mod-confirmar-pedido" size="xl" title="Resumen del pedido">
                <h5><b>N. de pedido: {{ pedido.num_pedido }}</b></h5>
                <label>
                    <b>Cliente: </b><label style="text-transform:uppercase;">{{ pedido.cliente }}</label>
                </label><br>
                <label><b>Forma de pago:</b> {{ pedido.tipo_pago }}</label>
                <b-table :items="pedido.pedidos" :fields="fieldsD">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="4"></th>
                            <th>{{ pedido.total_unidades | formatNumber }}</th>
                            <th>${{ pedido.total | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
                <div slot="modal-footer">
                    <b-row>
                        <b-col sm="10">
                            <b-alert show variant="info">
                                <i class="fa fa-exclamation-circle"></i>
                                <b>Verificar los datos del pedido.</b> En caso de algún error, modificar antes de presionar <b>Confirmar</b> ya que después no se podrán realizar cambios.
                            </b-alert>
                        </b-col>
                        <b-col sm="2" align="right">
                            <b-button :disabled="load" @click="guardarCompra()" variant="success">
                                <i class="fa fa-check"></i> Confirmar
                            </b-button>
                        </b-col>
                    </b-row>
                </div>
            </b-modal>
        </div> 
    </div>
</template>

<script>
    export default {
        props: ['role_id', 'registersall', 'listresponsables'], 
        data() {
            return {
                isbn: '',
                inputISBN: true,
                temporal: {},
                queryTitulo: '',
                inputLibro: true,
                resultslibros: [],
                inputUnidades: false,
                unidades: '',
                costo_unitario: '',
                inputCosto: false,
                load: false,
                pedido: {
                    id: null,
                    num_pedido: '',
                    cliente: '',
                    pedidos: [],
                    total_unidades: 0,
                    total: 0,
                    tipo_pago: null,
                    entregado: false,
                    entregado_por: null,
                    created_at: ''
                },
                stateP: null,
                stateC: null,
                responsables: [],
                options: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'Tarjeta Crédito/Debito', text: 'Tarjeta Crédito/Debito' },
                    { value: 'OXXO', text: 'OXXO' },
                    { value: 'Transferencia Bancaria', text: 'Transferencia Bancaria' },
                ],
                fields: [
                    {key: 'pedido', label: 'Num. pedido'},
                    {key: 'usuario', label: 'Cliente'},
                    {key: 'created_at', label: 'Fecha de creación'},
                    'unidades', 'total',
                    {key: 'tipo_pago', label: 'Forma de pago'},
                    {key: 'detalles', label: ''},
                    {key: 'entregado', label: ''}
                ],
                fieldsN: [
                    {key: 'index', label: 'N.'},
                    'ISBN',
                    {key: 'titulo', label: 'Libro'},
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                    {key: 'eliminar', label: ''}
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    'ISBN', 'libro',
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                ],
                query_pedido: '',
                compras: this.registersall,
                mostrarDetalles: false,
                mostrarCrearCompra: false,
                listadoCompras: true,
                queryCliente: null,
                perPage: 10,
                currentPage: 1,
                inicio: '0000-00-00',
                final: '0000-00-00',
                stateDate: null,
                total_unidades: 0,
                total: 0,
                stateResp: null,
                position: null
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
        mounted: function(){
            this.acumular_totales();
            this.assign_responsables();
        },
        methods: {
            assign_responsables(){
                if(this.role_id === 3){
                    this.responsables.push({
                        value: null,
                        text: 'Selecciona una opción',
                        disabled: true
                    });
                    this.listresponsables.forEach(responsable => {
                        this.responsables.push({
                            value: responsable.responsable,
                            text: responsable.responsable
                        });
                    });
                }
            },
             rowClass(item, type) {
                if (!item) return
                if (item.entregado === true) return 'table-success'
            },
            // RESUMEN DEL PEDIDO
            confirmarCompra(){
                var c = (this.stateP === true && this.stateC === true) && this.pedido.tipo_pago !== null && this.pedido.pedidos.length > 0;
                if(c === true){
                    this.$refs['mod-confirmar-pedido'].show();
                } else {
                    this.makeToast('warning', 'Verificar que todos los datos esten escritos correctamente.');
                }
            },
            // MARCAR ENTREGA DEL PEDIDO
            marcar_entrega(compra, i){
                this.pedido.id = compra.id;
                this.position = i;
                this.$refs['modal-entrega-pedido'].show();
            },
            // GUARDAR LA ENTREGA
            guardar_entrega(){
                if(this.pedido.entregado_por != null){
                    this.load = true;
                    this.stateResp = null;
                    axios.put('/marcar_pedido', this.pedido).then(response => {
                        this.load = false;
                        this.compras[this.position].entregado = true;
                        this.$refs['modal-entrega-pedido'].hide();
                        this.makeToast('success', 'El pedido ha sido marcado como entregado.');
                    }).catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                    });
                } else {
                    this.stateResp = false;
                    this.makeToast('warning', 'Seleccionar responsable para poder continuar.');
                }
            },
            // GUARDAR NOTA
            guardarCompra(){
                var c = (this.stateP === true && this.stateC === true) && this.pedido.tipo_pago !== null && this.pedido.pedidos.length > 0;
                if(c === true){
                    this.load = true;
                    axios.post('/guardar_compra', this.pedido).then(response => {
                        this.$refs['mod-confirmar-pedido'].hide();
                        this.load = false;
                        this.compras.unshift(response.data);
                        this.acumular_totales();
                        this.makeToast('success', 'La compra se guardo correctamente');
                        this.mostrarCrearCompra = false;
                        this.listadoCompras = true;
                    }).catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar');
                    });
                } else {
                    this.makeToast('warning', 'Verificar que todos los datos esten escritos correctamente.');
                } 
            },
            // BUSCAR PEDIDOS POR FECHA
            porFecha(){
                if(this.final != '0000-00-00'){
                    if(this.inicio != '0000-00-00'){
                        axios.get('/buscar_fecha_p', {
                            params: {inicio: this.inicio, final: this.final, usuario: this.queryCliente}}).then(response => {
                            this.compras = response.data;
                            this.acumular_totales();
                        });
                    } else {
                        this.stateDate = false;
                        this.makeToast('warning', 'Es necesario seleccionar la fecha de inicio');
                    }
                }
            },
            // ELIMINAR REGISTRO DEL ARRAY
            eliminarRegistro(i){
                this.pedido.pedidos.splice(i, 1);
                this.acumular();
            },
            // BUSCAR LIBRO POR ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.datosLibro(response.data);
                }).catch(error => {
                    this.makeToast('danger', 'ISBN incorrecto');
                });
            }, 
            // SELECCIONAR LIBRO
            datosLibro(libro){
                this.temporal = {
                    id: libro.id,
                    libro: {
                        ISBN: libro.ISBN,
                        titulo: libro.titulo,
                        piezas: libro.piezas,
                    },
                    costo_unitario: 0,
                    unidades: 0,
                    total: 0
                };
                this.ini_1();
            },
            // Asignar valores a variables
            ini_1(){
                this.inputLibro = false;
                this.inputISBN = false;
                this.inputCosto = true;
                this.resultslibros = [];
            },
            // MOSTRAR COINCIDENCIAS DE LIBROS
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
                } 
            },
            // VERIFICAR QUE EL COSTO SEA VALIDO
            guardarCosto(){
                if(this.costo_unitario > 0){
                    this.inputUnidades = true;
                    this.temporal.costo_unitario = this.costo_unitario;
                }
                else{
                    this.makeToast('warning', 'Costo invalido');
                }
                
            },
            // GUARDAR REGISTRO TEMPORAL EN ARRAY
            guardarRegistro(){
                if(this.unidades > 0){
                    if(this.unidades <= this.temporal.libro.piezas){
                        this.temporal.unidades = this.unidades;
                        this.temporal.total = this.temporal.unidades * this.temporal.costo_unitario;
                        this.pedido.pedidos.push(this.temporal);
                        this.eliminarTemporal();
                        this.acumular();
                    }
                    else{
                        this.makeToast('warning', `${this.temporal.libro.piezas} unidades en existencia`);
                    }
                }
                else{
                    this.makeToast('warning', 'Unidades invalidas');
                }
            },
            // ELIMINAR DATOS TEMPORALES
            eliminarTemporal(){
                this.inputUnidades = false;
                this.inputLibro = true;
                this.inputISBN = true;
                this.queryTitulo = '';
                this.temporal = {};
                this.unidades = '';
                this.costo_unitario = '';
                this.inputCosto = false;
                this.isbn = '';
            },
            // VALIDAR EL CAMPO CLIENTE
            verificarUsuario(){
                if(this.pedido.cliente.length > 4){
                    this.stateC = true;
                }
                else{
                    this.stateC = false;
                    this.makeToast('warning', 'Campo obligatorio, mayor a 5 caracteres');
                }
            },
            acumular(){
                this.pedido.total_unidades = 0;
                this.pedido.total = 0;
                this.pedido.pedidos.forEach(pedido => {
                    this.pedido.total_unidades += parseInt(pedido.unidades);
                    this.pedido.total += parseInt(pedido.total);
                });
            },
            acumular_totales(){
                this.total_unidades = 0;
                this.total = 0;
                this.compras.forEach(compra => {
                    this.total_unidades += parseInt(compra.unidades);
                    this.total += parseInt(compra.total);
                });
            },
            // MOSTRAR DETALLES DE LA NOTA
            detalles_compra(compra){
                axios.get('/detalles_compra', {params: {compra_id: compra.id}}).then(response => {
                    this.listadoCompras = false;
                    this.mostrarDetalles = true;
                    this.pedido = {
                        id: response.data.id,
                        num_pedido: response.data.pedido,
                        cliente: response.data.usuario,
                        pedidos: response.data.pedidos,
                        total_unidades: response.data.unidades,
                        total: response.data.total,
                        tipo_pago: response.data.tipo_pago,
                        created_at: response.data.created_at,
                        entregado: response.data.entregado,
                        entregado_por: response.data.entregado_por,
                    };
                });
            },
            // INICIALIZAR PARA CREAR NOTA
            nueva_compra(){
                this.listadoCompras = false;
                this.mostrarCrearCompra = true;
                this.pedido = {
                    num_pedido: '',
                    cliente: '',
                    pedidos: [],
                    total_unidades: 0,
                    total: 0,
                    tipo_pago: null,
                    created_at: ''
                };
                this.stateP = null;
                this.stateC = null;
            },
            // BUSCAR NOTA POR pedido
            porPedido(){
                axios.get('/buscar_n_pedido', {params: {num_pedido: this.query_pedido}}).then(response => {
                    if(response.data.id != undefined){
                        this.compras = [];
                        this.compras.push(response.data);
                        this.acumular_totales();
                    }
                    else{
                        this.makeToast('warning', 'El numero de pedido no existe');
                    }
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // BUSCAR NOTA POR CLIENTE
            porUsuario(){
                if(this.queryCliente !== null){
                    if(this.queryCliente.length > 0){
                        axios.get('/buscar_usuario_p', {params: {usuario: this.queryCliente}}).then(response => {
                            this.compras = response.data;
                            this.inicio = '0000-00-00';
                            this.final = '0000-00-00';
                            this.acumular_totales();
                        }).catch(error => {
                            this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                        });
                    } else {
                        this.queryCliente = null;
                    }
                }
            },
            verificarNPedido(){
                if(this.pedido.num_pedido.length > 0){
                    axios.get('/buscar_n_pedido', {params: {num_pedido: this.pedido.num_pedido}}).then(response => {
                        if(response.data.id !== undefined){
                            this.stateP = false;
                            this.makeToast('warning', 'El numero de pedido ya existe');
                        } else{
                            this.stateP = true;
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                    });
                }
                else{
                    this.stateP = false;
                    this.makeToast('warning', 'Definir numero de pedido');
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

<style scoped>
    #listaBL{
        position: absolute;
        z-index: 100
    }
    #txtObligatorio {
        color: red;
    }
</style>
