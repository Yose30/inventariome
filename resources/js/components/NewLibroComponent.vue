<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-row class="my-1">
                <label align="right" class="col-md-3">Titulo</label>
                <div class="col-md-9">
                    <b-form-input 
                        v-model="form.titulo"
                        :disabled="loaded"
                        required>
                    </b-form-input>
                    <div v-if="errors && errors.titulo" class="text-danger">{{ errors.titulo[0] }}</div>
                </div>
            </b-row>
            <b-row class="my-1">
                <label align="right" class="col-md-3">ISBN</label>
                <div class="col-md-9">
                    <b-form-input 
                        v-model="form.ISBN" 
                        :disabled="loaded"
                        required>
                    </b-form-input>
                    <div v-if="errors && errors.ISBN" class="text-danger">{{ errors.ISBN[0] }}</div>
                </div>
            </b-row>
            <b-row class="my-1">
                <label align="right" class="col-md-3">Autor</label>
                <div class="col-md-9">
                    <b-form-input 
                        :disabled="loaded"
                        v-model="form.autor">
                    </b-form-input>
                    <div v-if="errors && errors.autor" class="text-danger">{{ errors.autor[0] }}</div>
                </div>
            </b-row>
            <b-row class="my-1">
                <label align="right" class="col-md-3">Editorial</label>
                <div class="col-md-9">
                    <!-- <b-form-input 
                        v-model="form.editorial" 
                        :disabled="loaded"
                        required>
                    </b-form-input> -->
                    <b-form-select v-model="form.editorial" :disabled="loaded" :options="options" required></b-form-select>
                    <div v-if="errors && errors.editorial" class="text-danger">{{ errors.editorial[0] }}</div>
                </div>
            </b-row>
            <hr>
            <div class="text-right">
                <b-button type="submit" :disabled="loaded" variant="success">
                    <i class="fa fa-check"></i> {{ !loaded ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="loaded"></b-spinner>
                </b-button>
            </div>
        </b-form>
        <hr>
        <b-alert v-if="success" show dismissible>
            <i class="fa fa-check"></i>Libro guardado
        </b-alert>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {},
                errors: {},
                success: false,
                loaded: false,
                options: [
                    { value: null, text: 'Selecciona una opción', disabled: true },
                    { value: 'CAMBRIDGE', text: 'CAMBRIDGE' },
                    { value: 'CENGAGE', text: 'CENGAGE' },
                    { value: 'EMPRESER', text: 'EMPRESER' },
                    { value: 'EXPRESS PUBLISHING', text: 'EXPRESS PUBLISHING'},
                    { value: 'HELBLING LANGUAGES', text: 'HELBLING LANGUAGES'},
                    { value: 'MAJESTIC', text: 'MAJESTIC'},
                    { value: 'MC GRAW - MAJESTIC', text: 'MC GRAW - MAJESTIC'},
                    { value: 'MCGRAW HILL', text: 'MCGRAW HILL'},
                    { value: 'RICHMOND', text: 'RICHMOND'},
                    { value: 'IMPRESOS DE CALIDAD', text: 'IMPRESOS DE CALIDAD'},
                    { value: 'ENGLISH TEXBOOK', text: 'ENGLISH TEXBOOK'},
                ],
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
                    this.$emit('actualizarLista', response.data);
                })
                .catch(error => {
                    this.errors = {};
                    this.loaded = false;
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                    else{
                        this.$bvToast.toast('Ocurrio un problema, vuelve a intentar o actualiza la pagina', {
                            title: 'Mensaje',
                            variant: 'danger',
                            solid: true
                        });
                    }
                });
            },
        }
    }
</script>
