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
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" for="input-descuento" class="col-md-5">Descuento</label>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-descuento"
                                v-model="form.descuento" 
                                type="number"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.descuento" class="text-danger">{{ errors.descuento[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" for="input-condiciones_pago" class="col-md-5">Condiciones de pago</label>
                        <div class="col-md-7">
                            <b-form-input 
                                id="input-condiciones_pago"
                                v-model="form.condiciones_pago" 
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.condiciones_pago" class="text-danger">{{ errors.condiciones_pago[0] }}</div>
                        </div>
                    </b-row>
                    <hr>
                    <b-button type="submit" variant="success"><i class="fa fa-check"></i> Guardar</b-button>
                </b-form>
                <div v-if="success" class="alert alert-success mt-3">
                    <i class="fa fa-check"></i>Cliente guardado
                </div>
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
                loaded: true
            }
        },
        methods: {
            onSubmit(evt) {
                if (this.loaded) {
                    this.loaded = false;
                    this.success = false;
                    this.errors = {};
                    console.log(this.form);
                    axios.post('/new_client', this.form).then(response => {
                        console.log(response);
                        this.form = {};
                        this.loaded = true;
                        this.success = true;
                    })
                    .catch(error => {
                        this.loaded = true;
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
                }
            },
        }
    }
</script>