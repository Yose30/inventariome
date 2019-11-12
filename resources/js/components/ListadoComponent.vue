<template>
    <div>
        <check-connection-component></check-connection-component>
        <div v-if="!detalles">
            <div class="row">
                <!-- BUSCAR REMISION POR NUMERO -->
                <div class="col-md-3">
                    <b-row class="my-1">
                        <b-col sm="5">
                            <label for="input-numero">Remisión</label>
                        </b-col>
                        <b-col sm="7">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="num_remision" 
                                @keyup.enter="porNumero()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                    <hr>
                    <b-button  v-if="role_id != 3" variant="info" pill v-b-modal.modal-ayuda><i class="fa fa-info-circle"></i> Ayuda</b-button>
                </div>
                <div class="col-md-5">
                    <!-- BUSCAR POR CLIENTE -->
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="9">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()"></b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listaL">
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
                    <hr>
                    <!-- BUSCAR REMISION POR ESTADO -->
                    <b-row class="my-1" v-if="role_id != 3">
                        <b-col sm="3"><label for="input-estado">Estado</label></b-col>
                        <b-col sm="9">
                            <b-form-select v-model="estadoRemision" :options="estados" @change="porEstado()"></b-form-select>
                        </b-col>
                    </b-row>
                </div>
                <!-- BUSCAR POR FECHAS -->
                <div class="col-md-4">
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-inicio">De:</label>
                        </b-col>
                        <b-col sm="9">
                            <input 
                                class="form-control" 
                                type="date" 
                                v-model="inicio">
                        </b-col>
                    </b-row>
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-final">A: </label>
                        </b-col>
                        <b-col sm="9">
                            <input 
                                class="form-control" 
                                type="date" 
                                v-model="final"
                                @change="porFecha()">
                        </b-col>
                    </b-row>
                </div>
            </div>
            <hr>
            <!-- IMPIRMIR REPORTE -->
            <div>
                <b-row>
                    <b-col>
                        <!-- PAGINACIÓN -->
                        <b-pagination
                            v-if="remisiones.length"
                            v-model="currentPage"
                            :total-rows="remisiones.length"
                            :per-page="perPage"
                            aria-controls="my-table"
                            align="left">
                        </b-pagination>
                    </b-col>
                    <b-col align="right" v-if="role_id != 3">
                        <div v-if="imprimirCliente && remisiones.length">
                            <a 
                                class="btn btn-danger"
                                :href="'/imprimirCliente/' + cliente_id + '/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Descargar PDF
                            </a>
                            <a 
                                class="btn btn-success"
                                :href="'/imprimirClienteEXC/' + cliente_id + '/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Descargar Excel
                            </a>
                            <a 
                                class="btn btn-primary"
                                :href="'/imprimirClienteDet/' + cliente_id + '/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Detallado Excel
                            </a>
                        </div>
                        <div v-if="imprimirFecha && remisiones.length">
                            <a 
                                class="btn btn-danger"
                                :href="'/imprimirFecha/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Descargar PDF
                            </a>
                            <a 
                                class="btn btn-success"
                                :href="'/imprimirFechaEXC/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Descargar Excel
                            </a>
                            <a 
                                class="btn btn-primary"
                                :href="'/imprimirFechaDet/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Detallado Excel
                            </a>
                        </div>
                        <div v-if="imprimirEstado && remisiones.length">
                            <a 
                                class="btn btn-danger"
                                :href="'/imprimirEstado/' + estadoRemision + '/' + cliente_id + '/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Descargar PDF
                            </a>
                            <a 
                                class="btn btn-success"
                                :href="'/imprimirEstadoEXC/' + estadoRemision + '/' + cliente_id + '/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Descargar Excel
                            </a>
                            <a 
                                class="btn btn-primary"
                                :href="'/imprimirEstadoDet/' + estadoRemision + '/' + cliente_id + '/' + inicio + '/' + final">
                                <i class="fa fa-download"></i> Detallado Excel
                            </a>
                        </div>
                    </b-col>
                </b-row>
            </div>
            <!-- LISTADO DE REMISIONES -->
            <div>
                <!-- TABLA DE REMISIONES -->
                <b-alert v-if="remisiones.length === 0" show variant="secondary">
                    <i class="fa fa-warning"></i> No se encontraron registros.
                </b-alert>
                <b-table 
                    responsive
                    :items="remisiones" 
                    :fields="fields" 
                    v-if="remisiones.length"
                    :tbody-tr-class="rowClass"
                    :per-page="perPage"
                    :current-page="currentPage"
                    id="my-table">
                    <template slot="cliente_id" slot-scope="row">
                        {{ row.item.cliente.name }}
                    </template>
                    <template slot="total" slot-scope="row">
                        ${{ row.item.total | formatNumber }}
                    </template>
                    <template slot="total_devolucion" slot-scope="row">
                        ${{ row.item.total_devolucion | formatNumber }}
                    </template>
                    <template slot="total_donacion" slot-scope="row">
                        ${{ row.item.total_donacion | formatNumber }}
                    </template>
                    <template slot="pagos" slot-scope="row">
                        ${{ row.item.pagos | formatNumber }}
                    </template>
                    <template slot="total_pagar" slot-scope="row">
                        ${{ row.item.total_pagar | formatNumber }}
                    </template>
                    <template slot="detalles" slot-scope="row">
                        <b-button 
                            variant="info"
                            @click="detallesRemision(row.item)">
                            Detalles
                        </b-button>
                    </template>
                    <!-- ENCABEZADO DE TOTALES -->
                    <template slot="thead-top" slot-scope="row" v-if="role_id != 3">
                        <tr>
                            <th colspan="3"></th>
                            <th>${{ total_salida | formatNumber }}</th>
                            <th>${{ total_pagos | formatNumber }}</th>
                            <th>${{ total_devolucion | formatNumber }}</th>
                            <th>${{ total_donacion | formatNumber }}</th>
                            <th>${{ total_pagar | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
            </div>
        </div>
        <!-- DETALLES DE LA REMISIÓN -->
        <div v-if="detalles">
            <b-row>
                <b-col sm="5">
                    <h5><b>Remisión No. {{ remision.id }}</b></h5>
                    <label><b>Fecha de creación:</b> {{ remision.fecha_creacion }}</label>  
                </b-col>
                <b-col sm="3">
                    <b-button 
                        variant="outline-danger" 
                        v-b-modal.modal-cancelar 
                        v-if="remision.estado == 'Iniciado' && role_id == 2">
                        <i class="fa fa-close"></i> Cancelar remisión
                    </b-button>
                </b-col>
                <b-col sm="2" align="left">
                    <b-badge variant="info" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                    <b-badge variant="primary" v-if="remision.estado == 'Proceso' && remision.total_pagar > 0">Entregado</b-badge>
                    <b-badge variant="danger" v-if="remision.estado == 'Cancelado'">Remisión cancelada</b-badge>
                    <b-badge variant="success" v-if="remision.total_pagar == 0 && (remision.pagos > 0 || remision.total_devolucion > 0)">Pagado</b-badge>
                </b-col>
                <b-col sm="2" align="right">
                    <b-button 
                        variant="secondary"
                        @click="detalles = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="9"><label><b>Cliente:</b> {{ remision.cliente.name }}</label></b-col>
                <b-col class="text-right">
                    <a 
                        class="btn btn-info"
                        :href="'/imprimirSalida/' + remision.id">
                        <i class="fa fa-download"></i> Descargar
                    </a>
                </b-col>
            </b-row>
            <hr>
            <div class="row">
                <h4 class="col-md-10"><b>Salida</b></h4>
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
                            <td>$ {{ registro.costo_unitario | formatNumber }}</td>
                            <td>{{ registro.unidades }}</td>
                            <td>$ {{ registro.total | formatNumber }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td>
                            <td></td><td></td>
                            <td><h5>$ {{ remision.total | formatNumber }}</h5></td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
            <hr>
            <div class="row" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                <h4 class="col-md-10"><b>Pagos</b></h4>
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
                <b-table v-if="depositos.length > 0" :items="depositos" :fields="fieldsDep">
                    <template slot="index" slot-scope="row">
                        {{ row.index + 1 }}
                    </template>
                    <template slot="pago" slot-scope="row">
                        ${{ row.item.pago | formatNumber }}
                    </template>
                    <template slot="created_at" slot-scope="row">
                        {{ row.item.created_at | moment }}
                    </template>
                    <template slot="user" slot-scope="row">Teresa Pérez</template>
                </b-table>
                <b-table v-if="depositos.length == 0" :items="vendidos" :fields="fieldsP">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario | formatNumber }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                    <template slot="detalles" slot-scope="row">
                        <b-button v-if="row.item.pagos.length > 0" variant="outline-info" @click="row.toggleDetails">
                            {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}} detalles
                        </b-button>
                    </template>
                    <template slot="row-details" slot-scope="row">
                        <b-card>
                            <b-table :items="row.item.pagos" :fields="fieldsD">
                                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                                <template slot="user_id" slot-scope="row">
                                    <label v-if="row.item.user_id == 2">Teresa Pérez</label>
                                    <label v-if="row.item.user_id == 3">Almacén</label>
                                </template>
                                <template slot="unidades" slot-scope="row">{{ row.item.unidades }}</template>
                                <template slot="pago" slot-scope="row">$ {{ row.item.pago | formatNumber }}</template>
                                <template slot="created_at" slot-scope="row">{{ row.created_at | moment }}</template>
                            </b-table>
                        </b-card>
                    </template>
                </b-table>
            </b-collapse>
            <hr>
            <div class="row" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                <h4 class="col-md-10"><b>Devolución</b></h4>
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
                <table class="table" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
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
                            <td>$ {{ devolucion.dato.costo_unitario | formatNumber }}</td>
                            <td>{{ devolucion.unidades }}</td>
                            <td>$ {{ devolucion.total | formatNumber }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td>
                            <td></td><td></td>
                            <td><h5>$ {{ remision.total_devolucion | formatNumber }}</h5></td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="fechas.length > 0">
                    <h5>Detalles de devolución</h5>
                    <b-table :items="fechas" :fields="fieldsFechas">
                        <template  slot="isbn" slot-scope="data">
                            {{ data.item.libro.ISBN}}
                        </template>
                        <template  slot="titulo" slot-scope="data">
                            {{ data.item.libro.titulo}}
                        </template>
                    </b-table>
                </div>
            </b-collapse>
            <hr>
            <div class="row" v-if="donaciones.length > 0 && (remision.estado == 'Proceso' || remision.estado == 'Terminado')">
                <h4 class="col-md-10"><b>Donaciones</b></h4>
                <b-button 
                    variant="link" 
                    :class="mostrarDonaciones ? 'collapsed' : null"
                    :aria-expanded="mostrarDonaciones ? 'true' : 'false'"
                    aria-controls="collapse-3"
                    @click="mostrarDonaciones = !mostrarDonaciones">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-3" v-model="mostrarDonaciones" class="mt-2">
                <b-table :items="donaciones" :fields="fieldsDon">
                    <template  slot="isbn" slot-scope="data">
                        {{ data.item.libro.ISBN}}
                    </template>
                    <template  slot="titulo" slot-scope="data">
                        {{ data.item.libro.titulo}}
                    </template>
                    <template  slot="created_at" slot-scope="data">
                        {{ data.item.created_at | moment }}
                    </template>
                </b-table>
            </b-collapse>
            <hr>
            <div class="row" v-if="(remision.estado == 'Proceso' || remision.estado === 'Terminado') && (depositos.length === 0 || remision.total_pagar === 0)">
                <h4 class="col-md-10"><b>Pagar</b></h4>
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
                <table class="table" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
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
                            <td>$ {{ devolucion.dato.costo_unitario | formatNumber }}</td>
                            <td>{{ devolucion.unidades_resta }}</td>
                            <td>$ {{ devolucion.total_resta | formatNumber }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td><td></td><td></td>
                            <td><h5>$ {{ remision.total_pagar | formatNumber }}</h5></td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
        </div>
        <!-- MODALS -->
        <!-- MODAL DE AYUDA -->
        <b-modal id="modal-ayuda" hide-backdrop hide-footer title="Ayuda">
            <p>
                <b>Busqueda de remisiones</b><br>
                <ul>
                    <li><b>Busqueda por remisión</b>: Ingresar el numero de folio de la remisión que se desea buscar y presionar <b>Enter</b>.</li>
                    <li><b>Busqueda por cliente</b>: Escribir el nombre del cliente y elegir entre las opciones que aparezcan.</li>
                    <li><b>Busqueda por fechas</b>: Elegir fecha de inicio y fecha final para buscar las remisiones en ese rango de fechas.</li>
                    <li>
                        <b>Busqueda por estado</b>: Elegir alguna opción<br>
                        <ul>
                            <li><b>NO ENTREGADO:</b> Remisiones que aún no han sido marcadas como entregadas.</li>
                            <li><b>ENTREGADO:</b> Remisiones que ya fueron entregadas y que estan pendientes por pagar.</li>
                            <li><b>PAGADO:</b> Remisiones que ya están pagadas.</li>
                            <li><b>CANCELADO:</b> Remisiones que están canceladas.</li>
                        </ul>
                    </li>
                    <li><b>Busqueda por cliente y fecha</b>: Elegir el rango de fechas y despues escribir el nombre del cliente para elegir entre las opciones que aparezcan.</li>
                    <li><b>Busqueda por estado y fecha</b>: Elegir el rango de fechas y despues seleccionar el estado.</li>
                    <li><b>Busqueda por cliente y estado</b>: Escribir el nombre del cliente para elegir entre las opciones que aparezcan y despues seleccionar el estado.</li>
                </ul>
            </p>
            <hr>
            <p>
                <b>Descargar reporte</b><br>
                Los botones de descarga aparecerán de acuerdo a la busqueda realizada. Se puede descargar en PDF o EXCEL.<br>
                Para el reporte detallado, solo se podrá descargar en EXCEL.
            </p>
            <hr>
            <p>
                <b>Totales</b><br>
                El total de Salida, Pagos, Devolución, Donación y Pagar solo aparecen de las remisiones que ya fueron marcadas como entregadas y las que ya están pagadas.
                De las remisiones no entregadas y canceladas el total aparecerá en $0.
            </p>
        </b-modal>
        <b-modal id="modal-cancelar" title="Cancelar remisión">
            <p><b><i class="fa fa-exclamation-triangle"></i> ¿Estas seguro de cancelar la remisión?</b></p>
            <div slot="modal-footer">
                <b-button :disabled="load" @click="cambiarEstado()">OK</b-button>
            </div>
        </b-modal>
        <!-- <b-modal ref="modal-dates" hide-backdrop title="Busqueda por estado">
            <p>Seleccionar rango de fechas para buscar las remisiones.</p>
            <hr>
            <b-row class="my-1">
                <b-col sm="2" align="right">
                    <label for="input-inicio">De:</label>
                </b-col>
                <b-col sm="10">
                    <input class="form-control" type="date" v-model="estinicio">
                </b-col>
            </b-row>
            <b-row class="my-1">
                <b-col sm="2" align="right">
                    <label for="input-final">A: </label>
                </b-col>
                <b-col sm="10">
                    <input class="form-control" type="date" v-model="estfinal">
                </b-col>
            </b-row>
            <div slot="modal-footer">
                <b-row>
                    <b-col sm="9">
                        <b-button 
                            variant="info"
                            :disabled="estinicio === '' || estfinal === ''"
                            :href="'/imprimirEstado/' + estadoRemision + '/' + 1 + '/' + estinicio + '/' + estfinal">
                            <i class="fa fa-download"></i> General
                        </b-button>
                        <b-button 
                            id="tooltip-descargar"
                            variant="info"
                            :disabled="estinicio === '' || estfinal === ''"
                            :href="'/imprimirEstado/' + estadoRemision + '/' + 2 + '/' + estinicio + '/' + estfinal">
                            <i class="fa fa-download"></i> Detallado
                        </b-button>
                        <b-tooltip target="tooltip-descargar" triggers="hover">
                            Para descargar el reporte detallado, el rango de fecha debe ser igual o menor a dos meses.
                        </b-tooltip>
                    </b-col>
                    <b-col sm="3" class="text-right">
                        <b-button variant="primary" @click="porEstado()">Buscar</b-button>
                    </b-col>
                </b-row>
            </div>
        </b-modal> -->
    </div>
</template>

<script>
    moment.locale('es');
    export default {
        props: ['role_id', 'registersall'],
        data() {
            return {
                fields: [
                    { key: 'id', label: 'Folio' },
                    { key: 'fecha_creacion', label: 'Fecha de creación' },
                    { key: 'cliente_id', label: 'Cliente' },
                    { key: 'total', label: 'Salida' },
                    'pagos',
                    { key: 'total_devolucion', label: 'Devolución' },
                    { key: 'total_donacion', label: 'Donación' },
                    { key: 'total_pagar', label: 'Pagar' },
                    { key: 'detalles', label: '' }
                ],
                fieldsDep: [
                    {key: 'index', label: 'No.'},
                    'pago',
                    {key: 'created_at', label: 'Fecha de pago'},
                    {key: 'user', label: 'Usuario'}
                ],
                num_remision: null,
                inicio: '0000-00-00',
                final: '0000-00-00',
                remisiones: this.registersall,
                remision: {},
                queryCliente: '',
                resultsClientes: [],
                total_salida: 0,
                total_devolucion: 0,
                total_pagar: 0,
                cliente_id: null,
                options: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'Terminado', text: 'Terminado'},
                    { value: 'Proceso', text: 'Entregado' },
                    { value: 'Iniciado', text: 'Iniciado' },
                ],
                imprimirCliente: false,
                imprimirEstado: false,
                imprimirFecha: false,
                estadoRemision: null,
                detalles: false,
                mostrarSalida: false,
                mostrarPagos: false,
                mostrarDevolucion: false,
                mostrarDonaciones: false,
                mostrarFinal: false,
                donaciones: [],
                fieldsDon: [
                    { key: 'isbn', label: 'ISBN' },
                    { key: 'titulo', label: 'Libro' },
                    { key: 'unidades', label: 'Unidades donadas' },
                    { key: 'created_at', label: 'Fecha' }
                ],
                registros: [],
                devoluciones: [],
                total_pagos: 0,
                vendidos: [],
                fieldsP: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    {key: 'unidades', label: 'Unidades'}, 
                    'subtotal',
                    {key: 'detalles', label: ''}
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    {key: 'user_id', label: 'Usuario'}, 
                    'unidades',
                    'pago', 
                    {key: 'created_at', label: 'Fecha'}, 
                ],
                load: false,
                estados: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'no_entregado', text: 'NO ENTREGADO' },
                    { value: 'entregado', text: 'ENTREGADO' },
                    { value: 'pagado', text: 'PAGADO'},
                    { value: 'cancelado', text: 'CANCELADO' }
                ],
                currentPage: 1,
                perPage: 10,
                total_donacion: 0,
                fechas: [],
                fieldsFechas: [
                    { key: 'isbn', label: 'ISBN' },
                    { key: 'titulo', label: 'Libro' },
                    { key: 'unidades', label: 'Unidades devueltas' },
                    { key: 'fecha_devolucion', label: 'Fecha' }
                ],
                depositos: []
            }
        },
        created: function(){
			this.acumular();
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            },
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            // CONSULTAR REMISIÓN POR NUMERO
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        if(this.role_id === 3 && response.data.remision.estado === 'Cancelado'){
                            this.makeToast('warning', 'La remisión esta cancelada.');
                        }
                        else{
                            this.remision = response.data.remision;
                            this.remisiones = [];
                            this.remisiones.push(this.remision);
                            this.acumular();
                            this.ini_imprimir(false, false, false);
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Error al consultar el numero de remisión ingresado.');
                    });
                }
            },
            // INCIALIZAR VARIABLES PARA INDICAR QUE BOTON DE IMPRIMIR SE MOSTRARA
            ini_imprimir(t1, t2, t3){
                if(this.inicio == '' || this.final == ''){
                    this.inicio = '0000-00-00';
                    this.final = '0000-00-00';
                }
                this.imprimirCliente = t1;
                this.imprimirEstado = t2;
                this.imprimirFecha = t3;
            },
            // MOSTRAR LOS CLIENTES
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    this.resultsClientes = [];
                }
            },
            // MOSTRAR REMISIONES POR CLIENTE
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id, inicio: this.inicio, final: this.final}}).then(response => {
                    this.cliente_id = result.id;
                    this.queryCliente = result.name;
                    this.resultsClientes = [];
                    this.ini_imprimir(true, false, false);
                    this.valores(response);
                });
            },
            // AISGNAR LOS DATOS A VARIABLES
            valores(response){
                this.remisiones = [];
                if(this.role_id === 3){
                    response.data.forEach(remision => {
                        if(remision.estado != 'Cancelado'){
                            this.remisiones.push(remision)
                        }
                    });
                }
                else {
                    this.remisiones = response.data;
                }
                this.acumular();
            },
            // MOSTRAR REMISIONES POR ESTADO
            porEstado(){
                if(this.estadoRemision != ''){
                    axios.get('/buscar_por_estado', {
                                params: {
                                    estado: this.estadoRemision,
                                    inicio: this.inicio,
                                    final: this.final,
                                    cliente_id: this.cliente_id
                                }
                            }).then(response => {
                        this.valores(response);
                        this.ini_imprimir(false, true, false);
                        //  this.$refs['modal-dates'].hide();
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    });
                }
            },
            // OBTENER TODAS LAS REMISIONES
            getTodo(){ 
                axios.get('/todos_los_clientes').then(response => {
                    this.imprimirCliente = true;
                    this.imprimirEstado = false;
                    this.cliente_id = null;
                    // if(this.inicio == '' || this.final == ''){
                    //     this.inicio = '0000-00-00';
                    //     this.final = '0000-00-00';
                    // }
                    this.valores(response);
                });
            },
            // OBTENER REMISIONES POR FECHA
            porFecha(){
                axios.get('/buscar_por_fecha', {params: {inicio: this.inicio, final: this.final}}).then(response => {
                    this.valores(response);
                    this.ini_imprimir(false, false, true);
                });
                
            },
            // MOSTRAR LOS DETALLES DE LA REMISIÓN
            detallesRemision(remision){
                this.mostrarSalida = false;
                this.mostrarPagos = false;
                this.mostrarDevolucion = false;
                this.mostrarDonaciones = false;
                this.mostrarFinal = false;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.detalles = true;
                    this.remision = remision;
                    this.registros = response.data.remision.datos;
                    this.devoluciones = response.data.remision.devoluciones;
                    this.vendidos = response.data.vendidos;
                    this.fechas = response.data.remision.fechas;
                    this.donaciones = response.data.remision.donaciones;
                    this.depositos = response.data.remision.depositos;
                });
            },
            // CANCELAR REMISIÓN
            cambiarEstado(){
                this.load = true;
                axios.put('/cancelar_remision', this.remision).then(response => {
                    this.remision.estado = response.data.estado;
                    this.$bvModal.hide('modal-cancelar');
                    this.load = false;
                    this.makeToast('secondary', 'Remisión cancelada');
                })
                .catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            rowClass(item, type) {
                if (!item) return
                if (item.estado == 'Iniciado') return 'table-secondary'
                if (item.estado == 'Cancelado') return 'table-danger'
                if (item.total_pagar == 0 && (item.pagos > 0 || item.total_devolucion > 0)) return 'table-success'
                if (item.total_pagar < 0 && (item.pagos > 0 || item.total_devolucion > 0)) return 'table-warning'
            },
            acumular(){
                this.total_salida = 0;
                this.total_devolucion = 0;
                this.total_pagos = 0;
                this.total_pagar = 0;
                this.total_donacion = 0;
                this.remisiones.forEach(remision => {
                    if(remision.estado == 'Proceso' || remision.estado == 'Terminado'){
                        this.total_salida += remision.total;
                        this.total_devolucion += remision.total_devolucion;
                        this.total_pagos += remision.pagos;
                        this.total_pagar += remision.total_pagar;
                        this.total_donacion += remision.total_donacion;
                    }
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

<style>
    #btnCancelar {
        color: red;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #listaL{
        position: absolute;
        z-index: 100
    }
    #listaL a {
        background-color: #f2f8ff;
    }
</style>