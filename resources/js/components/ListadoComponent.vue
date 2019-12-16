<template>
    <div>
        <check-connection-component></check-connection-component>
        <div v-if="listaRemisiones">
            <div class="row">
                <!-- BUSCAR REMISION POR NUMERO -->
                <div class="col-md-3">
                    <b-row class="my-1">
                        <b-col class="text-right" sm="5">
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
                    <!-- <hr>
                    <b-button variant="info" pill v-b-modal.modal-ayuda><i class="fa fa-info-circle"></i> Ayuda</b-button> -->
                </div>
                <div class="col-md-5">
                    <!-- BUSCAR POR CLIENTE -->
                    <b-row class="my-1">
                        <b-col class="text-right" sm="3">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="9">
                            <b-input
                                style="text-transform:uppercase;"
                                v-model="queryCliente" @keyup="mostrarClientes()">
                            </b-input>
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
                    <b-row class="my-1">
                        <b-col class="text-right" sm="3"><label for="input-estado">Estado</label></b-col>
                        <b-col sm="9">
                            <b-form-select v-model="estadoRemision" :options="estados" @change="porEstado()"></b-form-select>
                        </b-col>
                    </b-row>
                </div>
                <!-- BUSCAR POR FECHAS -->
                <div class="col-md-4">
                    <b-row>
                        <b-col class="text-right" sm="3">
                            <label for="input-inicio">De:</label>
                        </b-col>
                        <b-col sm="9">
                            <input 
                                class="form-control" 
                                type="date" 
                                :state="stateDate"
                                v-model="inicio"
                                @change="porFecha()">
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col class="text-right" sm="3">
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
                    <b-col align="right">
                        <a 
                            v-if="role_id === 1 && remisiones.length > 0 && num_remision === null"
                            class="btn btn-dark"
                            :href="'/down_gral_excel/' + cliente_id + '/' + inicio + '/' + final + '/' + estadoRemision">
                            <i class="fa fa-download"></i> General
                        </a>
                        <a 
                            v-if="role_id !== 3 && remisiones.length > 0 && num_remision === null"
                            class="btn btn-dark"
                            :href="'/down_remisiones_excel/' + cliente_id + '/' + inicio + '/' + final + '/' + estadoRemision">
                            <i class="fa fa-download"></i> EXCEL
                        </a>
                        <a 
                            v-if="role_id != 3 && remisiones.length > 0 && num_remision === null"
                            class="btn btn-dark"
                            :href="'/down_remisiones_pdf/' + cliente_id + '/' + inicio + '/' + final + '/' + estadoRemision">
                            <i class="fa fa-download"></i> PDF
                        </a>
                    </b-col>
                    <b-col sm="3" class="text-right">
                        <b-button variant="success" v-if="role_id === 2" @click="nuevaRemision()">
                            <i class="fa fa-plus"></i> Crear remisión
                        </b-button>
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
                    hover
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
                    <template 
                        v-if="row.item.estado === 'Proceso' || row.item.estado === 'Terminado'" 
                        slot="pagos_por" slot-scope="row">
                        <p v-if="row.item.depositos_count > 0">Teresa Pérez</p>
                        <!-- <p v-else>Almacén</p> -->
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
                            <th>${{ total_pagar | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
            </div>
        </div>
        <!-- DETALLES DE LA REMISIÓN -->
        <div v-if="detalles">
            <b-row>
                <b-col sm="3"><h5><b>Remisión No. {{ remision.id }}</b></h5></b-col>
                <b-col sm="3" align="right">
                    <b-button 
                        variant="danger" 
                        v-b-modal.modal-cancelar 
                        v-if="remision.estado == 'Iniciado' && role_id == 2">
                        <i class="fa fa-close"></i> Cancelar remisión
                    </b-button>
                    <b-badge variant="primary" v-if="remision.estado == 'Proceso' && remision.total_pagar > 0">Remisión entregada</b-badge>
                    <b-badge variant="danger" v-if="remision.estado == 'Cancelado'">Remisión cancelada</b-badge>
                    <b-badge variant="success" v-if="remision.total_pagar == 0 && (remision.pagos > 0 || remision.total_devolucion > 0)">Remisión pagada</b-badge>
                </b-col>
                <b-col sm="2">
                    <a class="btn btn-dark" v-if="role_id !== 2" :href="'/imprimirSalida/' + remision.id">
                        <i class="fa fa-download"></i> Descargar
                    </a>
                    <b-button v-if="role_id === 2" :href="`/download_remision/${remision.id}`" variant="dark">Descargar</b-button>
                </b-col>
                <b-col sm="2">
                    <b-button v-b-modal.my-comentarios @click="ini_comment()" variant="info">Comentarios</b-button>
                </b-col>
                <b-col sm="2" align="right">
                    <b-button 
                        variant="secondary"
                        @click="detalles = false; listaRemisiones = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <p>
                <label><b>Fecha de creación:</b> {{ remision.fecha_creacion }}</label><br>
                <label><b>Cliente:</b> {{ remision.cliente.name }}</label><br>
                <label v-if="remision.responsable != null">
                    <b>Responsable de entrega:</b> {{ remision.responsable }}
                </label>
            </p>
            <hr>
            <b-row>
                <b-col sm="10"><h4><b>Salida</b></h4></b-col>
                <b-col sm="1">
                    <b-button 
                        variant="link" 
                        :class="mostrarSalida ? 'collapsed' : null"
                        :aria-expanded="mostrarSalida ? 'true' : 'false'"
                        aria-controls="collapse-1"
                        @click="mostrarSalida = !mostrarSalida">
                        <i class="fa fa-sort-asc"></i>
                    </b-button>
                </b-col>
                <b-col sm="1"></b-col>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarSalida" class="mt-2">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <td></td><td></td>
                            <td></td><td></td>
                            <td><h5>${{ remision.total | formatNumber }}</h5></td>
                        </tr>
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
                            <td>${{ registro.costo_unitario | formatNumber }}</td>
                            <td>{{ registro.unidades }}</td>
                            <td>${{ registro.total | formatNumber }}</td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
            <b-row v-if="remision.pagos > 0">
                <b-col sm="10"><h4><b>Pagos</b></h4></b-col>
                <b-col sm="1">
                    <b-button 
                        variant="link" 
                        :class="mostrarPagos ? 'collapsed' : null"
                        :aria-expanded="mostrarPagos ? 'true' : 'false'"
                        aria-controls="collapse-3"
                        @click="mostrarPagos = !mostrarPagos">
                        <i class="fa fa-sort-asc"></i>
                    </b-button>
                </b-col>
                <b-col sm="1">
                    <!-- <b-button variant="link" id="tooltip-pagos" pill><i class="fa fa-question"></i></b-button>
                    <b-tooltip target="tooltip-pagos" variant="danger" triggers="click">
                        I am tooltip <b>component</b> content!
                    </b-tooltip> -->
                </b-col>
            </b-row>
            <b-collapse id="collapse-3" v-model="mostrarPagos" class="mt-2">
                <div v-if="depositos.length > 0">
                    <h5><b>Pago por monto</b></h5>
                    <b-table hover :items="depositos" :fields="fieldsDep">
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
                        <template slot="thead-top" slot-scope="row">
                            <tr>
                                <th colspan="3"></th><th><h5>${{ total_depositos | formatNumber }}</h5></th>
                            </tr>
                        </template>
                    </b-table>
                    <hr>
                </div>
                <div v-if="checkUnit">
                    <h5><b>Pago por unidades</b></h5>
                    <b-table :items="vendidos" :fields="fieldsP">
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
                                    <template slot="pago" slot-scope="row">${{ row.item.pago | formatNumber }}</template>
                                    <template slot="created_at" slot-scope="row">{{ row.created_at | moment }}</template>
                                </b-table>
                            </b-card>
                        </template>
                        <template slot="thead-top" slot-scope="row">
                            <tr><th colspan="4"></th><th><h5>${{ total_vendido | formatNumber }}</h5></th></tr>
                        </template>
                    </b-table>
                </div>
            </b-collapse>
            <!-- <hr> -->
            <b-row v-if="remision.total_devolucion > 0">
                <b-col sm="10"><h4><b>Devolución</b></h4></b-col>
                <b-col sm="1">
                    <b-button 
                        variant="link" 
                        :class="mostrarDevolucion ? 'collapsed' : null"
                        :aria-expanded="mostrarDevolucion ? 'true' : 'false'"
                        aria-controls="collapse-2"
                        @click="mostrarDevolucion = !mostrarDevolucion">
                        <i class="fa fa-sort-asc"></i>
                    </b-button>
                </b-col>
                <b-col sm="1">
                    <!-- <b-button variant="link" pill><i class="fa fa-question"></i></b-button> -->
                </b-col>
            </b-row>
            <b-collapse id="collapse-2" v-model="mostrarDevolucion" class="mt-2">
                <table class="table table-hover" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                    <thead>
                        <tr>
                            <td></td><td></td>
                            <td></td><td></td>
                            <td><h5>${{ remision.total_devolucion | formatNumber }}</h5></td>
                        </tr>
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
                            <td>${{ devolucion.dato.costo_unitario | formatNumber }}</td>
                            <td>{{ devolucion.unidades }}</td>
                            <td>${{ devolucion.total | formatNumber }}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div v-if="fechas.length > 0">
                    <h5><b>Detalles de la devolución</b></h5>
                    <b-table hover :items="fechas" :fields="fieldsFechas">
                        <template  slot="isbn" slot-scope="data">
                            {{ data.item.libro.ISBN}}
                        </template>
                        <template  slot="titulo" slot-scope="data">
                            {{ data.item.libro.titulo}}
                        </template>
                    </b-table>
                </div>
            </b-collapse>
            <b-row v-if="remision.total_pagar > 0 && depositos.length === 0">
                <b-col sm="10"><h4><b>Pagar</b></h4></b-col>
                <b-col sm="1">
                    <b-button 
                        variant="link" 
                        :class="mostrarFinal ? 'collapsed' : null"
                        :aria-expanded="mostrarFinal ? 'true' : 'false'"
                        aria-controls="collapse-3"
                        @click="mostrarFinal = !mostrarFinal">
                        <i class="fa fa-sort-asc"></i>
                    </b-button>
                </b-col>
                <b-col sm="1">
                    <!-- <b-button variant="link" pill><i class="fa fa-question"></i></b-button> -->
                </b-col>
            </b-row>
            <b-collapse id="collapse-3" v-model="mostrarFinal" class="mt-2">
                <table class="table table-hover" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                    <thead>
                        <tr>
                            <td></td><td></td><td></td><td></td>
                            <td><h5>${{ remision.total_pagar | formatNumber }}</h5></td>
                        </tr>
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
                            <td>${{ devolucion.dato.costo_unitario | formatNumber }}</td>
                            <td>{{ devolucion.unidades_resta }}</td>
                            <td>${{ devolucion.total_resta | formatNumber }}</td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
        </div>
        <!-- CREAR UNA NUEVA REMISIÓN -->
        <div v-if="newRemision">
            <b-row>
                <b-col><h4 style="color: #170057">Crear remisión</h4></b-col>
                <b-col sm="2" align="right">
                    <b-button 
                        variant="secondary"
                        @click="newRemision = false; listaRemisiones = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row><br>
            <remision-component :clientesall="clientes" @actListado="actualizarRs"></remision-component>
        </div>
        <!-- MODALS -->
        <!-- MODAL DE AYUDA -->
        <b-modal id="modal-ayuda" hide-backdrop hide-footer title="Ayuda">
            En este apartado aparecerán todas las remisiones creadas.
            <hr>
            <h5 id="titleA"><b>Búsqueda de remisiones</b></h5>
            <p>
                <ul>
                    <li><b>Búsqueda por remisión</b>: Ingresar el número de folio de la remisión que se desea buscar y presionar <label id="ctrlS">Enter</label>.</li>
                    <li><b>Búsqueda por cliente</b>: Escribir el nombre del cliente y elegir entre las opciones que aparezcan.</li>
                    <li><b>Búsqueda por fechas</b>: Elegir fecha de inicio y fecha final para buscar las remisiones en ese rango de fechas.</li>
                    <li>
                        <b>Búsqueda por estado</b>: Elegir alguna opción<br>
                        <ul>
                            <li><b>NO ENTREGADO:</b> Remisiones que aún no han sido marcadas como entregadas.</li>
                            <li><b>ENTREGADO:</b> Remisiones que ya fueron entregadas y que están pendientes por pagar.</li>
                            <li><b>PAGADO:</b> Remisiones que ya están pagadas.</li>
                            <li v-if="role_id != 3"><b>CANCELADO:</b> Remisiones que están canceladas.</li>
                        </ul>
                    </li>
                    <li><b>Búsqueda por cliente y estado</b>: Escribir el nombre del cliente, elegir entre las opciones que aparezcan y después seleccionar el estado.</li>
                    <li><b>Búsqueda por fecha y cliente</b>: Elegir el rango de fechas y después escribir el nombre del cliente para elegir entre las opciones que aparezcan.</li>
                    <li><b>Búsqueda por fecha y estado</b>: Elegir el rango de fechas y después seleccionar el estado.</li>
                </ul>
            </p>
            <hr>
            <h5 id="titleA"><b>Descargar reporte</b></h5>
            <p v-if="role_id != 3">
                Los botones de descarga aparecerán de acuerdo a la búsqueda realizada. Se puede descargar en PDF.<br>
                Para el reporte detallado, solo se podrá descargar en EXCEL.
            </p>
            <p v-else>
                El boton de descarga aparecerá de acuerdo a la búsqueda realizada.
            </p>
            <hr>
            <h5 v-if="role_id != 3" id="titleA"><b>Totales</b></h5>
            <p v-if="role_id != 3">
                El total de Salida, Pagos, Devolución, Donación y Pagar solo aparecen de las remisiones que ya fueron marcadas como entregadas y las que ya están pagadas.
                De las remisiones no entregadas y canceladas el total aparecerá en $0.
            </p>
            <hr v-if="role_id != 3">
            <h5 id="titleA"><b>Pago realizado por</b></h5>
            <p>
                La última columna hace referencia al usuario que realizo el pago de la remisión. Incluye pago en efectivo y por devolución.
            </p>
        </b-modal>
        <!-- CANCELAR REMISIÓN -->
        <b-modal id="modal-cancelar" title="Cancelar remisión">
            <p><b><i class="fa fa-exclamation-triangle"></i> ¿Estas seguro de cancelar la remisión?</b></p>
            <b-alert show variant="warning">
                <i class="fa fa-exclamation-circle"></i> Una vez presionado <b>OK</b> no se podrán realizar cambios.
            </b-alert>
            <div slot="modal-footer">
                <b-button variant="danger" :disabled="load" @click="cambiarEstado()">OK</b-button>
            </div>
        </b-modal>
        <!-- MODAL PARA HACER COMENTARIOS -->
        <b-modal id="my-comentarios" size="lg" title="Comentarios de la remisión">
            <div v-if="!newComment">
                <div class="text-right">
                    <b-button v-if="!newComment && remision.estado != 'Cancelado'" variant="success" @click="newComment = true"><i class="fa fa-plus"></i> Agregar comentario</b-button>
                </div>
                <hr>
                <b-table v-if="comentarios.length" :items="comentarios" :fields="fieldsComen">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="user_id" slot-scope="row">{{ row.item.user.name }}</template>
                    <template slot="created_at" slot-scope="row">{{ row.item.created_at | moment }}</template> 
                </b-table>
                <b-alert v-else show variant="secondary">La remisión no tiene comentarios</b-alert>
            </div>
            <div v-else>
                <b-form @submit.prevent="guardarComentario()">
                    <label><b>Escribir comentario</b></label>
                    <b-form-input type="text" v-model="commentRem.comentario" required></b-form-input><br>
                    <div class="text-right">
                        <b-button type="submit" :disabled="load" variant="success">
                            <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                        </b-button>
                    </div>
                </b-form>
            </div>
            <div slot="modal-footer"></div>
        </b-modal>
    </div>
</template>

<script>
    moment.locale('es');
    export default {
        props: ['role_id', 'registersall'],
        data() {
            return {
                clientes: [],
                fields: [
                    { key: 'id', label: 'Folio' },
                    { key: 'fecha_creacion', label: 'Fecha de creación' },
                    { key: 'cliente_id', label: 'Cliente' },
                    { key: 'total', label: 'Salida' },
                    'pagos',
                    { key: 'total_devolucion', label: 'Devolución' },
                    // { key: 'total_donacion', label: 'Donación' },
                    { key: 'total_pagar', label: 'Pagar' },
                    { key: 'detalles', label: '' },
                    { key: 'pagos_por', label: '' }
                ],
                fieldsDep: [
                    {key: 'index', label: 'No.'},
                    {key: 'created_at', label: 'Fecha de pago'},
                    {key: 'user', label: 'Pago realizado por'},
                    'pago',
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
                    { value: 'Iniciado', text: 'Iniciado' }
                ],
                estadoRemision: null,
                detalles: false,
                mostrarSalida: false,
                mostrarPagos: false,
                mostrarDevolucion: false,
                mostrarFinal: false,
                donaciones: [],
                fieldsDon: [
                    { key: 'isbn', label: 'ISBN' },
                    { key: 'titulo', label: 'Libro' },
                    { key: 'unidades', label: 'Unidades donadas' },
                    { key: 'created_at', label: 'Fecha' },
                    {key: 'entregado_por', label: 'Donación entregada por'}, 
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
                    {key: 'created_at', label: 'Fecha'},
                    {key: 'user_id', label: 'Pago realizado por'},
                    {key: 'entregado_por', label: 'Pago entregado por'}, 
                    'unidades', 'pago'
                ],
                load: false,
                estados: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'no_entregado', text: 'NO ENTREGADO' },
                    { value: 'entregado', text: 'ENTREGADO' },
                    { value: 'pagado', text: 'PAGADO'},
                    { value: 'cancelado', text: 'CANCELADO', disabled: this.role_id === 3 }
                ],
                currentPage: 1,
                perPage: 10,
                total_donacion: 0,
                fechas: [],
                fieldsFechas: [
                    { key: 'isbn', label: 'ISBN' },
                    { key: 'titulo', label: 'Libro' },
                    { key: 'unidades', label: 'Unidades devueltas' },
                    { key: 'fecha_devolucion', label: 'Fecha' },
                    { key: 'entregado_por', label: 'Devolución entregada por' }
                ],
                depositos: [],
                comentarios: [],
                fieldsComen: [
                    {key: 'index', label: 'N.'},
                    'comentario',
                    {key: 'user_id', label: 'Usuario'},
                    {key: 'created_at', label: 'Fecha'}, 
                ],
                newComment: false,
                commentRem: {
                    remision_id: null,
                    comentario: ''
                },
                stateDate: null,
                newRemision: false,
                listaRemisiones: true,
                total_depositos: 0,
                total_vendido: 0,
                checkUnit: false
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
            // INICIALIZAR PARA NUEVA REMISIÓN
            nuevaRemision(){
                axios.get('/getTodo').then(response => {
                    this.clientes = response.data;
                    this.listaRemisiones = false;
                    this.newRemision = true;
                }).catch(error => {
                   this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // AGREGAR LA NUEVA REMISIÓN A LA LISTA
            actualizarRs(remision){
                this.remisiones.unshift(remision);
                this.acumular();
                this.makeToast('success', 'La remisión se creó correctamente.');
                this.newRemision = false;
                this.listaRemisiones = true;
            },
            // GUARDAR COMENTARIO DE LA REMISIÓN
            guardarComentario() {
                this.commentRem.remision_id = this.remision.id;
                this.load = true;
                axios.post('/guardar_comentario', this.commentRem).then(response => {
                    this.load = false;
                    this.makeToast('success', 'El comentario se guardo correctamente');
                    this.comentarios.push(response.data);
                    this.ini_comment();
                })
                .catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // Inicializar las variables para agregar un nuevo comentario
            ini_comment(){
                this.newComment = false;
                this.commentRem = {
                    remision_id: null,
                    comentario: ''
                }
            },
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
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Error al consultar el numero de remisión ingresado.');
                    });
                }
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
                    this.inicio = '0000-00-00';
                    this.final = '0000-00-00';
                    this.estadoRemision = null;
                    this.num_remision = null;
                    this.resultsClientes = [];
                    this.estadoRemision = null;
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
                    axios.get('/buscar_por_estado', {params: {
                        estado: this.estadoRemision, cliente_id: this.cliente_id,
                        inicio: this.inicio, final: this.final}})
                    .then(response => {
                        this.valores(response);
                        this.num_remision = null;
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                    });
                }
            },
            // OBTENER REMISIONES POR FECHA
            porFecha(){
                if(this.final != '0000-00-00'){
                    if(this.inicio != '0000-00-00'){
                        axios.get('/buscar_por_fecha', {
                            params: {cliente_id: this.cliente_id, inicio: this.inicio, final: this.final}}).then(response => {
                            this.valores(response);
                            this.num_remision = null;
                            this.estadoRemision = null;
                        });
                    } else {
                        this.stateDate = false;
                        this.makeToast('warning', 'Es necesario seleccionar la fecha de inicio');
                    }
                }
            },
            // MOSTRAR LOS DETALLES DE LA REMISIÓN
            detallesRemision(remision){
                this.mostrarSalida = false;
                this.mostrarPagos = false;
                this.mostrarDevolucion = false;
                this.mostrarFinal = false;
                this.total_depositos = 0;
                this.total_vendido = 0;
                this.checkUnit = false;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    this.listaRemisiones = false;
                    this.detalles = true;
                    this.remision = remision;
                    this.registros = response.data.remision.datos;
                    this.devoluciones = response.data.remision.devoluciones;
                    this.vendidos = response.data.vendidos;
                    this.fechas = response.data.remision.fechas;
                    this.donaciones = response.data.remision.donaciones;
                    this.depositos = response.data.remision.depositos;
                    this.comentarios = response.data.remision.comentarios;

                    this.depositos.forEach(deposito => {
                        this.total_depositos += deposito.pago;
                    });

                    var count = 0;
                    this.vendidos.forEach(vendido => {
                        if(vendido.unidades > 0){
                           count += 1; 
                        }
                        this.total_vendido += vendido.total;
                    });

                    if(count > 0 && (this.total_depositos !== this.total_vendido)){
                        this.checkUnit = true;
                    }
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
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
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
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
        /* background-color: #f2f8ff; */
    }
</style>