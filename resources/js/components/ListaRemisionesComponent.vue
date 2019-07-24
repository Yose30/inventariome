<template>
    <div>
        <div v-if="mostrar && !detalles">
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
                <div class="col-md-4">
                    <!-- <div class="row">
                        <label class="col-md-3">Estado</label>
                        <div class="col-md-9">
                            <b-form-select v-model="selected" :options="options" @change="porEstado"></b-form-select>
                        </div>
                    </div>
                    <hr> -->
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
                <!-- <div class="col-md-5">
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-inicio">Inicio</label>
                        </b-col>
                        <b-col sm="9">
                            <input 
                                class="form-control" 
                                type="date" 
                                v-model="inicio"
                                @change="porFecha">
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
                </div> -->
            </div>
            <hr>
            <div align="right">
                <div class="col-md-1" v-if="imprimirCliente && remisiones.length">
                    <a 
                        class="btn btn-info"
                        
                        :href="'/imprimirCliente/' + cliente_id + '/' + inicio + '/' + final">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
                <div class="col-md-1" v-if="imprimirEstado && remisiones.length">
                    <a 
                        class="btn btn-info"
                        
                        :href="'/imprimirEstado/' + estadoRemision">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
            <hr>
            <div>
                <table class="table" v-if="tabla_numero">
                    <thead>
                        <tr>
                            <th scope="col">Folio</th>
                            <th scope="col">Fecha de creación</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Salida</th>
                            <th scope="col">Devolución</th>
                            <th scope="col">Final</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Fecha de entrega</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ remision.id }}</td>
                            <td>{{ remision.fecha_creacion }}</td>
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
                            <td>
                                <!-- <button class="btn btn-warning" @click="editarRemision(remision)"><i class="fa fa-pencil"></i></button> -->
                                <!-- v-if="remision.estado != 'Iniciado'" -->
                                <button 
                                    class="btn btn-primary" 
                                    
                                    @click="detallesRemision(remision)">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <!-- <button 
                                    class="btn btn-primary" 
                                    v-if="remision.estado == 'Iniciado'"
                                    @click="editarRemision(remision)">
                                    <i class="fa fa-eye"></i>
                                </button> -->
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" v-if="tabla_gral && remisiones.length">
                    <thead>
                        <tr>
                            <th scope="col">Folio</th>
                            <th scope="col">Fecha de creación</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Salida</th>
                            <th scope="col">Devolución</th>
                            <th scope="col">Final</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Fecha de entrega</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(remision, i) in remisiones" v-bind:key="i">
                            <td>{{ remision.id }}</td>
                            <td>{{ remision.fecha_creacion }}</td>
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
                            <td>
                                <!-- v-if="remision.estado != 'Iniciado'" -->
                                <button 
                                    class="btn btn-primary" 
                                    @click="detallesRemision(remision)">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <!-- <button 
                                    class="btn btn-primary" 
                                    v-if="remision.estado == 'Iniciado'"
                                    @click="editarRemision(remision)">
                                    <i class="fa fa-eye"></i>
                                </button> -->
                            </td>
                        </tr>
                        <tr>
                            <td></td><td></td><td></td>
                            <td><b>$ {{ total_salida }}</b></td>
                            <td><b>$ {{ total_devolucion }}</b></td>
                            <td><b>$ {{ total_pagar }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="!mostrar">
            <div class="row">
                <h4 class="col-md-4">Remisión N. {{ bdremision.id }}</h4>
                <div class="col-md-2">
                    <b-button variant="danger" v-if="mostrarActualizar" @click="show=true"><i class="fa fa-close"></i></b-button>
                    <b-modal v-model="show" title="Cerrar remisión">
                        <p><b><i class="fa fa-exclamation-triangle"></i> No se guardara ningún cambio</b></p>
                        <div slot="modal-footer">
                            <b-button @click="cancelarRemision">OK</b-button>
                        </div>
                    </b-modal>
                </div>
                <div class="col-md-2">
                    <b-button  
                        variant="success"
                        @click="actRemision"
                        v-if="mostrarActualizar"> 
                        <i class="fa fa-check"></i> Guardar
                    </b-button>
                    <b-button 
                        variant="warning" 
                        @click="editar"
                        v-if="!mostrarActualizar">
                        <i class="fa fa-pencil"></i>
                    </b-button>
                </div>
                <div class="col-md-2">
                    <a 
                        class="btn btn-info"
                        v-if="!mostrarActualizar"
                        :href="'/imprimirSalida/' + bdremision.id">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
                <div class="col-md-2" align="right">
                    <b-button 
                        variant="primary" 
                        @click="cancelarRemision"
                        id="btnCancelar"
                        v-if="!mostrarActualizar">
                        <i class="fa fa-close"></i>
                    </b-button>
                </div>
            </div>
            <hr>
            <div v-if="!formularioEditar">
                <div class="row">
                    <h6 class="col-md-10">Datos del cliente</h6>
                    <div class="col-md-1">
                        <button id="btnEditar" class="btn btn-warning" :disabled="btnInformacion" @click="editarInformacion"><i class="fa fa-pencil"></i></button>
                    </div>
                    <b-button 
                        variant="link" 
                        :class="mostrarDatos ? 'collapsed' : null"
                        :aria-expanded="mostrarDatos ? 'true' : 'false'"
                        aria-controls="collapse-1"
                        @click="mostrarDatos = !mostrarDatos">
                        <i class="fa fa-sort-asc"></i>
                    </b-button>
                </div>
                <b-collapse id="collapse-1" v-model="mostrarDatos" class="mt-2">
                    <div class="row">
                        <b-list-group class="col-md-6">
                            <b-list-group-item><b>Nombre:</b> {{ dato.name }}</b-list-group-item>
                            <b-list-group-item><b>Dirección:</b> {{dato.direccion  }}</b-list-group-item>
                            <!-- <b-list-group-item><b>Descuento:</b> {{ dato.descuento }} %</b-list-group-item> -->
                            <b-list-group-item><b>Condiciones de pago:</b> {{ dato.condiciones_pago }}</b-list-group-item>
                        </b-list-group>
                        <b-list-group class="col-md-6">
                            <b-list-group-item><b>Correo electrónico:</b> {{ dato.email }}</b-list-group-item>
                            <b-list-group-item><b>Teléfono:</b></b> {{ dato.telefono }}</b-list-group-item>
                        </b-list-group>
                    </div>
                </b-collapse>
            </div>
            <hr>
            <div align="center" v-if="formularioEditar">
                <div align="right">
                    <button id="btnCancelar" class="btn btn-danger" @click="formularioEditar = false; mostrarDatos = true"><i class="fa fa-close"></i></button>
                </div>
                <div class="card col-md-8">
                    <h6 class="card-title">Datos del cliente</h6>
                    <div class="card-body">
                        <b-form @submit.prevent="onSubmit">
                            <b-row class="my-1">
                                <label align="right" for="input-name" class="col-md-5">Nombre</label>
                                <div class="col-md-7">
                                    <b-form-input 
                                        id="input-name"
                                        v-model="dato.name"
                                        required>
                                    </b-form-input>
                                    <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
                                </div>
                            </b-row>
                            <b-row class="my-1">
                                <label align="right" for="input-email" class="col-md-5">Correo electrónico</label>
                                <div class="col-md-7">
                                    <b-form-input 
                                        id="input-email"
                                        v-model="dato.email"
                                        type="email"
                                        required>
                                    </b-form-input>
                                    <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                                </div>
                            </b-row>
                            <b-row class="my-1">
                                <label align="right" for="input-telefono" class="col-md-5">Teléfono</label>
                                <div class="col-md-7">
                                    <b-form-input 
                                        id="input-telefono"
                                        v-model="dato.telefono" 
                                        required>
                                    </b-form-input>
                                    <div v-if="errors && errors.telefono" class="text-danger">{{ errors.telefono[0] }}</div>
                                </div>
                            </b-row>
                            <b-row class="my-1">
                                <label align="right" for="input-direccion" class="col-md-5">Dirección</label>
                                <div class="col-md-7">
                                    <b-form-input 
                                        id="input-direccion"
                                        v-model="dato.direccion" 
                                        required>
                                    </b-form-input>
                                    <div v-if="errors && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</div>
                                </div>
                            </b-row>
                            <!-- <b-row class="my-1">
                                <label align="right" for="input-descuento" class="col-md-5">Descuento</label>
                                <div class="col-md-7">
                                    <b-form-input 
                                        id="input-descuento"
                                        v-model="dato.descuento" 
                                        type="number"
                                        required>
                                    </b-form-input>
                                    <div v-if="errors && errors.descuento" class="text-danger">{{ errors.descuento[0] }}</div>
                                </div>
                            </b-row> -->
                            <b-row class="my-1">
                                <label align="right" for="input-condiciones_pago" class="col-md-5">Condiciones de pago</label>
                                <div class="col-md-7">
                                    <b-form-input 
                                        id="input-condiciones_pago"
                                        v-model="dato.condiciones_pago" 
                                        required>
                                    </b-form-input>
                                    <div v-if="errors && errors.condiciones_pago" class="text-danger">{{ errors.condiciones_pago[0] }}</div>
                                </div>
                            </b-row>
                            <hr>
                            <b-button type="submit" variant="success"><i class="fa fa-check"></i> Continuar</b-button>
                        </b-form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6" v-if="!formularioEditar">
                    <label><b>Fecha de entrega</b></label>
                    <input 
                        class="form-control" 
                        type="date" 
                        v-model="fecha" 
                        :disabled="inputFecha"
                        @change="bdremision.fecha_entrega=fecha">
                    </input>
                </div>
                <div class="col-md-6" align="right" v-if="!formularioEditar">
                    <label><b>Total:</b> ${{ total_remision }}</label>
                    <hr>
                    <!-- <label><b>{{ dato.descuento }}% descuento:</b> ${{ descuento }}</label><br>
                    <label><b>Total:</b> ${{ pagar }}</label> -->
                </div>
            </div>
            <hr>
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Libro</th>
                            <th scope="col">Costo unitario</th>
                            <th scope="col">Unidades</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in items" v-bind:key="i">
                            <td>{{ item.libro.ISBN }}</td>
                            <td>{{ item.libro.titulo }}</td>
                            <td>$ {{ item.costo_unitario }}</td>
                            <td>{{ item.unidades }}</td>
                            <td>$ {{ item.total }}</td>
                            <td>
                                <button 
                                    class="btn btn-danger" 
                                    @click="eliminarRegistro(item, i)" 
                                    v-if="botonEliminar">
                                    <i class="fa fa-minus-circle"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b-input
                                v-model="isbn"
                                @keyup.enter="buscarLibroISBN()"
                                v-if="inputISBN"
                                ></b-input>
                                <div class="text-danger">{{ respuestaISBN }}</div>
                                <b v-if="!inputISBN">{{ temporal.ISBN }}</b>
                            </td>
                            <td>
                                <b-input
                                v-model="buscarTitulo"
                                @keyup="mostrarLibros"
                                v-if="inputLibro"
                                ></b-input>
                                <div class="list-group" v-if="resultadoslibros.length">
                                    <a 
                                        href="#" 
                                        v-bind:key="i" 
                                        class="list-group-item list-group-item-action" 
                                        v-for="(libro, i) in resultadoslibros" 
                                        @click="datosLibro(libro)">
                                        {{ libro.titulo }}
                                    </a>
                                </div>
                                <b v-if="!inputLibro">{{ temporal.titulo }}</b>
                            </td>
                            <td>
                                <input 
                                    type="number" 
                                    v-model="costo_unitario"
                                    v-if="inputCosto"
                                    min="1"
                                    max="9999"
                                    @keyup.enter="guardarCosto()">
                                </input>
                                <b v-if="!inputCosto">$ {{ temporal.costo_unitario }}</b>
                                <div class="text-danger">{{ respuestaCosto }}</div>
                            </td>
                            <td>
                                <input 
                                type="number" 
                                v-model="unidades"
                                v-if="inputUnidades"
                                min="1"
                                max="9999"
                                @keyup.enter="guardarRegistro()"
                                ></input>
                                <div class="text-danger">{{ respuestaUnidades }}</div>
                            </td>
                            <td></td>
                            <td>
                                <button 
                                    class="btn btn-secondary" 
                                    @click="eliminarTemporal" 
                                    v-if="inputCosto || inputUnidades">
                                    <i class="fa fa-minus-circle"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="detalles">
            <div class="row">
                <h4 class="col-md-11">Remisión n. {{ remision.id }}</h4>
                <button 
                    id="btnCancelar" 
                    class="btn btn-danger"
                    @click="detalles = false">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-11">
                    <b-badge variant="primary" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                    <b-badge variant="primary" v-if="remision.estado == 'Proceso'">{{ remision.estado }}</b-badge>
                    <b-badge variant="success" v-if="remision.estado == 'Terminado'">{{ remision.estado }}</b-badge>
                </div>
                <!-- v-if="remision.estado == 'Terminado'" -->
                <a 
                    class="btn btn-info"
                    v-if="remision.estado != 'Proceso'"
                    :href="'/imprimirSalida/' + remision.id">
                    <i class="fa fa-print"></i> 
                </a>
            </div>
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
                            <td><h5>$ {{ remision.total }}</h5></td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
            <hr>
            <div v-if="remision.estado != 'Iniciado'">
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
                                <td>{{ devolucion.unidades }}</td>
                                <td>$ {{ devolucion.total }}</td>
                            </tr>
                            <tr>
                                <td></td><td></td>
                                <td></td><td></td>
                                <td><h5>$ {{ remision.total_devolucion }}</h5></td>
                            </tr>
                        </tbody>
                    </table>
                </b-collapse>
                <hr>
                <div class="row">
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
                                <td><h5>$ {{ remision.total_pagar }}</h5></td>
                            </tr>
                        </tbody>
                    </table>
                </b-collapse>
            </div>
        </div>
    </div>
</template>

<script>
    moment.locale('es');
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
                total_salida: 0,
                total_devolucion: 0,
                total_pagar: 0,
                tabla_gral: true,
                currentTime: null,
                cliente_id: 0,
                selected: null,
                selected2: 'Terminado',
                options: [
                    { value: 'Terminado', text: 'Terminado'},
                    { value: 'Proceso', text: 'Proceso' },
                    { value: 'Iniciado', text: 'Iniciado' },
                ],
                imprimirCliente: false,
                imprimirEstado:false,
                estadoRemision: '',
                queryTitulo: '',
                resultslibros: [],
                tabla_libros: false,
                libros: [],
                //ATRIBUTOS EDITAR REMISION
                mostrar: true,
                show: false,
                mostrarActualizar: true,
                btnInformacion: true,
                mostrarDatos: false,
                dato: {},
                formularioEditar: false,
                errors: {},
                fecha: '',
                inputFecha: true,
                bdremision: {
                    id: 0,
                    cliente_id: 0,
                    total: 0,
                    fecha_entrega: ''
                },
                items: [],
                botonEliminar: false,
                total_remision: 0,
                descuento: 0,
                pagar: 0,
                isbn: '',
                inputISBN: false,
                respuestaISBN: '',
                temporal: {},
                buscarTitulo: '',
                inputLibro: false,
                resultadoslibros: [],
                costo_unitario: 0,
                inputCosto: false,
                respuestaCosto: '',
                unidades: 0,
                inputUnidades: false,
                respuestaUnidades: '',
                detalles: false,
                mostrarSalida: true,
                mostrarDevolucion: true,
                mostrarFinal: true,
                registros: [],
                devoluciones: [],
                d_total_salida: 0,
                d_total_devolucion: 0,
                d_total_pagar: 0,
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            porNumero(){
                if(this.num_remision > 0){
                    this.respuesta_numero = '';
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        this.remision = response.data.remision;
                        this.cliente_nombre = response.data.cliente_nombre;
                        this.tabla_gral = false;
                        this.tabla_numero = true;
                        this.imprimirCliente = false;
                        this.imprimirEstado = false;
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
                    this.getTodo();
                }
            },
            getTodo(){
                axios.get('/todos_los_clientes').then(response => {
                    this.imprimirCliente = true;
                    this.imprimirEstado = false;
                    this.cliente_id = 0;
                    if(this.inicio == '' || this.final == ''){
                        this.inicio = '0000-00-00';
                        this.final = '0000-00-00';
                    }
                    this.valores(response);
                });
            },
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id, inicio: this.inicio, final: this.final}}).then(response => {
                    this.cliente_id = result.id;
                    this.queryCliente = '';
                    this.resultsClientes = [];

                    if(this.inicio == '' || this.final == ''){
                        this.inicio = '0000-00-00';
                        this.final = '0000-00-00';
                    }
                    this.imprimirEstado = false;
                    this.imprimirCliente = true;

                    this.valores(response);
                });
            },
            porFecha(){
                axios.get('/buscar_por_fecha', {params: {inicio: this.inicio, final: this.final, cliente_id: this.cliente_id}}).then(response => {
                    this.valores(response);
                    if(this.final != ''){
                        this.imprimirEstado = false;
                        this.imprimirCliente = true;
                    }
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
            },
            porEstado(){
                axios.get('/buscar_por_estado', {params: {estado: this.selected}}).then(response => {
                    this.valores(response);
                    this.imprimirCliente = false;
                    this.imprimirEstado = true;
                    this.estadoRemision = this.selected;
                });
            },
            //Mostrar resultados de la busqueda por titulo del libro
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
                }
                else{
                    this.porEstadoLibros();
                } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.resultslibros = [];
                this.queryTitulo = '';
                this.tabla_libros = true;
                console.log(libro);
            },
            inicializar(response){
                this.libros = [];
                this.libros = response.data;
                this.tabla_libros = true;
            },
            //Mostrar resultados de libros con estado
            porEstadoLibros(){
                axios.get('/buscar_por_estado_libros', {params: {estado: this.selected2}}).then(response => {
                    this.inicializar(response);
                });
            },

            //EDITAR REMISION
            editarRemision(remision){
                this.mostrar = false;
                this.mostrarActualizar = false;
                axios.get('/nueva_edicion', {params: {id: remision.id}}).then(response => {
                    axios.get('/getCliente', {params: {id: remision.cliente_id, remision_id: remision.id}}).then(response => {
                        this.dato = response.data.cliente;
                        this.items = response.data.datos;
                        this.items.forEach(item => {
                            this.total_remision += item.total;
                        });
                        this.bdremision = {
                            id: remision.id,
                            fecha_entrega: remision.fecha_entrega,
                            cliente_id: remision.cliente_id,
                        };
                        this.fecha = this.bdremision.fecha_entrega;
                        // this.getDescuento();
                        this.bdremision.total = this.total_remision;
                    });
                });
            },
            getDescuento(){
                this.descuento = (this.total_remision * this.dato.descuento) / 100;
                this.pagar = this.total_remision - this.descuento;
            },
            cancelarRemision(){
                this.show = false;
                this.mostrar = true;
                this.dato = {};
                this.form = {};
                this.errors = {};
                this.fecha = '';
                this.bdremision = {id: 0, cliente_id: 0, total: 0, fecha_entrega: ''};
                this.items = [];
                this.total_remision = 0;
                this.descuento = 0;
                this.pagar = 0;
                this.mostrarDatos = false;
                this.temporal = {};
                this.inputCosto = false;
                this.inputUnidades = false;
                this.inputISBN = true;
                this.inputLibro = true;
                this.buscarTitulo = '';
                this.unidades = 0;
                this.costo_unitario = 0;
            },
            actRemision(){
                this.getDescuento();
                
                this.bdremision.total = this.total_remision;

                axios.put('/actualizar_remision', this.bdremision).then(response => {
                    this.mostrarActualizar = false;
                    this.botonEliminar = false;
                    this.eliminarTemporal();
                    this.inputISBN = false;
                    this.inputLibro = false;
                    this.btnInformacion = true;
                    this.inputFecha = true;
                });
            },
            editar(){
                this.mostrarActualizar = true;
                this.botonEliminar = true;
                this.btnInformacion = false;
                this.inputFecha = false;
                this.eliminarTemporal();
            },
            editarInformacion(){
                this.formularioEditar = true;
            },
            onSubmit(){
                axios.put('/editar_cliente', this.form).then(response => {
                    this.dato = response.data;
                    this.formularioEditar = false;
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            //Eliminar registro de la remision
            eliminarRegistro(item, i){
                axios.delete('/eliminar_registro', {params: {id: item.id}}).then(response => {
                    this.items.splice(i, 1);
                    this.total_remision = this.total_remision - item.total;
                    this.bdremision.total = this.total_remision;
                    // this.getDescuento();
                });
            },
            //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.temporal = response.data;
                    this.isbn = '';
                    this.ini_1();
                }).catch(error => {
                    this.respuestaISBN = 'ISBN incorrecto';
                });
            },
            mostrarLibros(){
                if(this.buscarTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.buscarTitulo}}).then(response => {
                        this.resultadoslibros = response.data;
                    });
               } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.resultadoslibros = [];
                this.ini_1();
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: 0,
                    unidades: 0,
                    total: 0,
                    piezas: libro.piezas
                };
            },
            ini_1(){
                this.inputISBN = false;
                this.inputLibro = false;
                this.inputCosto = true;
            },
            guardarCosto(){
                this.respuestaCosto = '';
                if(this.costo_unitario > 0){
                    this.temporal.costo_unitario = this.costo_unitario;
                    this.inputCosto = false;
                    this.inputUnidades = true;
                    this.respuestaCosto = '';
                }
                else{
                    this.respuestaCosto = 'Costo invalido';
                } 
            },
            eliminarTemporal(){
                this.temporal = {};
                this.inputISBN = true;
                this.respuestaISBN = '';
                this.buscarTitulo = '';
                this.inputLibro = true;
                this.unidades = 0;
                this.inputUnidades = false;
                this.respuestaUnidades = '';
                this.costo_unitario = 0;
                this.inputCosto = false;
                this.respuestaCosto = '';
            },
            guardarRegistro(){
                if(this.unidades > 0){
                    this.temporal.remision_id = this.bdremision.id;
                    this.temporal.unidades = this.unidades;
                    this.temporal.total = this.unidades * this.temporal.costo_unitario;
                    
                    axios.post('/registro_remision', this.temporal).then(response => {
                        this.temporal = {
                            id: response.data.dato.id,
                            libro: {
                                ISBN: response.data.libro.ISBN,
                                titulo: response.data.libro.titulo,
                            },
                            costo_unitario: response.data.dato.costo_unitario,
                            unidades: response.data.dato.unidades,
                            total: response.data.dato.total
                        };
                        this.items.push(this.temporal);
                        this.total_remision += response.data.dato.total;
                        // this.getDescuento();
                        this.eliminarTemporal();
                    }); 
                }
            },
            detallesRemision(remision){
                this.detalles = true;
                this.remision = remision;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.registros = response.data.datos;
                    this.devoluciones = response.data.devoluciones;
                });
            }
        }
    }
</script>

<style>
    #btnCancelar {
        color: red;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
</style>