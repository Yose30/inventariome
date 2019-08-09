<template>
    <div>
        <div align="right">
            <b-button variant="success" v-b-modal.modal-nuevoCliente><i class="fa fa-plus"></i> Agregar cliente</b-button>
        </div>
        <b-table :items="clientes" :fields="fields">
            <template slot="editar" slot-scope="row">
                <b-button 
                    v-if="role_id == 2" 
                    v-b-modal.modal-editarCliente 
                    variant="outline-warning" 
                    @click="editarCliente(row.item, row.index)">
                    <i class="fa fa-pencil"></i> Editar
                </b-button>
            </template>
            <template slot="detalles" slot-scope="row">
                <b-button variant="info" v-b-modal.modal-detalles @click="form = row.item">Detalles</b-button>
            </template>
        </b-table>

        <b-modal id="modal-nuevoCliente" title="Agregar cliente">
            <new-client-component @actualizarClientes="actClientes"></new-client-component>
            <div slot="modal-footer"></div>
        </b-modal>

        <b-modal id="modal-detalles" :title="`${form.name}`">
            <label><b>Contacto:</b> {{ form.contacto }}</label><br>
            <label><b>Correo:</b> {{ form.email }}</label><br>
            <label><b>Telefono:</b> {{ form.telefono }}</label><br>
            <label><b>Dirección:</b> {{ form.direccion }}</label><br>
            <label><b>Condiciones de pago:</b> {{ form.condiciones_pago }}</label><br>
            <div slot="modal-footer"></div>
        </b-modal>

        <b-modal id="modal-editarCliente" title="Editar cliente">
            <b-form @submit.prevent="onUpdate">
                <b-row class="my-1">
                    <b-col align="right">Nombre</b-col>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-name"
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
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
               clientes: [],
               fields: [
                   {key: 'id', label: 'N.'},
                   {key: 'name', label: 'Nombre'},
                   'contacto',
                   {key: 'email', label: 'Correo'},
                   {key: 'telefono', label: 'Teléfono'},
                   {key: 'detalles', label: ''},
                   {key: 'editar', label: ''}
                ],
                form: {
                    id: 0,
                    name: '',
                    contacto: '',
                    email: '',
                    telefono: 0,
                    direccion: '',
                    condiciones_pago: ''

                },
                loaded: false,
                errors: {},
                posicion: null,
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/getTodo').then(response => {
                    this.clientes = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            editarCliente(cliente, i){
                this.posicion = i;
                this.form = {
                    id: 0,
                    name: '',
                    contacto: '',
                    email: '',
                    telefono: 0,
                    direccion: '',
                    condiciones_pago: ''
                };
                this.form.id = cliente.id;
                this.form.name = cliente.name;
                this.form.contacto = cliente.contacto;
                this.form.email = cliente.email;
                this.form.telefono = cliente.telefono;
                this.form.direccion = cliente.direccion;
                this.form.condiciones_pago = cliente.condiciones_pago;
            },
            onUpdate(){
                this.loaded = true;
                axios.put('/editar_cliente', this.form).then(response => {
                    this.loaded = false;
                    this.clientes[this.posicion].name = response.data.name;
                    this.clientes[this.posicion].contacto = response.data.contacto;
                    this.clientes[this.posicion].email = response.data.email;
                    this.clientes[this.posicion].telefono = response.data.telefono;
                    this.$bvModal.hide('modal-editarCliente');
                    this.makeToast('success', 'Cliente actualizado');
                })
                .catch(error => {
                    this.loaded = false;
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                    else{
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                    }
                });
            },
            actClientes(cliente){
                this.clientes.push(cliente);
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