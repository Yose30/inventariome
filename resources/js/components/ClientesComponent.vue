<template>
    <div>
        <b-table v-if="!mostrarEditar" :items="clientes" :fields="fields">
            <template slot="editar" slot-scope="row">
                <b-button v-if="role_id == 2" variant="outline-warning" @click="editarCliente(row.item, row.index)"><i class="fa fa-pencil"></i> Editar</b-button>
            </template>
            <template slot="detalles" slot-scope="row">
                <b-button variant="info" v-b-modal.modal-detalles @click="form = row.item">Detalles</b-button>
            </template>
        </b-table>

        <b-modal id="modal-detalles" :title="`${form.name}`">
            <b-row>
                <b-col sm="5" align="right">
                    <label><b>Contacto:</b></label><br>
                    <label><b>Correo:</b></label><br>
                    <label><b>Telefono:</b></label><br>
                    <label><b>Dirección:</b></label><br>
                    <label><b>Condiciones de pago:</b></label>
                </b-col>
                <b-col>
                    <label>{{ form.contacto }}</label><br>
                    <label>{{ form.email }}</label><br>
                    <label>{{ form.telefono }}</label><br>
                    <label>{{ form.direccion }}</label><br>
                    <label>{{ form.condiciones_pago }}</label>
                </b-col>
            </b-row>
        </b-modal>

        <div v-if="mostrarEditar">
            <div align="center">
                <h4>Editar cliente</h4>
                <hr>
                <div class="card col-md-8">
                    <div class="card-body">
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
                            <b-button type="submit" :disabled="loaded" variant="success">
                                <i class="fa fa-check"></i> {{ !loaded ? 'Actualizar' : 'Actualizando' }} <b-spinner small v-if="loaded"></b-spinner>
                            </b-button>
                        </b-form>
                    </div>
                </div>
            </div>
        </div>
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
                mostrarEditar: false,
                form: {},
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
                this.form = {};
                this.form = cliente;
                this.posicion = i;
                this.mostrarEditar = true;
            },
            onUpdate(){
                this.loaded = true;
                axios.put('/editar_cliente', this.form).then(response => {
                    this.loaded = false;
                    this.clientes[this.posicion] = response.data;
                    this.mostrarEditar = false;
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