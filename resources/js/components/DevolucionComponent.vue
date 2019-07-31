<template>
    <div>
        <b-alert v-if="remisiones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="remisiones.length > 0 && !mostrarDevolucion && !mostrarDetalles" :items="remisiones" :fields="fields">
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="pagos" slot-scope="row">${{ row.item.pagos }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar }}</template>
            <template slot="detalles" slot-scope="row">
                <b-button v-if="row.item.total_devolucion != 0" variant="info" @click="func_detalles(row.item)">Detalles</b-button>
            </template>
            <template slot="registrar_devolucion" slot-scope="row">
                <b-button v-if="row.item.estado != 'Terminado'" variant="primary" @click="registrarDevolucion(row.item, row.index)">Registrar devolución</b-button>
            </template>
        </b-table>
        <div v-if="mostrarDevolucion">
            <div class="row">
                <div class="col-md-6">
                    <h4>Remisión n. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </div>
                <div class="col-md-2">
                    <b-button 
                        variant="success" 
                        @click="guardar" 
                        v-if="btnGuardar">
                        <i class="fa fa-check"></i> Concluir
                    </b-button>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-3 text-right">
                    <b-button variant="outline-secondary" @click="mostrarDevolucion = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </div>
            </div>
            <hr>
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
        </div>
        <div v-if="mostrarDetalles">
            <div class="row">
                <div class="col-md-9">
                    <h4>Remisión n. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>
                </div>
                <div class="col-md-3 text-right">
                    <b-button variant="outline-secondary" @click="mostrarDetalles = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </div>
            </div>
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Libro</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades devueltas</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.libro.ISBN }}</td>
                        <td>{{ devolucion.libro.titulo }}</td>
                        <td>$ {{ devolucion.dato.costo_unitario }}</td>
                        <td>{{ devolucion.unidades }}</td>
                        <td>$ {{ devolucion.total }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td>
                        <td><h5>$ {{ total_devolucion }}</h5></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                fields: [
                    {key: 'id', label: 'Remisión No.'}, 
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'pagos', label: 'Pagado'},
                    {key: 'total_pagar', label: 'Pagar'},
                    {key: 'detalles', label: ''},
                    {key: 'registrar_devolucion', label: ''},
                ],
                mostrarDetalles: false,
                btnGuardar: false, //Indica si se muestra el boton de guardar
                mostrarDevolucion: false,
                remision: {}, //Datos de la remision
                devoluciones: [], //Array de las devoluciones
                inputUnidades: false, //Indica si esta habilitado o no el de unidades
                disabled: false, //Indica si esta hablitidado o no el boton de registrar
                total_devolucion: 0,
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
            registrarDevolucion(remision, i){
                this.devoluciones = [];
                this.posicion = i;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.devoluciones = response.data.devoluciones;
                    this.remision = remision;
                    this.acumularFinal();
                    this.mostrarDevolucion = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            guardarUnidades(devolucion, i){
                if(devolucion.unidades > 0){
                    if(devolucion.unidades <= devolucion.unidades_resta){
                        this.devoluciones[i].total = devolucion.dato.costo_unitario * devolucion.unidades;
                        this.acumularFinal();
                        this.btnGuardar = true;
                    }
                    else{
                        this.item = devolucion.id;
                        this.makeToast('warning', 'Unidades mayor a unidades pendientes');
                    }
                }
                else{
                    this.makeToast('warning', 'Las unidades no pueden ser cero');
                }
            },
            guardar(){
                this.remision.devoluciones = this.devoluciones;
                axios.put('/concluir_remision', this.remision).then(response => {
                    this.remisiones[this.posicion].estado = response.data.estado;
                    this.remisiones[this.posicion].total_devolucion = response.data.total_devolucion;
                    this.remisiones[this.posicion].total_pagar = response.data.total_pagar;
                    this.mostrarDevolucion = false;
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
            func_detalles(remision){
                this.devoluciones = [];
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.devoluciones = response.data.devoluciones;
                    this.mostrarDetalles = true;
                    this.remision = remision;
                    this.acumularFinal();
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar');
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