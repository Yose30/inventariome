<template>
    <div align="center">
        <h4>Agregar libro</h4>
        <hr>
        <div class="card col-md-8">
            <div class="card-body">
                <b-form @submit.prevent="onSubmit">
                    <b-row class="my-1">
                        <label align="right" class="col-md-5">Titulo</label>
                        <div class="col-md-7">
                            <b-form-input 
                                v-model="form.titulo"
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.titulo" class="text-danger">{{ errors.titulo[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" class="col-md-5">ISBN</label>
                        <div class="col-md-7">
                            <b-form-input 
                                v-model="form.ISBN" 
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.ISBN" class="text-danger">{{ errors.ISBN[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" class="col-md-5">Autor</label>
                        <div class="col-md-7">
                            <b-form-input 
                                :disabled="loaded"
                                v-model="form.autor">
                            </b-form-input>
                            <div v-if="errors && errors.autor" class="text-danger">{{ errors.autor[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" class="col-md-5">Editorial</label>
                        <div class="col-md-7">
                            <b-form-input 
                                v-model="form.editorial" 
                                :disabled="loaded"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.editorial" class="text-danger">{{ errors.editorial[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" class="col-md-5">Edición</label>
                        <div class="col-md-7">
                            <b-form-input 
                                :disabled="loaded"
                                v-model="form.edicion">
                            </b-form-input>
                            <div v-if="errors && errors.edicion" class="text-danger">{{ errors.edicion[0] }}</div>
                        </div>
                    </b-row>
                    <b-row class="my-1">
                        <label align="right" class="col-md-5">Costo unitario</label>
                        <div class="col-md-7">
                            <b-form-input 
                                v-model="form.costo_unitario" 
                                :disabled="loaded"
                                type="number"
                                required>
                            </b-form-input>
                            <div v-if="errors && errors.costo_unitario" class="text-danger">{{ errors.costo_unitario[0] }}</div>
                        </div>
                    </b-row>
                    <hr>
                    <b-button type="submit" :disabled="loaded" variant="success">
                        <i class="fa fa-check"></i> {{ !loaded ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="loaded"></b-spinner>
                    </b-button>
                </b-form>
                <hr>
                <b-alert v-if="success" show dismissible>
                    <i class="fa fa-check"></i>Libro guardado
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
                this.success = false;
                this.errors = {};
                axios.post('/new_libro', this.form).then(response => {
                    this.form = {};
                    this.loaded = false;
                    this.success = true;
                })
                .catch(error => {
                    this.errors = {};
                    this.loaded = false;
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
        }
    }
</script>
