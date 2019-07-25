<template>
    <div>
        <b-alert v-if="devoluciones.length == 0" show variant="secondary">
            <i class="fa fa-exclamation-triangle"></i> No hay remisones
        </b-alert>
        <b-table v-if="!mostrarDetalles && devoluciones.length > 0" :items="devoluciones" :fields="fields">
            <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
            <template slot="cliente" slot-scope="row">{{ row.item.cliente.name }}</template>
            <template slot="total" slot-scope="row">${{ row.item.total }}</template>
            <template slot="total_devolucion" slot-scope="row">${{ row.item.total_devolucion }}</template>
            <template slot="total_pagar" slot-scope="row">${{ row.item.total_pagar }}</template>
            <template slot="pagos" slot-scope="row">${{ row.item.pagos }}</template>
            <template slot="estado" slot-scope="row">
                <b-badge variant="primary" v-if="row.item.estado == 'Proceso'">Entregado</b-badge>
                <b-badge variant="success" v-if="row.item.estado == 'Terminado'">{{ row.item.estado }}</b-badge>
            </template>
            <template slot="pagar" slot-scope="row">
                <b-button 
                    v-if="row.item.pagos < row.item.total_pagar" 
                    variant="secondary" 
                    v-b-modal.modal-pago 
                    @click="verPago(row.item, row.index)">
                    Pago
                </b-button>
                <b-badge v-else variant="success">Pagado</b-badge>
            </template>
            <template slot="detalles" slot-scope="row">
                <b-button variant="outline-info" @click="detallesRemision(row.item)"><i class="fa fa-eye"></i></b-button>
            </template>
        </b-table>
        <div v-if="mostrarDetalles">
            <b-row>
                <b-col><h4>Remisión n. {{ remision.id }}</h4></b-col>
                <b-col>
                    <div class="text-right">
                        <b-button variant="outline-secondary" @click="mostrarDetalles = false"><i class="fa fa-mail-reply"></i> Devoluciones</b-button>
                    </div>
                </b-col>
            </b-row>
            <hr>
            <b-row>
                <b-col sm="10"><h4>Salida</h4></b-col>
                <b-button 
                    variant="link" 
                    :class="mostrarSalida ? 'collapsed' : null"
                    :aria-expanded="mostrarSalida ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarSalida = !mostrarSalida">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarSalida" class="mt-2">
                <b-table :items="remision.datos" :fields="fieldsSD">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
                </b-table>
                <h5 class="text-right">${{ remision.total }}</h5>
            </b-collapse>
            <hr>
            <b-row>
                <b-col sm="10"><h4>Devolución</h4></b-col>
                <b-button 
                    variant="link" 
                    :class="mostrarDevolucion ? 'collapsed' : null"
                    :aria-expanded="mostrarDevolucion ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarDevolucion = !mostrarDevolucion">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarDevolucion" class="mt-2">
                <b-table :items="remision.devoluciones" :fields="fieldsSD">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
                </b-table>
                <h5 class="text-right">${{ remision.total_devolucion }}</h5>
            </b-collapse>
            <hr>
            <b-row>
                <b-col sm="10"><h4>Remisión final</h4></b-col>
                <b-button 
                    variant="link" 
                    :class="mostrarFinal ? 'collapsed' : null"
                    :aria-expanded="mostrarFinal ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarFinal = !mostrarFinal">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </b-row>
            <b-collapse id="collapse-1" v-model="mostrarFinal" class="mt-2">
                <b-table :items="remision.devoluciones" :fields="fieldsSD">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                    <template slot="unidades" slot-scope="row">{{ row.item.unidades_resta }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total_resta }}</template>
                </b-table>
                <h5 class="text-right">${{ remision.total_pagar }}</h5>
            </b-collapse>
        </div>

        <b-modal id="modal-pago" title="Registrar abono">
            <b-form @submit.prevent="onPay">
                <b-form-group label-cols="4" label-cols-lg="2" label="Pago" label-for="input-pago">
                    <b-form-input id="input-pago" type="number" v-model="pago"></b-form-input>
                </b-form-group>
                <div class="text-right">
                    <b-button type="submit" variant="success">
                        <i class="fa fa-check"></i> Guardar
                    </b-button>
                </div>
            </b-form>
            <div slot="modal-footer"></div>
        </b-modal>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                devoluciones: [],
                fields: [
                    {key: 'index', label: 'N.'}, 
                    'estado',
                    'fecha_devolucion', 
                    'cliente', 
                    {key: 'total', label: 'Salida'}, 
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'total_pagar', label: 'Final'},
                    'pagos',
                    {key: 'pagar', label: 'Registrar pago'},
                    'detalles'
                ],
                fieldsSD: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    'unidades', 
                    'subtotal'],
                mostrarDetalles: false,
                remision: {
                    id: 0,
                    total: 0,
                    total_devolucion: 0,
                    total_pagar: 0,
                    datos: [],
                    devoluciones: []
                },
                mostrarSalida: true,
                mostrarDevolucion: true,
                mostrarFinal: true,
                informacion: {},
                pago: 0,
                posicion: 0,
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/all_devoluciones').then(response => {
                    this.devoluciones = response.data;
                });
            },
            verPago(remision, i){
                this.informacion.id = remision.id;
                this.informacion.total_pagar = remision.total_pagar;
                this.informacion.pagos = remision.pagos;
                this.posicion = i;
            },
            detallesRemision(remision){
                this.remision = { id: 0, total: 0, total_devolucion: 0, total_pagar: 0, datos: [], devoluciones: [] };
                this.mostrarDetalles = true;
                this.remision.id = remision.id;
                this.remision.total = remision.total;
                this.remision.total_devolucion = remision.total_devolucion;
                this.remision.total_pagar = remision.total_pagar;
                axios.get('/datos_devolucion', {params: {remision_id: remision.id}}).then(response => {
                    this.remision.datos = response.data.datos;
                    this.remision.devoluciones = response.data.devoluciones
                });
            },
            onPay(){
                if(this.pago > this.informacion.total_pagar - this.informacion.pagos){
                    this.makeToast('warning', 'El pago es mayor a lo restante');
                }
                else{
                    this.informacion.pago = this.pago;
                    axios.post('/registrar_pago', this.informacion).then(response => {
                        this.devoluciones[this.posicion].pagos = response.data.pagos;
                        this.$bvModal.hide('modal-pago');
                    })
                    .catch(error => {
                        this.makeToast('danger', 'Ocurrio un error, vuelve a intentarlo');
                    });
                }
                
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