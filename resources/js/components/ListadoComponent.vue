<template>
    <div>
        <h4>Remisiones</h4>
        <div class="row">
            <div class="col-md-3">
                <b-row class="my-1">
                    <b-col sm="5">
                        <label for="input-numero">Remision</label>
                    </b-col>
                    <b-col sm="7">
                        <b-form-input 
                            id="input-numero" 
                            type="number" 
                            v-model="num_remision" 
                            @keyup.enter="porNumero">
                        </b-form-input>
                        <div class="text-danger">{{ respuesta_numero }}</div>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-5">
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-cliente">Cliente</label>
                    </b-col>
                    <b-col sm="9">
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
                                @click="porCliente(result)">
                                {{ result.name }}
                            </a>
                        </div>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-4">
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-inicio">Inicio</label>
                    </b-col>
                    <b-col sm="9">
                        <input 
                            class="form-control" 
                            type="date" 
                            v-model="inicio">
                        </input>
                        <div class="text-danger">{{ respuesta_fecha }}</div>
                    </b-col>
                </b-row>
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-final">Final</label>
                    </b-col>
                    <b-col sm="9">
                        <input 
                            class="form-control" 
                            type="date" 
                            v-model="final"
                            @change="porFecha">
                        </input>
                    </b-col>
                </b-row>
            </div>
        </div>
        <div>
            <table class="table" v-if="tabla_numero">
                <thead>
                    <tr>
                        <th scope="col">Numero</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Total Salida</th>
                        <th scope="col">Total Devolución</th>
                        <th scope="col">Total a pagar</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha de entrega</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ remision.id }}</td>
                        <td>{{ cliente_nombre }}</td>
                        <td>$ {{ remision.total }}</td>
                        <td>$ {{ remision.total_devolucion }}</td>
                        <td>$ {{ remision.total_pagar }}</td>
                        <td>
                            <b-badge variant="secondary" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                            <b-badge variant="primary" v-if="remision.estado == 'Proceso'">{{ remision.estado }}</b-badge>
                            <b-badge variant="success" v-if="remision.estado == 'Terminado'">{{ remision.estado }}</b-badge>
                        </td>
                        <td>{{ remision.fecha_entrega }}</td>
                    </tr>
                </tbody>
            </table>
            <table class="table" v-if="tabla_gral && remisiones.length">
                <thead>
                    <tr>
                        <th scope="col">Numero</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Total Salida</th>
                        <th scope="col">Total Devolución</th>
                        <th scope="col">Total a pagar</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha de entrega</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(remision, i) in remisiones" v-bind:key="i">
                        <td>{{ remision.id }}</td>
                        <td>{{ remision.cliente.name }}</td>
                        <td>$ {{ remision.total }}</td>
                        <td>$ {{ remision.total_devolucion }}</td>
                        <td>$ {{ remision.total_pagar }}</td>
                        <td>
                            <b-badge variant="secondary" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                            <b-badge variant="primary" v-if="remision.estado == 'Proceso'">{{ remision.estado }}</b-badge>
                            <b-badge variant="success" v-if="remision.estado == 'Terminado'">{{ remision.estado }}</b-badge>
                        </td>
                        <td>{{ remision.fecha_entrega }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td><b>$ {{ total_salida }}</b></td>
                        <td><b>$ {{ total_devolucion }}</b></td>
                        <td><b>$ {{ total_pagar }}</b></td>
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
                num_remision: 0,
                inicio: '',
                final: '',
                respuesta_numero: '',
                respuesta_fecha: '',
                remisiones: [],
                remision: {},
                cliente_nombre: '',
                tabla_numero: false,
                queryCliente: '',
                resultsClientes: [],
                //tabla_cliente: false,
                total_salida: 0,
                total_devolucion: 0,
                total_pagar: 0,
                tabla_gral: true,
                currentTime: null,
                cliente_id: 0,

            }
        },
        methods: {
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        console.log(response.data);
                        this.remision = response.data.remision;
                        this.cliente_nombre = response.data.cliente_nombre;
                        this.tabla_gral = false;
                        this.tabla_numero = true;
                    }).catch(error => {
                        console.log(error.response);
                        this.respuesta_numero = 'No existe';
                    });
                }
            },
            //Se repite en remision
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    axios.get('/todos_los_clientes').then(response => {
                        this.valores(response);
                    });
                }
            },
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id, inicio: this.inicio, final: this.final}}).then(response => {
                    console.log(response);
                    this.cliente_id = result.id;
                    this.queryCliente = '';
                    this.resultsClientes = [];

                    this.valores(response);

                });
            },
            porFecha(){
                axios.get('/buscar_por_fecha', {params: {inicio: this.inicio, final: this.final, cliente_id: this.cliente_id}}).then(response => {
                    console.log(response);
                    this.valores(response);
                });
            },
            acumular(){
                this.total_salida = 0;
                this.total_devolucion = 0;
                this.total_pagar = 0;

                this.remisiones.forEach(remision => {
                    this.total_salida += remision.total;
                    this.total_devolucion += remision.total_devolucion;
                    this.total_pagar += remision.total_pagar;
                });
            },
            valores(response){
                this.remisiones = [];
                this.remisiones = response.data;
                this.tabla_numero = false;
                this.tabla_gral = true;
                this.acumular();
            }
        }
    }
</script>