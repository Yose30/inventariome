<template>
    <div>
        <div v-if="listadoAdeudos">
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
                            <b-input
                            v-model="queryCliente"
                            @keyup="mostrarClientes"
                            ></b-input>
                            <div class="list-group" v-if="resultsClientes.length">
                                <a 
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(result, i) in resultsClientes" 
                                    @click="adeudosCliente(result)">
                                    {{ result.name }}
                                </a>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="adeudos" :fields="fieldsA" :tbody-tr-class="rowClass">
                <template slot="cliente_id" slot-scope="row">
                    {{ row.item.cliente.name }}
                </template>
                <template slot="total_devolucion" slot-scope="row">
                    ${{ row.item.total_devolucion }}
                </template>
                <template slot="total_adeudo" slot-scope="row">
                    ${{ row.item.total_adeudo }}
                </template>
                <template slot="total_abonos" slot-scope="row">
                    ${{ row.item.total_abonos }}
                </template>
                <template slot="total_pendiente" slot-scope="row">
                    ${{ row.item.total_pendiente }}
                </template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="detallesAdeudo(row.item)">Detalles</b-button>
                </template>
                <template slot="registrar_pago" slot-scope="row">
                    <b-button 
                        v-if="row.item.total_pendiente != 0 && row.item.total_devolucion == 0" 
                        @click="registrarDevAdeudo(row.item, row.index)"
                        variant="primary">Registrar devolución
                    </b-button>
                </template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="2"></th>
                        <th>${{ total_adeudo }}</th>
                        <th>${{ total_devolucion }}</th>
                        <th>${{ total_pagos }}</th>
                        <th>${{ total_pendiente }}</th>
                    </tr>
                </template>
            </b-table>
            
        </div>

        <div v-if="mostrarRegistrar">
            <b-row>
                <b-col align="right">
                    <b-button 
                        variant="success" 
                        @click="guardarDevolucion" 
                        :disabled="load">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </b-col>
                <b-col align="right">
                    <b-button variant="secondary" @click="mostrarRegistrar = false; listadoAdeudos = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <hr>
            <b-row>
                <b-col>
                    <label>Remisión No.: {{ adeudo.remision_num }}</label><br>
                    <label>Fecha del adeudo: {{ adeudo.fecha_adeudo }}</label>
                </b-col>
                <b-col align="right">
                    <label><b>Total adeudo</b>: ${{ adeudo.total_adeudo }}</label>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="adeudo.devoluciones" :fields="fieldsRD">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                <template slot="unidades" slot-scope="row">
                    <b-input 
                        type="number" 
                        @change="obtenerSubtotal(row.item, row.index)"
                        v-model="row.item.unidades">
                    </b-input>
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            </b-table>
        </div> 

        <div v-if="mostrarAbonos">
            <b-row>
                <b-col sm="4">
                    <label><b>Cliente</b><br>{{ adeudo.cliente.name }}</label>
                </b-col>
                <b-col sm="2">
                    <!-- <label><b>Total adeudo</b> ${{ adeudo.total_adeudo }}</label> -->
                </b-col>
                <b-col sm="2">
                    <!-- <label><b>Pagos</b> ${{ adeudo.total_abonos }}</label>
                    <label><b>Devolución</b> ${{ adeudo.total_devolucion }}</label> -->
                </b-col>
                <b-col sm="2">
                    <!-- <label><b>Total pendiente</b> ${{ adeudo.total_pendiente }}</label> -->
                </b-col>
                <b-col sm="2" align="right">
                    <b-button variant="secondary" @click="listadoAdeudos = true; mostrarAbonos = false;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <div class="row">
                <h4 class="col-md-10">Salida</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarSalida ? 'collapsed' : null"
                    :aria-expanded="mostrarSalida ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarSalida = !mostrarSalida">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-1" v-model="mostrarSalida" class="mt-2">
                <b-table :items="adeudo.datos" :fields="fieldsD">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total }}</template>
                </b-table>
            </b-collapse>
            <hr>
            <div class="row">
                <h4 class="col-md-10">Devolución</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarDevolucion ? 'collapsed' : null"
                    :aria-expanded="mostrarDevolucion ? 'true' : 'false'"
                    aria-controls="collapse-2"
                    @click="mostrarDevolucion = !mostrarDevolucion">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-2" v-model="mostrarDevolucion" class="mt-2">
                <b-table :items="adeudo.devoluciones" :fields="fieldsD">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total }}</template>
                </b-table>
            </b-collapse>
            <!-- <hr>
            <div class="row">
                <h4 class="col-md-10">Pagos</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarPagos ? 'collapsed' : null"
                    :aria-expanded="mostrarPagos ? 'true' : 'false'"
                    aria-controls="collapse-3"
                    @click="mostrarPagos = !mostrarPagos">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-3" v-model="mostrarPagos" class="mt-2">
                
            </b-collapse>
            <hr>
            <div class="row">
                <h4 class="col-md-10">Pagar</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarFinal ? 'collapsed' : null"
                    :aria-expanded="mostrarFinal ? 'true' : 'false'"
                    aria-controls="collapse-3"
                    @click="mostrarFinal = !mostrarFinal">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-3" v-model="mostrarFinal" class="mt-2">
                
            </b-collapse> -->
            <!-- <b-table :items="adeudo.abonos" :fields="fieldsP">
                <template slot="index" slot-scope="row">
                    {{ row.index + 1 }}
                </template>
                <template slot="pago" slot-scope="row">
                    ${{ row.item.pago }}
                </template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
            </b-table> -->
        </div>
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                datos: [],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    'ISBN',
                    {key: 'titulo', label: 'Libro'},
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                    {key: 'eliminar', label: ''}
                ],
                fieldsRD: [
                    {key: 'index', label: 'N.'},
                    'ISBN',
                    {key: 'titulo', label: 'Libro'},
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    {key: 'unidades_resta', label: 'Unidades pendientes'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'}
                ],
                listadoAdeudos: true,
                mostrarRegistrar: false,
                mostrarSeleccionar: true,
                mostrarAbonos: false,
                mostrarForm: false,
                mostrarDatos: true,
                clientes: [],
                fieldsC: [
                    {key: 'name', label: 'Nombre'},
                    {key: 'email', label: 'Correo'},
                    {key: 'direccion', label: 'Dirección'},
                    {key: 'seleccionar', label: ''}
                ],
                cliente: {},
                adeudo: {
                    id: 0,
                    cliente_id: 0,
                    cliente: {},
                    remision_num: 0,
                    fecha_adeudo: '',
                    total_adeudo: 0,
                    total_pendiente: 0,
                    datos: [],
                    devoluciones: []
                },
                state: null,
                load: false,
                adeudos: [],
                fieldsA: [
                    {key: 'remision_num', label: 'Remisión No.'},
                    {key: 'cliente_id', label: 'Cliente'},
                    {key: 'total_adeudo', label: 'Total adeudo'},
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'total_abonos', label: 'Pagos'},
                    {key: 'total_pendiente', label: 'Total pendiente'},
                    {key: 'detalles', label: ''},
                    {key: 'registrar_pago', label: ''}
                ],
                fieldsP: [
                    {key: 'index', label: 'No.'},
                    'pago',
                    {key: 'created_at', label: 'Fecha de pago'},
                ],
                abono: {
                    adeudo_id: 0,
                    pago: 0,
                },
                posicion: null,
                queryCliente: '',
                resultsClientes: [],
                total_adeudo: 0,
                total_pagos: 0,
                total_pendiente: 0,
                total_devolucion: 0,
                stateR: null,
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
                mostrarSalida: false,
                mostrarDevolucion: false,
                mostrarPagos: false,
                mostrarFinal: false,
                devoluciones: [],
                vendidos: [],
                num_remision: null,

            }
        },
        created: function(){
			this.obtenerAdeudos();
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            }
        },
        methods: {
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_adeudo', {params: {num_remision: this.num_remision}}).then(response => {
                        if(response.data.id != undefined){
                            this.adeudos = [];
                            this.adeudos.push(response.data);
                            this.acumular();
                        }
                        else{
                            this.makeToast('warning', 'Numero de remisión incorrecto');
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentarlo');
                    });
                }
            },
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.datosLibro(response.data);
                }).catch(error => {
                    this.makeToast('danger', 'ISBN incorrecto');
                });
            },
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
               } 
            },
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
            ini_1(){
                this.inputLibro = false;
                this.inputISBN = false;
                this.inputCosto = true;
                this.resultslibros = [];
            },
            guardarRegistro(){
                if(this.unidades > 0){
                    this.temporal.unidades = this.unidades;
                    this.temporal.total = this.temporal.unidades * this.temporal.costo_unitario;
                    // if(this.editar == true){
                    //     this.nuevos.push(this.temporal);
                    // }
                    this.datos.push(this.temporal);
                    this.adeudo.total_adeudo += this.temporal.total;
                    this.eliminarTemporal();
                }
                else{
                    this.makeToast('warning', 'Unidades invalidas');
                }
            },
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
            eliminarRegistro(dato, i){
                // if(this.editar == true){
                //     this.eliminados.push(registro);
                // }
                this.datos.splice(i, 1);
                this.adeudo.total_adeudo = this.adeudo.total_adeudo - dato.total;
            },
            rowClass(item, type) {
                if (!item) return
                if (item.total_pendiente == 0) return 'table-success'
            },
            guardarCosto(){
                if(this.costo_unitario > 0){
                    this.inputUnidades = true;
                    this.temporal.costo_unitario = this.costo_unitario;
                }
                else{
                    this.makeToast('warning', 'Costo invalido');
                }
            },
            verificarRemision(){
                if(this.adeudo.remision_num.length > 0){
                    axios.get('/buscarRemision', {params: {remision_num: this.adeudo.remision_num}}).then(response => {
                        if(response.data.adeudo == 0 && response.data.remision == 0){
                            this.stateR = null;
                        }
                        else{
                            this.stateR = false;
                            this.makeToast('danger', 'El numero de remisión ya existe');
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.stateR = false;
                    this.makeToast('danger', 'Definir folio');
                }
            },
            obtenerAdeudos(){
                axios.get('/obtener_adeudos').then(response => {
                    this.adeudos = response.data;
                    this.acumular();
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            registrarAdeudo(){
                this.clientes = [];
                this.cliente = {};
                this.mostrarSeleccionar = true;
                this.mostrarDatos = false;
                this.mostrarForm = false;
                this.ini_adeudo();
                axios.get('/getTodo').then(response => {
                    this.clientes = response.data;
                    this.listadoAdeudos = false;
                    this.mostrarRegistrar = true;
                }).catch(error => {
                   this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            seleccionCliente(cliente){
                this.cliente = {};
                this.cliente = cliente;
                this.mostrarSeleccionar = false;
                this.mostrarDatos = true;
                this.mostrarForm = true;
            },
            registrarDevAdeudo(adeudo, i){
                this.posicion = i;
                this.ini_adeudo();
                axios.get('/detalles_adeudo', {params: {id: adeudo.id}}).then(response => {
                    this.adeudo = {
                        id: response.data.adeudo.id,
                        cliente_id: response.data.adeudo.cliente_id,
                        cliente: response.data.adeudo.cliente,
                        remision_num: response.data.adeudo.remision_num,
                        fecha_adeudo: response.data.adeudo.fecha_adeudo,
                        total_adeudo: response.data.adeudo.total_adeudo,
                        total_pendiente: response.data.adeudo.total_pendiente,
                        datos: response.data.datos,
                        devoluciones: response.data.devoluciones
                    };
                    this.listadoAdeudos = false;
                    this.mostrarRegistrar = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            guardarDevolucion(){
                this.load = true;
                axios.put('/guardar_adeudo_devolucion', this.adeudo).then(response => {
                    this.adeudos[this.posicion].total_pendiente = response.data.total_pendiente;
                    this.adeudos[this.posicion].total_devolucion = response.data.total_devolucion;
                    this.acumular();
                    this.load = false;
                    this.mostrarRegistrar = false;
                    this.listadoAdeudos = true;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            registrarAbono(adeudo, i){
                this.ini_adeudo();
                this.abono = { adeudo_id: 0, pago: 0, };
                this.posicion = i;
                this.adeudo = adeudo;
                this.abono.adeudo_id = this.adeudo.id;
            },
            guardarAbono(){
                if(this.abono.pago > 0){
                    if(this.abono.pago <= this.adeudo.total_pendiente){
                        this.state = null;
                        this.load = true;
                        axios.post('/guardar_abono', this.abono).then(response => {
                            this.$bvModal.hide('modal-pago');
                            this.load = false;
                            this.adeudos[this.posicion].total_abonos = response.data.total_abonos;
                            this.adeudos[this.posicion].total_pendiente = response.data.total_pendiente;
                            this.acumular();
                        })
                        .catch(error => {
                            this.load = false;
                            this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                        });
                    }
                    else{
                        this.state = false;
                        this.makeToast('warning', 'El pago es mayor al total pendiente');
                    }
                }
                else{
                    this.state = false;
                    this.makeToast('warning', 'El pago tiene que ser mayor a 0');
                }
            },
            detallesAdeudo(adeudo){
                this.ini_adeudo();
                axios.get('/detalles_adeudo', {params: {id: adeudo.id}}).then(response => {
                    this.mostrarSalida = false;
                    this.mostrarDevolucion = false;
                    this.mostrarPagos = false;
                    this.mostrarFinal = false;
                    this.adeudo = {
                        cliente_id: response.data.adeudo.cliente_id,
                        cliente: response.data.adeudo.cliente,
                        remision_num: response.data.adeudo.remision_num,
                        fecha_adeudo: response.data.adeudo.fecha_adeudo,
                        total_adeudo: response.data.adeudo.total_adeudo,
                        total_pendiente: response.data.adeudo.total_pendiente,
                        datos: response.data.datos,
                        devoluciones: response.data.devoluciones
                    };
                    this.listadoAdeudos = false;
                    this.mostrarAbonos = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            ini_adeudo(){
                this.adeudo = {
                    id: 0,
                    cliente_id: 0,
                    cliente: {},
                    remision_num: 0,
                    fecha_adeudo: '',
                    total_adeudo: 0,
                    total_pendiente: 0,
                    datos: [],
                    devoluciones: []
                };
            },
            acumular(){
                this.total_adeudo = 0;
                this.total_pagos = 0;
                this.total_pendiente = 0;
                this.total_devolucion = 0;
                this.adeudos.forEach(adeudo => {
                    this.total_adeudo += adeudo.total_adeudo;
                    this.total_pagos += adeudo.total_abonos;
                    this.total_pendiente += adeudo.total_pendiente;
                    this.total_devolucion += adeudo.total_devolucion;
                });
            },
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    this.obtenerAdeudos();
                }
            },
            adeudosCliente(cliente){
                this.resultsClientes = [];
                this.queryCliente = cliente.name;
                axios.get('/adeudos_cliente', {params: {cliente_id: cliente.id}}).then(response => {
                    this.adeudos = response.data;
                    this.acumular();
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            obtenerSubtotal(devolucion, i){
                if(devolucion.unidades >= 0){
                    if(devolucion.unidades > devolucion.unidades_resta){
                        this.makeToast('warning', 'Las unidades son mayor a las unidades pendientes');
                        this.adeudo.devoluciones[i].unidades = 0;
                        this.adeudo.devoluciones[i].total = 0;
                    }
                    else{
                        this.adeudo.devoluciones[i].total = devolucion.unidades * devolucion.dato.costo_unitario;
                    }
                }
                else{
                    this.makeToast('warning', 'Unidades invalidas');
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

<style>
    #btnEditar {
        color: #ffb300;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #btnCancelar {
        color: red;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #txtObligatorio {
        color: red;
    }
</style>
