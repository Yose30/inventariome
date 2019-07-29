<template>
    <div>
        <b-alert v-if="remisiones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="remisiones.length > 0 && !mostrarDevolucion" :items="remisiones" :fields="fields">
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="estado" slot-scope="row">
                <b-badge variant="primary" v-if="row.item.estado == 'Proceso'">Entregado</b-badge>
                <b-badge variant="success" v-if="row.item.estado == 'Terminado'">{{ row.item.estado }}</b-badge>
            </template>
            <template slot="registrar_devolucion" slot-scope="row">
                <b-button v-if="row.item.estado != 'Terminado'" variant="outline-primary" @click="registrarDevolucion(row.item, row.index)">Registrar devolución</b-button>
            </template>
        </b-table>

        <!-- <h4>Registrar devolución</h4>
        <hr> -->
        <div v-if="mostrarDevolucion">
        <div class="row">
            <div class="col-md-6">
                <h4>Remisión n. {{ remision.id }}</h4>
                <label>Cliente: {{ remision.cliente.name }}</label>
                <!-- <div class="row">
                    <label class="col-md-4">Numero de remisión</label>
                    <div class="col-md-4">
                        <b-input 
                            type="number" 
                            v-model="numero" 
                            @keyup.enter="listaRemisiones"
                            :disabled="inputNRemision"
                        ></b-input>
                        <div class="text-danger">{{ respuesta }}</div>
                    </div>
                </div> -->
            </div>
            <div class="col-md-2">
                <b-button 
                    variant="success" 
                    @click="guardar" 
                    v-if="btnGuardar">
                    <i class="fa fa-check"></i> Concluir
                </b-button>
                <!-- <b-badge variant="secondary" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                <b-badge variant="primary" v-if="remision.estado == 'Proceso'">Entregado</b-badge>
                <b-badge variant="success" v-if="remision.estado == 'Terminado'">{{ remision.estado }}</b-badge> -->
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-3 text-right">
                <b-button variant="outline-secondary" @click="mostrarDevolucion = false">
                    <i class="fa fa-mail-reply"></i> Regresar
                </b-button>
                <!-- <a 
                    class="btn btn-info"
                    v-if="btnImprimir"
                    :href="'/imprimirSalida/' + remision.id">
                    <i class="fa fa-print"></i>
                </a> -->
            </div>
        </div>
        <hr>
        <!-- v-if="mostrarDatos"  -->
        <!-- <b-button 
            variant="primary" 
            v-if="mostrarDatos && remision.estado != 'Iniciado'" 
            :disabled="disabled" 
            v-on:click="registrarDevolucion">{{ txtBoton }}
        </b-button> -->
        <!-- <hr> -->
        <!-- <div class="row" v-if="mostrarDatos">
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
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(registro, i) in registros" v-bind:key="i">
                        <td>{{ registro.libro.ISBN }}</td>
                        <td>{{ registro.libro.titulo }}</td>
                        <td>$ {{ registro.costo_unitario }}</td>
                        <td>{{ registro.unidades }}</td>
                        <td>$ {{ registro.total }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td>
                        <td><h5>$ {{ total_salida }}</h5></td>
                    </tr>
                </tbody>
            </table>
        </b-collapse> -->
        <!-- <div v-if="mostrarColumnas" class="row">
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
        <b-collapse id="collapse-2" v-model="mostrarDevolucion" class="mt-2"> -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades pendientes</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.libro.ISBN }}</td>
                        <td>{{ devolucion.libro.titulo }}</td>
                        <td>$ {{ devolucion.dato.costo_unitario }}</td>
                        <td>{{ devolucion.unidades_resta }}</td>
                        <td>
                            <input 
                            type="number" 
                            v-model="devolucion.unidades"
                            min="1"
                            max="9999"
                            :disabled="inputUnidades"
                            @keyup.enter="guardarUnidades(devolucion, i)"/>
                            <div class="text-danger" v-if="item == devolucion.id">{{ respuestaUnidades }}</div>
                        </td>
                        <td>$ {{ devolucion.total }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td><td></td>
                        <td><h5>$ {{ total_devolucion }}</h5></td>
                    </tr>
                </tbody>
            </table>
        <!-- </b-collapse> -->
        <hr>
        <!-- <div v-if="mostrarColumnas" class="row">
            <h4 class="col-md-10">Remisión final</h4>
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
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.libro.ISBN }}</td>
                        <td>{{ devolucion.libro.titulo }}</td>
                        <td>$ {{ devolucion.dato.costo_unitario }}</td>
                        <td>{{ devolucion.unidades_resta }}</td>
                        <td>$ {{ devolucion.total_resta }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td><td></td><td></td>
                        <td><h5>$ {{ total_pagar }}</h5></td>
                    </tr>
                </tbody>
            </table>
        </b-collapse> -->
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                fields: [
                    {key: 'id', label: 'N.'}, 
                    'estado',
                    'cliente', 
                    {key: 'total_pagar', label: 'Total'}, 
                    {key: 'total_devolucion', label: 'Total devolución'},
                    {key: 'registrar_devolucion', label: ''},
                ],

                numero: 0, //Variable del input
                inputNRemision: false, //Indica si esta habilitado o no
                respuesta: '', //Indica si es correcto o no el numero de remision
                respuestaUnidades: '',
                btnGuardar: false, //Indica si se muestra el boton de guardar
                btnImprimir: false, //ndica si se muestra el boton de imprimir
                mostrarDatos: false, //Boton para registrar la devolucion
                txtBoton: '', //Mensaje del boton de registrar devolucion
                mostrarSalida: false,
                mostrarColumnas: false,
                mostrarDevolucion: false,
                mostrarFinal: false, //Indica si se muestra el collspan
                registros: [], //Array de registros de salida
                remision: {}, //Datos de la remision
                devoluciones: [], //Array de las devoluciones
                inputUnidades: false, //Indica si esta habilitado o no el de unidades
                disabled: false, //Indica si esta hablitidado o no el boton de registrar
                btnImprimir: false,
                item: 0,
                total_salida: 0,
                total_devolucion: 0,
                total_pagar: 0,
                remisiones: [],
                posicion: 0,
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/all_devoluciones').then(response => {
                    this.remisiones = response.data;
                });
            },
            listaRemisiones(){
                if(this.numero > 0){
                    this.respuesta = '';
                    axios.get('/lista_datos', {params: {numero: this.numero}}).then(response => {
                        this.mostrarDatos = true;
                        this.mostrarSalida = true;
                        this.mostrarColumnas = false;
                        this.mostrarDevolucion = false;
                        this.mostrarFinal = false;
                        this.disabled = false;
                        this.btnGuardar = false;
                        this.btnImprimir = false;
                        this.inputUnidades = false;
                        
                        this.remision = {};
                        this.registros = [];
                        this.devoluciones = [];

                        this.remision = response.data.remision;
                        this.registros = response.data.datos;
                        this.acumularSalida();
                
                        if(this.remision.estado == 'Iniciado'){
                            this.txtBoton = 'Registrar devolución';
                        }
                        if(this.remision.estado == 'Proceso'){
                            this.txtBoton = 'Registrar devolución';
                        }
                        if(this.remision.estado == 'Terminado'){
                            this.txtBoton = 'Devolución registrada';
                            this.mostrarDatos = true;
                            this.btnImprimir = true;
                        }
                    }).catch(error => {
                        this.respuesta = 'No existe';
                    });
                }
                else{
                    this.respuesta = 'No valido';
                }
            },
            registrarDevolucion(remision, i){
                this.devoluciones = [];
                this.posicion = i;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.devoluciones = response.data.devoluciones;
                    this.mostrarDevolucion = true;
                    this.remision = remision;
                    this.acumularFinal();
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar');
                });
                // axios.get('/devoluciones_remision', {params: {remision_id: this.remision.id}}).then(response => {
                //     this.devoluciones = response.data.devoluciones;
                //     this.disabled = true;
                //     this.mostrarSalida = false;
                //     this.mostrarColumnas = true;
                //     this.mostrarDevolucion = true;
                //     this.mostrarFinal = true;

                //     this.remision.estado = response.data.remision.estado;
                //     this.remision.total_devolucion = response.data.remision.total_devolucion;
                //     this.remision.total_pagar = response.data.remision.total_pagar;
                //     this.acumularFinal();

                //     if(this.remision.estado != 'Terminado'){
                //         this.btnGuardar = true;
                //     }
                //     if(this.remision.estado == 'Terminado'){
                //         this.inputUnidades = true;
                //     }
                // }).catch(error => {
                //     console.log(error.response);
                // });
                
            },
            guardarUnidades(devolucion, i){
                if(devolucion.unidades > 0){
                    if(devolucion.unidades <= devolucion.unidades_resta){
                        // axios.put('/actualizar_unidades', devolucion).then(response => {
                        //     console.log(response);
                        //     this.devoluciones.splice(i, 1, response.data.devolucion);
                        //     this.remision.total_devolucion = response.data.total_devolucion;
                        //     this.remision.total_pagar = response.data.total_pagar;
                        //     this.acumularFinal();
                        // }).catch(error => {
                        //     console.log(error.response);
                        // });
                        this.devoluciones[i].total = devolucion.dato.costo_unitario * devolucion.unidades;
                        
                        // this.remision.total_devolucion = response.data.total_devolucion;
                        // this.remision.total_pagar = response.data.total_pagar;
                        this.acumularFinal();
                        this.btnGuardar = true;
                    }
                    else{
                        this.item = devolucion.id;
                        this.makeToast('danger', 'Unidades mayor a unidades pendientes');
                    }
                }
                else{
                    this.makeToast('danger', 'Las unidades no pueden ser cero');
                }
            },
            guardar(){
                this.remision.devoluciones = this.devoluciones;
                axios.put('/concluir_remision', this.remision).then(response => {
                    this.remisiones[this.posicion].estado = response.data.estado;
                    this.remisiones[this.posicion].total_devolucion = response.data.total_devolucion;
                    this.mostrarDevolucion = false;
                    console.log(response.data);
                }).catch(error => {
                    console.log(error.response);
                });
            },
            acumularSalida(){
                this.total_salida = 0;
                this.registros.forEach(registro => {
                    this.total_salida += registro.total;
                });
                
            },
            acumularFinal(){
                this.total_devolucion = 0;
                this.total_pagar = 0;
                this.devoluciones.forEach(devolucion => {
                    this.total_devolucion += devolucion.total;
                    this.total_pagar += devolucion.total_resta;
                });
            },
            makeToast(variant = null, descripcion) {
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variant,
                    solid: true
                })
            }
        },
    }
</script>