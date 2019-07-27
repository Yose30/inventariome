<template>
    <div align="center">
        <h4>Agregar cliente</h4>
        <hr>
        <div class="card col-md-8">
            <div class="card-body">
                <b-form @submit.prevent="onSubmit">
                    <b-row class="my-1">
                        <label align="right" for="input-name" class="col-md-5">Nombre</label>
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
                        <label align="right" for="input-email" class="col-md-5">Correo electrónico</label>
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
                        <label align="right" for="input-telefono" class="col-md-5">Teléfono</label>
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
                        <label align="right" for="input-direccion" class="col-md-5">Dirección</label>
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
                        <label align="right" for="input-condiciones_pago" class="col-md-5">Condiciones de pago</label>
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
                        <i class="fa fa-check"></i> {{ !loaded ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="loaded"></b-spinner>
                    </b-button>
                </b-form>
                <hr>
                <b-alert v-if="success" show dismissible>
                    <i class="fa fa-check"></i>Cliente guardado
                </b-alert>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {},
                errors: {},
                success: false,
                loaded: false
            }
        },
        methods: {
            onSubmit() {
                this.loaded = true;
                this.errors = {};
                axios.post('/new_client', this.form).then(response => {
                    this.form = {};
                    this.loaded = false;
                    this.success = true;
                })
                .catch(error => {
                    this.loaded = false;
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
        }
    }
</script>