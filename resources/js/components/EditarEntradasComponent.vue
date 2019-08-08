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
                            <b-form-select v-model="editorial" :options="options" @change="mostrarEditoriales"></b-form-select>
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
                <!-- <b-col align="right">
                    <label><b>Total:</b> ${{ entrada.total }}</label>
                </b-col> -->
            </b-row>
            <hr>
            <b-table v-if="!mostrarDetalles && !mostrarEA && entradas.length > 0" :items="entradas" :fields="fields">
                <template slot="total" slot-scope="row">${{ row.item.total }}</template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="detallesEntrada(row.item)">Detalles</b-button>
                </template>
                <template slot="descargar" slot-scope="row">
                    <b-button 
                        variant="primary"
                        :href="'/imprimirEntrada/' + row.item.id">
                        Descargar
                    </b-button>
                </template>
                <template slot="created_at" slot-scope="row">
                    {{ row.item.created_at | moment }}
                </template>
                <template slot="editar" slot-scope="row">
                    <b-button 
                        @click="editarEntrada(row.item, row.index)"
                        v-if="row.item.total == 0 && role_id == 2"
                        variant="warning">
                        <i class="fa fa-pencil"></i> Editar
                    </b-button>
                    <!-- <b-button 
                        @click="registrarPago(row.item, row.index)"
                        variant="primary" 
                        v-if="row.item.total > 0 && role_id == 2">
                        Registrar pago
                    </b-button> -->
                </template>
            </b-table>
        </div>
        <div v-if="mostrarRegistrar">
            <div class="text-right">
                <b-button variant="secondary" @click="mostrarRegistrar = false; listadoEntradas = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
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
                    <label><b>Unidades:</b> {{ total_unidades }}</label>
                </b-col>
                <b-col sm="3" class="text-right">
                    <b-button 
                        @click="guardarVendidos" 
                        variant="success"
                        :disabled="load">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }}
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros" :fields="fieldsRP">
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">{{ row.item.costo_unitario }}</template>
                <template slot="unidades_pendientes" slot-scope="row">
                    {{ row.item.unidades - row.item.unidades_vendido }}
                </template>
                <template slot="unidades_base" slot-scope="row">
                    <b-input 
                        type="number" 
                        @change="obtenerSubtotal(row.item, row.index)"
                        v-model="row.item.unidades_base">
                    </b-input>
                </template>
                <template slot="total_base" slot-scope="row">${{ row.item.total_base }}</template>
            </b-table>
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
                    <label><b>Unidades:</b> {{ entrada.unidades }}</label>
                </b-col>
                <b-col>
                    <label><b>Total:</b> ${{ entrada.total }}</label>
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
                <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                <template slot="total" slot-scope="row">${{ row.item.total }}</template>
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
                    <label><b>Unidades:</b> {{ total_unidades }}</label>
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
                        type="number" 
                        placeholder="Costo unitario"
                        @change="verificarUnidades(row.item.unidades, row.item.costo_unitario, row.index)" 
                        v-model="row.item.costo_unitario">
                    </b-input>
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total }}</template>
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
                fields: [
                    {key: 'id', label: 'N.'}, 
                    'folio',
                    'editorial',
                    'unidades',
                    'total',
                    {key: 'created_at', label: 'Fecha de creación'},
                    {key: 'detalles', label: ''},  
                    {key: 'descargar', label: ''}, 
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
                ],
                mostrarDetalles: false,
                fechaFinal: '',
                entrada: {
                    id: 0,
                    unidades: 0,
                    total: 0,
                    folio: '',
                    editorial: '',
                    items: [],
                    nuevos: []
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
            nuevaEntrada(){
                axios.get('/nueva_entrada').then(response => {
                    this.listadoEntradas = false;
                    this.agregar = true;
                    this.mostrarEA = true;
                    this.entrada = {
                        id: 0,
                        unidades: 0,
                        total: 0,
                        folio: '',
                        editorial: '',
                        items: [],
                        nuevos: []
                    };
                    this.registros = [];
                    this.eliminarTemporal();
                    this.total_unidades = 0;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            getTodo(){
                var ffinal = moment();
                this.fechaFinal = ffinal;
                axios.get('/all_entradas').then(response => {
                    this.entradas = response.data;
                    this.acumular();
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
                    this.eliminarTemporal();
                    this.asignar(response);
                    this.entrada.id = response.data.entrada.id;
                    this.total_unidades = this.entrada.unidades;
                    this.mostrarEA = true;
                });
            },
            asignar(response){
                this.entrada = {
                    id: 0,
                    unidades: 0,
                    total: 0,
                    folio: '',
                    editorial: '',
                    items: [],
                    nuevos: []
                };
                this.entrada.folio = response.data.entrada.folio;
                this.entrada.editorial = response.data.entrada.editorial;
                this.entrada.total = response.data.entrada.total;
                this.entrada.unidades = response.data.entrada.unidades;
                this.registros = response.data.registros;
                this.listadoEntradas = false;
            },
            //Eliminar registro de la entrada
            eliminarRegistro(item, i){
                if(this.agregar == false){
                    axios.delete('/eliminar_registro_entrada', {params: {id: item.id}}).then(response => {
                        this.restasUnidades(item, i);
                    });
                } 
                else{
                    this.restasUnidades(item, i);
                }
                
            },
            restasUnidades(item, i){
                this.registros.splice(i, 1);
                this.total_unidades = this.total_unidades - item.unidades;
                this.entrada.unidades = this.total_unidades;
            },
            //Buscar libro por ISBN
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
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.ini_1();
                this.temporal = {
                    id: libro.id,
                    libro: {
                        ISBN: libro.ISBN,
                        titulo: libro.titulo,
                    },
                    unidades: 0,
                };
            },
            ini_1(){
                this.inputLibro = false;
                this.inputISBN = false;
                this.inputUnidades = true;
                this.resultslibros = [];
            },
            //Guardar un registro de la entrada
            guardarRegistro(){
                if(this.unidades > 0){
                    this.temporal.unidades = this.unidades;
                    if(this.agregar == false){
                        this.nuevos.push(this.temporal);
                    }
                    this.registros.push(this.temporal);
                    this.total_unidades += parseInt(this.temporal.unidades);
                    this.unidades = 0;
                    this.temporal = {};
                    this.isbn = '';
                    this.queryTitulo = '',
                    this.inputISBN = true;
                    this.inputLibro = true;
                    this.inputUnidades = false;
                }
                else{
                    this.makeToast('danger', 'Unidades no validas');
                }
            },
            actRemision(){
                this.entrada.unidades = this.total_unidades;
                this.entrada.nuevos = this.nuevos;
                this.load = true;
                this.stateE = null;
                axios.put('/actualizar_entrada', this.entrada).then(response => {
                    this.makeToast('success', 'La entrada se ha actualizado');
                    this.load = false;
                    this.entradas[this.posicion] = response.data;
                    this.mostrarEA = false;
                    this.listadoEntradas = true;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            eliminarTemporal(){
                this.temporal = {};
                this.inputISBN = true;
                this.inputLibro = true;
                this.inputUnidades = false;
                this.queryTitulo = '';
                this.unidades = 0;
                this.isbn = '';
            },
            guardarNum(){
                if(this.entrada.folio.length > 0){
                    axios.get('/buscarFolio', {params: {folio: this.entrada.folio}}).then(response => {
                        if(response.data.id != undefined){
                            this.stateN = false;
                            this.makeToast('danger', 'El folio ya existe');
                        }
                        else{
                            this.stateN = null;
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
                else{
                    this.stateN = false;
                    this.makeToast('danger', 'Definir folio');
                }
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
                this.entrada.unidades = 0;
                this.entrada.total = 0;
                this.entradas.forEach(entrada => {
                    this.entrada.unidades += entrada.unidades;
                    this.entrada.total += entrada.total;
                });
            },
            verificarUnidades(unidades, costo_unitario, i){
                this.registros[i].total = unidades * costo_unitario;
            },
            porFecha(){
                axios.get('/fecha_entradas', {params: {fecha1: this.fecha1, fecha2: this.fecha2}}).then(response => {
                    this.entradas = response.data;
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
                        this.load = false;
                        this.mostrarEA = false;
                        this.listadoEntradas = true;
                    }).catch(error => {
                        this.load = false;
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
            },
            registrarPago(entrada, i){
                this.posicion = i;
                axios.get('/detalles_entrada', {params: {entrada_id: entrada.id}}).then(response => {

                    this.asignar(response);
                    this.total_unidades = this.entrada.unidades;
                    this.mostrarRegistrar = true;
                });
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
            guardarVendidos(){
                this.load = true;
                this.entrada.items = this.registros;
                axios.put('/pago_entrada', this.entrada).then(response => {
                    this.makeToast('success', '');
                    // this.entradas[this.posicion].total = response.data.total;
                    this.load = false;
                    console.log(response.data);
                    // this.mostrarEA = false;
                    // this.listadoEntradas = true; 
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
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