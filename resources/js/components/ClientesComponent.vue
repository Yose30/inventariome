<template>
    <div>
        <check-connection-component></check-connection-component>
        <b-row>
            <b-col sm="8">
                <!-- BUSCAR CLIENTE POR NOMBRE -->
                <b-row class="my-1">
                    <b-col sm="1"><label>Cliente</label></b-col>
                    <b-col sm="6">
                        <b-input style="text-transform:uppercase;" v-model="queryCliente" @keyup="mostrarClientes()"></b-input>
                    </b-col>
                </b-row>
            </b-col>
            <!-- AGREGAR NUEVO CLIENTE -->
            <b-col class="text-right" sm="4">
                <b-button v-if="role_id === 2" variant="success" v-b-modal.modal-nuevoCliente><i class="fa fa-plus"></i> Agregar cliente</b-button>
            </b-col>
        </b-row>
        <hr>
        <div v-if="clientes.length > 0">
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="clientes.length"
                :per-page="perPage"
                aria-controls="my-table"
            ></b-pagination>
            <!-- LISTADO DE CLIENTES -->
            <b-table 
                responsive
                :items="clientes" 
                :fields="fields"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
                <template slot="editar" slot-scope="row">
                    <b-button 
                        v-if="role_id === 2" 
                        v-b-modal.modal-editarCliente 
                        variant="warning" 
                        style="color: white;"
                        @click="editarCliente(row.item, row.index)">
                        <i class="fa fa-pencil"></i>
                    </b-button>
                </template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" v-b-modal.modal-detalles @click="showDetails(row.item)">Detalles</b-button>
                </template>
            </b-table>
            <!-- MODAL PARA MOSTRAR LOS DETALLES DEL CLIENTE -->
            <b-modal id="modal-detalles" :title="`${form.name}`" hide-footer>
                <label><b>Contacto:</b> {{ form.contacto }}</label><br>
                <label><b>Correo:</b> {{ form.email }}</label><br>
                <label><b>Telefono:</b> {{ form.telefono }}</label><br>
                <label><b>Dirección:</b> {{ form.direccion }}</label><br>
                <label><b>Condiciones de pago:</b> {{ form.condiciones_pago }}</label>
            </b-modal>
            <!-- MODAL PARA AGREGAR CLIENTE -->
            <b-modal id="modal-editarCliente" title="Editar cliente">
                <b-form @submit.prevent="onUpdate()">
                    <b-row class="my-1">
                        <b-col align="right">Nombre</b-col>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-name"
                                style="text-transform:uppercase;"
                                v-model="form.name"
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <b-col align="right">Contacto</b-col>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-name"
                                style="text-transform:uppercase;"
                                v-model="form.contacto"
                                :disabled="loaded">
                            </b-form-input>
                            <div v-if="errors && errors.contacto" class="text-danger">{{ errors.contacto[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <b-col align="right">Correo electrónico</b-col>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-email"
                                v-model="form.email"
                                type="email"
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <b-col align="right">Teléfono</b-col>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-telefono"
                                v-model="form.telefono" 
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.telefono" class="text-danger">{{ errors.telefono[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <b-col align="right">Dirección</b-col>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-direccion"
                                style="text-transform:uppercase;"
                                v-model="form.direccion" 
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <b-col align="right">Condiciones de pago</b-col>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-condiciones_pago"
                                style="text-transform:uppercase;"
                                v-model="form.condiciones_pago" 
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.condiciones_pago" class="text-danger">{{ errors.condiciones_pago[0] }}</div>
                        </div>
                    </b-row>
                    <hr>
                    <div align="right">
                        <b-button type="submit" :disabled="loaded" variant="success">
                            <i class="fa fa-check"></i> {{ !loaded ? 'Actualizar' : 'Actualizando' }} <b-spinner small v-if="loaded"></b-spinner>
                        </b-button>
                    </div>
                </b-form>
                <div slot="modal-footer"></div>
            </b-modal>
        </div>
        <b-alert v-else show variant="secondary">
            <i class="fa fa-warning"></i> No se encontraron registros.
        </b-alert>
        <!-- MODAL PARA AGREGAR UN CLIENTE -->
        <b-modal id="modal-nuevoCliente" title="Agregar cliente">
            <new-client-component @actualizarClientes="actClientes"></new-client-component>
            <div slot="modal-footer"></div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['role_id', 'registersall'],
        data() {
            return {
               clientes: this.registersall,
               fields: [
                   {key: 'name', label: 'Nombre'},
                   {key: 'email', label: 'Correo'},
                   {key: 'telefono', label: 'Teléfono'},
                   'contacto',
                   {key: 'detalles', label: ''},
                   {key: 'editar', label: ''}
                ],
                form: {
                    id: 0,
                    name: '',
                    contacto: null,
                    email: '',
                    telefono: 0,
                    direccion: '',
                    condiciones_pago: ''

                },
                loaded: false,
                errors: {},
                posicion: null,
                queryCliente: '',
                perPage: 10,
                currentPage: 1,
                loadRegisters: false
            }
        },
        methods: {
            showDetails(cliente){
                axios.get('/detallesCliente', {params: {cliente_id: cliente.id}}).then(response => {
                    this.assign_datos(response.data);
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // MOSTRAR TODOS LOS CLIENTES
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.clientes = response.data;
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                    }); 
                }
            },
            // INICIALIZAR PARA EDITAR CLIENTE
            editarCliente(cliente, i){
                this.posicion = i;
                this.assign_datos(cliente);
            },
            assign_datos(cliente){
                this.form.id = cliente.id;
                this.form.name = cliente.name;
                this.form.contacto = cliente.contacto;
                this.form.email = cliente.email;
                this.form.telefono = cliente.telefono;
                this.form.direccion = cliente.direccion;
                this.form.condiciones_pago = cliente.condiciones_pago;
            },
            // AGREGAR CLIENTE A LA LISTA (EVENTO)
            actClientes(cliente){
                this.clientes.unshift(cliente);
                this.$bvModal.hide('modal-nuevoCliente');
                this.makeToast('success', 'El cliente se agrego correctamente.');
            },
            // ACTUALIZAR DATOS DE CLIENTE
            onUpdate(){
                this.loaded = true;
                axios.put('/editar_cliente', this.form).then(response => {
                    this.loaded = false;
                    this.clientes[this.posicion].name = response.data.name;
                    this.clientes[this.posicion].contacto = response.data.contacto;
                    this.clientes[this.posicion].email = response.data.email;
                    this.clientes[this.posicion].telefono = response.data.telefono;
                    this.$bvModal.hide('modal-editarCliente');
                    this.makeToast('success', 'Cliente actualizado correctamente.');
                })
                .catch(error => {
                    this.loaded = false;
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                    else{
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
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