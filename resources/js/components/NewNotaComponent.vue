<template>
    <div>
        <div v-if="listadoNotas">
            <div align="right" v-if="role_id == 3">
                <b-button variant="success" @click="func_crearNota"><i class="fa fa-plus"></i> Crear nota</b-button>
            </div>
            <!-- <b-alert v-if="!mostrarDetalles && !mostrarCrearNota && !mostrarNewPago && notes.length == 0" show variant="secondary">
                <i class="fa fa-exclamation-triangle"></i> No hay notas
            </b-alert> -->
            <b-table 
                v-if="notes.length > 0" 
                :items="notes" :fields="fieldsN">
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
                <template slot="total_salida" slot-scope="row">
                    ${{ row.item.total_salida }}
                </template>
                <template slot="pagos" slot-scope="row">
                    ${{ row.item.pagos }}
                </template>
                <template slot="total_devolucion" slot-scope="row">
                    ${{ row.item.total_devolucion }}
                </template>
                <template slot="total_pagar" slot-scope="row">
                    ${{ row.item.total_pagar }}
                </template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="outline-info" @click="detallesNota(row.item)">Detalles</b-button>
                </template>
                <template slot="pagar" slot-scope="row">
                    <b-button 
                        v-if="role_id == 3 && row.item.total_pagar > 0" 
                        variant="outline-primary" 
                        @click="registrarPago(row.item, row.index)">Registrar pago
                    </b-button>
                </template>
                <template slot="devolucion" slot-scope="row">
                    <b-button
                        v-if="role_id == 3 && row.item.total_pagar > 0" 
                        variant="outline-primary"
                        @click="registrarDevolucion(row.item, row.index)">Registrar devolución</b-button>
                </template>
                <template slot="editar" slot-scope="row">
                    <b-button
                        v-if="role_id == 3 && row.item.total_pagar ==row.item.total_salida" 
                        variant="outline-warning"
                        @click="editarNota(row.item, row.index)"
                        >Editar</b-button>
                </template>
            </b-table>
        </div>
        <div v-if="mostrarNewPago">
            <h5>Registrar pago</h5>
            <hr>
            <b-row>
                <b-col>
                    <h4>Nota n. {{ nota.id }}</h4>
                    <label>Cliente: {{ nota.cliente }}</label>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button v-if="btnGuardar" variant="success" @click="guardarPagosNota">
                            <i class="fa fa-check"></i> Guardar
                        </b-button>
                    </div>
                </b-col>
                <b-col align="right">
                    <b-button variant="outline-secondary" @click="mostrarNewPago = false; listadoNotas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="nota.registers" :fields="fieldsNP">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                <template slot="unidades" slot-scope="row">
                    <b-input 
                        type="number" 
                        @change="verificarUnidades(row.item.unidades_base, row.item.unidades_pendiente, row.item.costo_unitario, row.index)" 
                        v-model="row.item.unidades_base">
                    </b-input>
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total_base }}</template>
            </b-table>
            <h5 class="text-right">${{ total_vendido }}</h5>
        </div>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col>
                    <h4>Nota n. {{ nota.id }}</h4>
                    <label>Cliente: {{ nota.cliente }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Unidades</b>: {{ total_unidades }}</label>
                </b-col>
                <b-col>
                    <br>
                    <label><b>Total</b>: ${{ nota.total_salida }}</label><br>
                </b-col>
                <b-col align="right">
                    <b-button variant="outline-secondary" @click="mostrarDetalles = false; listadoNotas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="nota.registers" :fields="fieldsD">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total }}</template>
                <template slot="pagos" slot-scope="row">
                    <b-button v-if="row.item.payments.length > 0" variant="outline-info" @click="row.toggleDetails">
                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}}
                    </b-button>
                </template>
                <template slot="row-details" slot-scope="row">
                    <b-card>
                        <b-table :items="row.item.payments" :fields="fieldsP">
                            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                            <template slot="unidades" slot-scope="row">{{ row.item.unidades }}</template>
                            <template slot="pago" slot-scope="row">$ {{ row.item.pago }}</template>
                            <template slot="created_at" slot-scope="row">{{ row.created_at | moment }}</template>
                        </b-table>
                    </b-card>
                </template>
            </b-table>
        </div>
        <div v-if="mostrarDevolucion">
            <h5>Registrar devolución</h5>
            <hr>
            <b-row>
                <b-col>
                    <h4>Nota n. {{ nota.id }}</h4>
                    <label>Cliente: {{ nota.cliente }}</label>
                </b-col>
                <b-col>
                    <div class="text-right">
                        <b-button v-if="btnGuardar" variant="success" @click="guardarDevolucion">
                            <i class="fa fa-check"></i> Guardar
                        </b-button>
                    </div>
                </b-col>
                <b-col align="right">
                    <b-button variant="outline-secondary" @click="mostrarDevolucion = false; listadoNotas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="nota.registers" :fields="fieldsNP">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                <template slot="unidades" slot-scope="row">
                    <b-input 
                        type="number" 
                        @change="verificarUnidades(row.item.unidades_base, row.item.unidades_pendiente, row.item.costo_unitario, row.index)" 
                        v-model="row.item.unidades_base">
                    </b-input>
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total_base }}</template>
            </b-table>
            <h5 class="text-right">${{ total_vendido }}</h5>
        </div>
        <div v-if="mostrarCrearNota">
            <b-row>
                <b-col>
                    <h4>{{ editar ? 'Editar' : 'Crear' }} nota</h4>
                </b-col>
                <b-col align="right">
                    <b-button variant="outline-secondary" @click="mostrarCrearNota = false; listadoNotas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </b-col>
            </b-row>
            <hr>
            <b-row class="col-md-8">
                <b-col sm="3">Cliente</b-col>
                <b-col sm="6">
                    <b-form-input v-model="cliente" :disabled="load" :state="state" @keyup.enter="func_viewForm"></b-form-input>
                </b-col>
                <b-col sm="3">
                    <b-button 
                        v-if="registers.length > 0 && editar == false" 
                        variant="success" 
                        :disabled="load"
                        @click="guardarNota">
                        {{ !load ? 'Guardar' : 'Guardando' }}
                    </b-button>
                    <b-button 
                        v-if="registers.length > 0 && editar == true" 
                        variant="success" 
                        :disabled="load"
                        @click="actualizarNota">
                        {{ !load ? 'Guardar cambios' : 'Guardando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <div v-if="viewForm">
                <b-table :items="registers" :fields="fields">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total }}</template>
                    <template slot="eliminar" slot-scope="row">
                        <b-button variant="danger" @click="eliminarRegistro(row.item, row.index)" :disabled="load">
                            <i class="fa fa-minus-circle"></i>
                        </b-button>
                    </template>
                </b-table>
                <hr>
                <b-row>
                    <b-col sm="3">
                        <label for="input-isbn">ISBN</label>
                        <b-input
                            id="input-isbn"
                            v-model="isbn"
                            @keyup.enter="buscarLibroISBN"
                            v-if="inputISBN"
                            :disabled="load"
                        ></b-input>
                        <br><b v-if="!inputISBN">{{ temporal.libro.ISBN }}</b>
                    </b-col>
                    <b-col sm="4">
                        <label for="input-libro">Libro</label>
                        <b-input
                            id="input-libro"
                            v-model="queryTitulo"
                            @keyup="mostrarLibros"
                            v-if="inputLibro"
                            :disabled="load">
                        </b-input>
                        <div class="list-group" v-if="resultslibros.length">
                            <a 
                                href="#" 
                                v-bind:key="i" 
                                class="list-group-item list-group-item-action" 
                                v-for="(libro, i) in resultslibros" 
                                @click="datosLibro(libro)">
                                {{ libro.titulo }}
                            </a>
                        </div>
                        <br><b v-if="!inputLibro">{{ temporal.libro.titulo }}</b>
                    </b-col>
                    <b-col sm="2">
                        <label for="input-costo">Costo unitario</label>
                        <b-form-input 
                            id="input-costo"
                            type="number" 
                            v-model="costo_unitario"
                            v-if="inputCosto"
                            @keyup.enter="guardarCosto"
                            :disabled="load">
                        </b-form-input> 
                    </b-col>
                    <b-col sm="2">
                        <label for="input-unidades">Unidades</label>
                        <b-form-input 
                            id="input-unidades"
                            @keyup.enter="guardarRegistro"
                            v-if="inputUnidades"
                            v-model="unidades" 
                            type="number"
                            required
                            :disabled="load">
                        </b-form-input>
                    </b-col>
                    <b-col sm="1">
                        <b-button 
                            variant="secondary"
                            @click="eliminarTemporal" 
                            v-if="inputCosto"
                            :disabled="load">
                            <i class="fa fa-minus-circle"></i>
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </div> 
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                cliente: '',
                viewForm: false,
                registers: [],
                fields: [
                    {key: 'index', label: 'N.'},
                    'ISBN',
                    {key: 'titulo', label: 'Libro'},
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                    'eliminar'
                ],
                fieldsP: [
                    {key: 'index', label: 'N.'}, 
                    'unidades',
                    'pago', 
                    {key: 'created_at', label: 'Fecha'}, 
                ],
                fieldsN: [
                    'folio',
                    'cliente',
                    {key: 'created_at', label: 'Fecha de creación'},
                    {key: 'total_salida', label: 'Salida'},
                    'pagos',
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'total_pagar', label: 'Pagar'},
                    {key: 'detalles', label: ''},
                    {key: 'pagar', label: ''},
                    {key: 'devolucion', label: ''},
                    {key: 'editar', label: ''},
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    'ISBN', 'libro',
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                    {key: 'unidades_pagado', label: 'Unidades vendidas', variant: 'info'},
                    {key: 'unidades_devuelto', label: 'Unidades devueltas', variant: 'info'},
                    {key: 'unidades_pendiente', label: 'Unidades pendientes', variant: 'info'},
                    'pagos'
                ],
                fieldsNP: [
                    {key: 'index', label: 'N.'},
                    'ISBN', 'libro',
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'unidades_pendiente', label: 'Unidades pendientes'},
                    {key: 'total', label: 'Subtotal'},
                ],
                state: null,
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
                nota: {},
                notes: [],
                mostrarDetalles: false,
                total_unidades: 0,
                mostrarCrearNota: false,
                mostrarNewPago: false,
                total_vendido: 0,
                btnGuardar: false,
                posicion: 0,
                mostrarDevolucion: false,
                listadoNotas: true,
                editar: false,
                eliminados: [],
                nuevos: []
            }
        },
        created: function(){
			this.getTodo();
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            }
        },
        methods: {
            getTodo(){
                axios.get('/all_notas').then(response => {
                    this.notes = response.data;
                });
            },
            func_crearNota(){
                this.listadoNotas = false;
                this.mostrarCrearNota = true;
                this.nota = {};
                this.cliente = '';
                this.registers = [];
                this.editar = false;
            },
            func_viewForm(){
                if(this.cliente.length > 4){
                    this.viewForm = true;
                    this.state = null;
                }
                else{
                    this.state = false;
                    this.makeToast('warning', 'Campo obligatorio, mayor a 5 caracteres');
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
            guardarCosto(){
                if(this.costo_unitario > 0){
                    this.inputUnidades = true;
                    this.temporal.costo_unitario = this.costo_unitario;
                }
                else{
                    this.makeToast('warning', 'Costo invalido');
                }
                
            },
            guardarRegistro(){
                if(this.unidades > 0){
                    if(this.unidades <= this.temporal.libro.piezas){
                        this.temporal.unidades = this.unidades;
                        this.temporal.total = this.temporal.unidades * this.temporal.costo_unitario;
                        if(this.editar == true){
                            this.nuevos.push(this.temporal);
                        }
                        this.registers.push(this.temporal);
                        this.eliminarTemporal();
                    }
                    else{
                        this.makeToast('warning', `${this.temporal.libro.piezas} unidades en existencia`);
                    }
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
            eliminarRegistro(registro, i){
                if(this.editar == true){
                    this.eliminados.push(registro);
                }
                this.registers.splice(i, 1);
            },
            guardarNota(){
                this.load = true;
                if(this.cliente.length > 4){
                    this.state = null;
                    this.nota.cliente = this.cliente;
                    this.nota.registers = this.registers;
                    axios.post('/guardar_nota', this.nota).then(response => {
                        this.load = false;
                        this.notes.push(response.data);
                        this.makeToast('success', 'La nota se creo correctamente');
                        this.mostrarCrearNota = false;
                        this.listadoNotas = true;
                    })
                    .catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar');
                    });
                }
                else{
                    this.state = false;
                    this.load = false;
                    this.makeToast('warning', 'Campo obligatorio, mayor a 5 caracteres');
                }
            },
            actualizarNota(){
                this.load = true;
                if(this.cliente.length > 4){
                    this.state = null;
                    this.nota.cliente = this.cliente;
                    this.nota.nuevos = this.nuevos;
                    this.nota.eliminados = this.eliminados;
                    axios.post('/actualizar_nota', this.nota).then(response => {
                        this.load = false;
                        this.makeToast('success', 'La nota se actualizo correctamente');
                        this.notes[this.posicion].total_salida = response.data.total_salida;
                        this.notes[this.posicion].total_pagar = response.data.total_pagar;
                        this.mostrarCrearNota = false;
                        this.listadoNotas = true;
                    })
                    .catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar');
                    });
                }
                else{
                    this.state = false;
                    this.load = false;
                    this.makeToast('warning', 'Campo obligatorio, mayor a 5 caracteres');
                }
            },
            detallesNota(nota){
                this.nota = {};
                this.total_unidades = 0;
                axios.get('/detalles_nota', {params: {note_id: nota.id}}).then(response => {
                    this.nota.id = nota.id;
                    this.nota.cliente = nota.cliente;
                    this.nota.total_salida = nota.total_salida;
                    this.nota.registers = response.data;
                    this.nota.registers.forEach(register => {
                        this.total_unidades += register.unidades;
                    });
                    this.listadoNotas = false;
                    this.mostrarDetalles = true;
                });
            },
            registrarPago(nota, i){
                this.nota = {};
                this.posicion = i;
                axios.get('/detalles_nota', {params: {note_id: nota.id}}).then(response => {
                    this.nota.id = nota.id;
                    this.nota.cliente = nota.cliente;
                    this.nota.total_salida = nota.total_salida;
                    this.nota.registers = response.data;
                    this.listadoNotas = false;
                    this.mostrarNewPago = true;
                });
            },
            registrarDevolucion(nota, i){
                this.nota = {};
                this.posicion = i;
                axios.get('/detalles_nota', {params: {note_id: nota.id}}).then(response => {
                    this.nota.id = nota.id;
                    this.nota.cliente = nota.cliente;
                    this.nota.total_salida = nota.total_salida;
                    this.nota.registers = response.data;
                    this.listadoNotas = false;
                    this.mostrarDevolucion = true;
                });
            },
            verificarUnidades(unidades, pendiente, costo_unitario, i){
                if(unidades > pendiente){
                    this.makeToast('warning', 'Las unidades son mayor a lo pendiente');
                    this.nota.registers[i].unidades_base = 0;
                    this.nota.registers[i].total_base = 0;
                }
                if(unidades <= pendiente){
                    this.total_vendido = 0;
                    this.nota.registers[i].total_base = unidades * costo_unitario;
                    this.btnGuardar = true;
                    this.nota.registers.forEach(register => {
                        this.total_vendido += register.total_base;
                    });
                }
            },
            guardarPagosNota(){
                axios.post('/guardar_pago', this.nota).then(response => {
                    this.notes[this.posicion] = response.data;
                    this.makeToast('success', 'El pago se guardo correctamente');
                    this.mostrarNewPago = false;
                    this.listadoNotas = true;
                })
                .catch(error => {
                    this.makeToast('danger', 'Ocurrio un error, vuelve a intentarlo');
                });
            },
            guardarDevolucion(){
                axios.post('/guardar_devolucion', this.nota).then(response => {
                    this.notes[this.posicion] = response.data;
                    this.makeToast('success', 'La devolución se guardo correctamente');
                    this.mostrarDevolucion = false;
                    this.listadoNotas = true;
                })
                .catch(error => {
                    this.makeToast('danger', 'Ocurrio un error, vuelve a intentarlo');
                });
            },
            editarNota(nota, i){
                this.editar = true;
                this.cliente = '';
                this.registers = [];
                this.nota = {};
                this.posicion = i;
                this.nuevos = [];
                this.eliminados = [];
                axios.get('/detalles_nota', {params: {note_id: nota.id}}).then(response => {
                    this.nota.id = nota.id;
                    this.cliente = nota.cliente;
                    this.nota.total_salida = nota.total_salida;
                    this.registers = response.data;
                    this.listadoNotas = false;
                    this.mostrarCrearNota = true;
                    this.viewForm = true;
                });
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
