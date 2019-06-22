<template>
    <div>
        <b-card title="Agregar libro">
            <b-form @submit.prevent="onSubmit">
                <b-row class="my-1">
                    <label for="input-clave" class="col-md-5">Clave</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-clave"
                            v-model="form.clave"
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.clave" class="text-danger">{{ errors.clave[0] }}</div>
                    </div>
                </b-row>
                <b-row class="my-1">
                    <label for="input-titulo" class="col-md-5">Titulo</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-titulo"
                            v-model="form.titulo"
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.titulo" class="text-danger">{{ errors.titulo[0] }}</div>
                    </div>
                </b-row>
                <b-row class="my-1">
                    <label for="input-ISBN" class="col-md-5">ISBN</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-ISBN"
                            v-model="form.ISBN" 
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.ISBN" class="text-danger">{{ errors.ISBN[0] }}</div>
                    </div>
                </b-row>
                <b-row class="my-1">
                    <label for="input-autor" class="col-md-5">Autor</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-autor"
                            v-model="form.autor" 
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.autor" class="text-danger">{{ errors.autor[0] }}</div>
                    </div>
                </b-row>
                <b-row class="my-1">
                    <label for="input-editorial" class="col-md-5">Editorial</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-editorial"
                            v-model="form.editorial" 
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.editorial" class="text-danger">{{ errors.editorial[0] }}</div>
                    </div>
                </b-row>
                <b-row class="my-1">
                    <label for="input-edicion" class="col-md-5">Edición</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-edicion"
                            v-model="form.edicion" 
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.edicion" class="text-danger">{{ errors.edicion[0] }}</div>
                    </div>
                </b-row>
                <b-row class="my-1">
                    <label for="input-costo_unitario" class="col-md-5">Costo unitario</label>
                    <div class="col-md-7">
                        <b-form-input 
                            id="input-costo_unitario"
                            v-model="form.costo_unitario" 
                            type="number"
                            required>
                        </b-form-input>
                        <div v-if="errors && errors.costo_unitario" class="text-danger">{{ errors.costo_unitario[0] }}</div>
                    </div>
                </b-row>
                <b-button type="submit" variant="success"><i class="fa fa-check"></i> Guardar</b-button>
            </b-form>
            <div v-if="success" class="alert alert-success mt-3">
                <i class="fa fa-check"></i>Libro guardado
            </div>
        </b-card>
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
                    axios.post('/new_libro', this.form).then(response => {
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