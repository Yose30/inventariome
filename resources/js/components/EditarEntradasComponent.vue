<template>
    <div>
        <div v-if="listadoEntradas">
            <b-row>
                <b-col>
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-editorial">Editorial</label>
                        </b-col>
                        <b-col sm="9">
                            <b-form-select 
                                v-model="editorial" 
                                :options="options" 
                                @change="mostrarEditoriales">
                            </b-form-select>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col align="right">
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-fecha">De: </label><br>
                            <label for="input-fecha">A: </label>
                        </b-col>
                        <b-col sm="9">
                            <b-input type="date" v-model="fecha1"/>
                            <b-input type="date" v-model="fecha2" @change="porFecha"/>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="3" align="right">
                    <b-button variant="info" :disabled="loadRegisters" @click="getTodo">
                        <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table 
                v-if="!mostrarDetalles && !mostrarEA && entradas.length > 0" 
                :items="entradas" 
                :fields="fields"
                :tbody-tr-class="rowClass"
                :per-page="perPage"
                :current-page="currentPage"
                id="my-table">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template v-if="row.item.folio != '05'" slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template v-if="row.item.folio != '05'" slot="total_pagos" slot-scope="row">${{ row.item.total_pagos | formatNumber }}</template>
                <template v-if="row.item.folio != '05'" slot="total_pendiente" slot-scope="row">${{ row.item.total - row.item.total_pagos | formatNumber }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="detallesEntrada(row.item)">Detalles</b-button>
                </template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
                <template slot="editar" slot-scope="row">
                    <b-button 
                        @click="editarEntrada(row.item, row.index)"
                        v-if="row.item.total == 0 && role_id == 2 && row.item.folio != '05'"
                        variant="warning">Editar
                    </b-button>
                    <b-button 
                        v-b-modal.modal-registrarPago
                        @click="registrarPago(row.item, row.index)"
                        variant="primary" 
                        v-if="row.item.total > 0 && row.item.total_pagos < row.item.total && role_id == 2">
                        Registrar pago
                    </b-button>
                </template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="4"></th>
                        <th>${{ total | formatNumber }}</th>
                        <th>${{ total_pagos | formatNumber }}</th>
                        <th>${{ total_pendiente | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
            <b-pagination
                v-model="currentPage"
                :total-rows="entradas.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="entradas.length > 0">
            </b-pagination>
        </div> 
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col sm="1">
                    <label><b>Folio:</b></label><br>
                    <label><b>Editorial:</b></label>
                </b-col>
                <b-col sm="4">
                    <label>{{entrada.folio}}</label><br>
                    <label>{{entrada.editorial}}</label>
                </b-col>
                <b-col>
                    <b-button 
                        variant="primary"
                        :href="'/imprimirEntrada/' + entrada.id">
                        Descargar
                    </b-button>
                </b-col>
                <b-col>
                    <b-button 
                        variant="info" 
                        @click="mostrarPagos(entrada.id)"
                        v-if="entrada.total_pagos > 0 && role_id == 2">Mostrar pagos
                    </b-button>
                </b-col>
                <b-col align="right">
                    <b-button 
                        variant="secondary" 
                        @click="mostrarDetalles = false; listadoEntradas = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table v-if="registros.length > 0" :items="registros" :fields="fieldsR">
                <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                <template slot="unidades" slot-scope="row">{{ row.item.unidades | formatNumber }}</template>
                <template slot="thead-top" slot-scope="data">
                    <tr>
                        <th colspan="4"></th>
                        <th>{{ entrada.unidades | formatNumber }}</th>
                        <th>${{ entrada.total | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
        </div>
        <div v-if="mostrarEA">
            <div class="text-right">
                <b-button variant="secondary" @click="mostrarEA = false; listadoEntradas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
            </div>
            <hr>
            <b-row>
                <b-col sm="1">
                    <label>Folio</label><br>
                    <label>Editorial</label>
                </b-col>
                <b-col sm="5">
                    <label>{{entrada.folio}}</label><br>
                    <label>{{entrada.editorial}}</label>
                </b-col>
                <b-col sm="3" align="right">
                    <label><b>Unidades:</b> {{ total_unidades | formatNumber }}</label>
                </b-col>
                <b-col sm="3" class="text-right">
                    <b-button 
                        @click="actualizarCosto" 
                        variant="success"
                        :disabled="load"
                        v-if="!agregar">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar cambios' : 'Guardando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros" :fields="fieldsR">
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">
                    <b-input 
                        :id="`input-${row.index}`"
                        type="number" 
                        placeholder="Costo unitario"
                        @change="verificarUnidades(row.item.unidades, row.item.costo_unitario, row.index)" 
                        v-model="row.item.costo_unitario">
                    </b-input>
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>

                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="5">&nbsp;</th>
                        <th>${{ subtotal | formatNumber }}</th>
                    </tr>
                </template>

            </b-table>
        </div>

        <b-modal id="modal-registrarPago" title="Registrar pago">
            <b-form @submit.prevent="guardarVendidos">
                <b-row>
                    <b-col sm="2">
                        <label>Monto</label>
                    </b-col>
                    <b-col sm="5">
                        <b-form-input v-model="repayment.pago" autofocus :state="state" :disabled="load" type="number" required></b-form-input>
                    </b-col>
                    <b-col>
                        <b-button type="submit" variant="success" :disabled="load">
                            <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                        </b-button>
                    </b-col>
                </b-row>
            </b-form>
            <div slot="modal-footer"></div>
        </b-modal>
        
        <div v-if="pagosGuardados">
            <b-row>
                <b-col sm="1">
                    <label><b>Folio:</b></label><br>
                    <label><b>Editorial:</b></label>
                </b-col>
                <b-col sm="4">
                    <label>{{entrada.folio}}</label><br>
                    <label>{{entrada.editorial}}</label>
                </b-col>
                <b-col>
                    <label><b>Total:</b> ${{ entrada.total | formatNumber }}</label>
                </b-col>
                <b-col>
                    <label><b>Pagos:</b> ${{ entrada.total_pagos | formatNumber }}</label>
                </b-col>
                <b-col>
                    <label><b>Total pendiente:</b> ${{ entrada.total - entrada.total_pagos | formatNumber }}</label>
                </b-col>
                <b-col align="right">
                    <b-button 
                        variant="secondary" 
                        @click="pagosGuardados = false; mostrarDetalles = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="pagos" :fields="fieldsP">
                <template slot="index" slot-scope="row">
                    {{ row.index + 1 }}
                </template>
                <template slot="pago" slot-scope="row">
                    ${{ row.item.pago | formatNumber }}
                </template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                entradas: [],
                registros: [],
                editorial: '',
                fieldsP: [
                    {key: 'index', label: 'No.'},
                    'pago',
                    {key: 'created_at', label: 'Fecha de pago'},
                ],
                fields: [
                    {key: 'index', label: 'N.'}, 
                    'folio',
                    'editorial',
                    'unidades',
                    'total',
                    {key: 'total_pagos', label: 'Pagos'},
                    {key: 'total_pendiente', label: 'Total pendiente'},
                    {key: 'created_at', label: 'Fecha de creación'},
                    {key: 'detalles', label: ''},  
                    {key: 'editar', label: ''}
                ],
                fieldsR: [
                    {key: 'id', label: 'N.'}, 
                    {key: 'isbn', label: 'ISBN'}, 
                    {key: 'titulo', label: 'Libro'}, 
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                ],
                fieldsRP: [
                    {key: 'id', label: 'N.'}, 
                    {key: 'isbn', label: 'ISBN'}, 
                    {key: 'titulo', label: 'Libro'}, 
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    {key: 'unidades_pendientes', label: 'Unidades pendientes'},,
                    {key: 'unidades_base', label: 'Unidades'},
                    {key: 'total_base', label: 'Subtotal'},
                ],
                options: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'CAMBRIDGE', text: 'CAMBRIDGE' },
                    { value: 'CENGAGE', text: 'CENGAGE' },
                    { value: 'EMPRESER', text: 'EMPRESER' },
                    { value: 'EXPRESS PUBLISHING', text: 'EXPRESS PUBLISHING'},
                    { value: 'HELBLING LANGUAGES', text: 'HELBLING LANGUAGES'},
                    { value: 'MAJESTIC', text: 'MAJESTIC'},
                    { value: 'MC GRAW - MAJESTIC', text: 'MC GRAW - MAJESTIC'},
                    { value: 'MCGRAW HILL', text: 'MCGRAW HILL'},
                    { value: 'RICHMOND', text: 'RICHMOND'},
                    { value: 'IMPRESOS DE CALIDAD', text: 'IMPRESOS DE CALIDAD'},
                    { value: 'ENGLISH TEXBOOK', text: 'ENGLISH TEXBOOK'},
                    { value: 'BOOKMART MÉXICO', text: 'BOOKMART MÉXICO' },
                    { value: 'ANGLO PUBLISHING', text: 'ANGLO PUBLISHING' },
                    { value: 'LAROUSSE', text: 'LAROUSSE' },
                    { value: 'TODAS', text: 'MOSTRAR TODO'},
                ],
                mostrarDetalles: false,
                fechaFinal: '',
                entrada: {
                    id: 0,
                    unidades: 0,
                    total: 0,
                    total_pagos: 0,
                    total_pendiente: 0,
                    folio: '',
                    editorial: '',
                    items: [],
                    nuevos: []
                },
                total: 0,
                total_pagos: 0,
                total_pendiente: 0,
                repayment: {
                    entrada_id: 0,
                    pago: null
                },
                mostrarEA: false,
                isbn: '',
                inputISBN: true,
                temporal: {},
                queryTitulo: '',
                inputLibro: true,
                resultslibros: [],
                inputUnidades: false,
                unidades: 0,
                stateN: null,
                stateE: null,
                load: false,
                posicion: null,
                listadoEntradas: true,
                agregar: false,
                nuevos: [],
                estado: false,
                fecha1: '',
                fecha2: '',
                mostrarRegistrar: false,
                state: null,
                pagosGuardados: false,
                pagos: [],
                subtotal: 0,
                perPage: 15,
                currentPage: 1,
                loadRegisters: false
            }
        },
        // created: function(){
        //     this.getTodo();
        // },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            },
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        }, 
        methods: {
            rowClass(item, type) {
                if (!item) return
                if (item.total_pagos > 0 && item.total_pagos == item.total) return 'table-success'
                if (item.total == 0) return 'table-warning'
            },
            getTodo(){
                var ffinal = moment();
                this.fechaFinal = ffinal;
                this.loadRegisters = true;
                axios.get('/all_entradas').then(response => {
                    this.entradas = response.data;
                    this.loadRegisters = false;
                    this.acumular();
                }).catch(error => {
                    this.loadRegisters = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            detallesEntrada(entrada){
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.asignar(response);
                    this.mostrarDetalles = true;
                });
            },
            editarEntrada(entrada, i){
                this.posicion = i;
                this.agregar = false;
                this.stateN = null;
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.asignar(response);
                    this.total_unidades = this.entrada.unidades;
                    this.mostrarEA = true;
                });
            },
            asignar(response){
                this.entrada = {
                    id: 0,
                    unidades: 0,
                    total: 0,
                    total_pagos: 0,
                    total_pendiente: 0,
                    folio: '',
                    editorial: '',
                    items: [],
                    nuevos: []
                };
                this.entrada.id = response.data.entrada.id;
                this.entrada.folio = response.data.entrada.folio;
                this.entrada.editorial = response.data.entrada.editorial;
                this.entrada.total = response.data.entrada.total;
                this.entrada.total_pagos = response.data.entrada.total_pagos;
                this.entrada.total_pendiente = this.entrada.total - this.entrada.total_pagos;
                this.entrada.unidades = response.data.entrada.unidades;
                this.registros = response.data.registros;
                this.listadoEntradas = false;
            },
            restasUnidades(item, i){
                this.registros.splice(i, 1);
                this.total_unidades = this.total_unidades - item.unidades;
                this.entrada.unidades = this.total_unidades;
            },
            mostrarEditoriales(){
                if(this.editorial.length > 0){
                    axios.get('/mostrarEditoriales', {params: {editorial: this.editorial}}).then(response => {
                        this.entradas = [];
                        this.entradas = response.data;
                        this.acumular();
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.getTodo();
                } 
            },
            acumular(){
                this.total = 0;
                this.total_pagos = 0;
                this.total_pendiente = 0;
                this.entradas.forEach(entrada => {
                    this.total += entrada.total;
                    this.total_pagos += entrada.total_pagos;
                    this.total_pendiente += entrada.total - entrada.total_pagos;
                });
            },
            verificarUnidades(unidades, costo_unitario, i){
                if(costo_unitario > 0){
                    this.registros[i].total = unidades * costo_unitario;
                    // SUMAR TODO LO QUE SE VAYA EDITANDO DE LA ENTRADA
                    this.sumatoriaSubtotal();
                    if(i + 1 < this.registros.length){
                        document.getElementById('input-'+(i+1)).focus();
                        document.getElementById('input-'+(i+1)).select();
                    }
                }
                else{
                    this.makeToast('warning', 'Costo unitario invalido');
                    this.registros[i].costo_unitario = 0;
                    this.registros[i].total = 0;
                    // SUMAR TODO LO QUE SE VAYA EDITANDO DE LA ENTRADA
                    this.sumatoriaSubtotal();
                }
            },
            sumatoriaSubtotal(){
                this.subtotal = 0;
                this.registros.forEach(registro => {
                    this.subtotal += registro.total;
                });
            },
            porFecha(){
                axios.get('/fecha_entradas', {params: {fecha1: this.fecha1, fecha2: this.fecha2}}).then(response => {
                    this.entradas = response.data;
                    this.acumular();
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            actualizarCosto(){
                this.estado = false;
                this.registros.forEach(registro => {
                    if(registro.costo_unitario == 0){
                        this.estado = true;
                    }
                });
                if(this.estado == true){
                    this.makeToast('warning', 'El costo unitario no puede ser 0');
                }
                else{
                    this.load = true;
                    this.entrada.items = this.registros;
                    axios.put('/actualizar_costos', this.entrada).then(response => {
                        this.makeToast('success', 'La entrada se ha actualizado');
                        this.entradas[this.posicion].total = response.data.total;
                        this.acumular();
                        this.load = false;
                        this.mostrarEA = false;
                        this.listadoEntradas = true;
                    }).catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
            },
            obtenerSubtotal(registro, i){
                if(registro.unidades_base > (registro.unidades - registro.unidades_vendido)){
                    this.makeToast('warning', 'Las unidades son mayor a las unidades pendientes');
                    this.registros[i].unidades_base = 0;
                    this.registros[i].total_base = 0;
                }
                else{
                    this.registros[i].total_base = registro.unidades_base * registro.costo_unitario;
                }
            },
            registrarPago(entrada, i){
                this.posicion = i;
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {
                    this.asignar(response);
                    this.listadoEntradas = true;
                    this.repayment.entrada_id = entrada.id;
                });
            },
            guardarVendidos(){
                if(this.repayment.pago > 0){
                    if(this.repayment.pago <= (this.entrada.total - this.entrada.total_pagos)){
                        this.state = null;
                        this.load = true;
                        axios.put('/pago_entrada', this.repayment).then(response => {
                            this.makeToast('success', 'El pago se guardo correctamente');
                            this.load = false;
                            this.repayment = {
                                entrada_id: 0,
                                pago: null
                            };
                            this.$bvModal.hide('modal-registrarPago');
                            this.entradas[this.posicion].total_pagos = response.data.total_pagos;
                            this.acumular();

                        }).catch(error => {
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
            mostrarPagos(entrada_id){
                axios.get('/detalles_entrada', {params: {entrada_id: entrada_id}}).then(response => {
                    this.asignar(response);
                    this.pagos = response.data.entrada.repayments;
                    this.mostrarDetalles = false;
                    this.pagosGuardados = true;
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