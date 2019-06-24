<template>
    <div>
        <h4>Devolución</h4>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <div class="row">
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
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn btn-success" @click="guardar" v-if="btnGuardar"><i class="fa fa-check"></i> Concluir</button>
            </div>
        </div>
        <hr>
        <b-button variant="primary" v-if="mostrarDatos" :disabled="disabled" v-on:click="registrarDevolucion">{{ txtBoton }}</b-button>
        <hr>
        <div class="row" v-if="mostrarDatos">
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
            <h5 align="right">Total: $ {{ remision.total }}</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Titulo</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(registro, i) in registros" v-bind:key="i">
                        <td>{{ registro.isbn_libro }}</td>
                        <td>{{ registro.titulo }}</td>
                        <td>$ {{ registro.costo_unitario }}</td>
                        <td>{{ registro.unidades }}</td>
                        <td>$ {{ registro.total }}</td>
                    </tr>
                </tbody>
            </table>
        </b-collapse>
        <hr>
        
        <div v-if="mostrarColumnas" class="mt-2">
            <h4>Devolución</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Titulo</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.clave_libro }}</td>
                        <td>{{ devolucion.titulo }}</td>
                        <td>$ {{ devolucion.costo_unitario }}</td>
                        <td>
                            <input 
                            type="number" 
                            v-model="devolucion.unidades"
                            min="1"
                            max="9999"
                            :disabled="inputUnidades"
                            @keyup.enter="guardarUnidades(devolucion, i)"
                            ></input>
                        </td>
                        <td>$ {{ devolucion.total }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                        <td></td><td></td>
                        <td><h5>$ {{ remision.total_devolucion }}</h5></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="mostrarColumnas" class="mt-2">
            <h4>Remisión final</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ISBN</th>
                        <th scope="col">Titulo</th>
                        <th scope="col">Costo unitario</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                        <td>{{ devolucion.clave_libro }}</td>
                        <td>{{ devolucion.titulo }}</td>
                        <td>$ {{ devolucion.costo_unitario }}</td>
                        <td>{{ devolucion.unidades_resta }}</td>
                        <td>$ {{ devolucion.total_resta }}</td>
                    </tr>
                    <tr>
                        <td></td><td></td><td></td><td></td>
                        <td><h5>$ {{ remision.total_pagar }}</h5></td>
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
                numero: 0,
                registros: [], 
                remision: {},
                id_actualizar: -1,
                actualizar: false,
                respuesta: '',
                options: [
                    { value: 1, text: 'Iniciado' },
                    { value: 1, text: 'Proceso' },
                    { value: 1, text: 'Terminado' },
                ],
                mostrarDatos: false,
                mostrarSalida: false,
                mostrarColumnas: false,
                devoluciones: [],
                disabled: false,
                inputUnidades: false,
                btnGuardar: false,
                txtBoton: '',
                inputNRemision: false,
            }
        },
        methods: {
            listaRemisiones(){
                if(this.numero > 0){
                    axios.get('/lista_datos', {params: {numero: this.numero}}).then(response => {
                        this.mostrarDatos = true;
                        this.mostrarSalida = true;
                        this.devoluciones = [];
                        this.disabled = false;
                        this.mostrarColumnas = false;
                        this.btnGuardar = false;
                        this.remision = response.data.remision;
                        this.registros = response.data.datos;
                
                        if(this.remision.estado == 'Iniciado'){
                            this.txtBoton = 'Registrar devolución';
                        }
                        if(this.remision.estado == 'Proceso'){
                            this.txtBoton = 'Continuar registro';
                        }
                        if(this.remision.estado == 'Terminado'){
                            this.txtBoton = 'Devolución registrada';
                            this.mostrarDatos = true;
                        }
                    }).catch(error => {
                        this.respuesta = 'No existe';
                    });
                }
                else{
                    this.respuesta = 'No valido';
                }
            },
            registrarDevolucion(){
                axios.get('/devoluciones_remision', {params: {remision_id: this.remision.id}}).then(response => {
                    this.devoluciones = response.data.devoluciones;
                    this.mostrarSalida = false;
                    this.disabled = true;
                    this.mostrarColumnas = true;
                    this.total_devolucion = this.remision.total_devolucion;
                    this.total_pagar = response.data.remision.total_pagar;

                    if(this.remision.estado != 'Terminado'){
                        this.btnGuardar = true;
                    }
                    if(this.remision.estado == 'Terminado'){
                        this.inputUnidades = true;
                    }
                }).catch(error => {
                    console.log(error.response);
                });
                
            },
            guardarUnidades(devolucion, i){
                if(devolucion.unidades >= 0){
                    axios.put('/actualizar_unidades', devolucion).then(response => {
                        this.devoluciones.splice(i, 1, response.data.devolucion);
                        this.remision.total_devolucion = response.data.total_devolucion;
                        this.remision.total_pagar = response.data.total_pagar;
                    }).catch(error => {
                        console.log(error.response);
                    });
                }
            },
            guardar(){
                axios.put('/concluir_remision', this.remision).then(response => {
                    this.inputUnidades = true;
                    this.btnGuardar = false;
                    this.inputNRemision = false;
                }).catch(error => {
                    console.log(error.response);
                });
            }
        },
    }
</script>