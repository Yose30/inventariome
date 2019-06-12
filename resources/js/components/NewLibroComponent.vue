<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group id="input-group-titulo" label="Titulo" label-for="input-titulo">
                <b-form-input
                id="input-titulo"
                v-model="form.titulo"
                required
                ></b-form-input>
                <div v-if="errors && errors.titulo" class="text-danger">{{ errors.titulo[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-ISBN" label="ISBN" label-for="input-ISBN">
                <b-form-input
                id="input-ISBN"
                v-model="form.ISBN"
                required
                ></b-form-input>
                <div v-if="errors && errors.ISBN" class="text-danger">{{ errors.ISBN[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-autor" label="Autor" label-for="input-autor">
                <b-form-input
                id="input-autor"
                v-model="form.autor" 
                required
                ></b-form-input>
                <div v-if="errors && errors.autor" class="text-danger">{{ errors.autor[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-editorial" label="Editorial" label-for="input-editorial">
                <b-form-input
                id="input-editorial"
                v-model="form.editorial" 
                required
                ></b-form-input>
                <div v-if="errors && errors.editorial" class="text-danger">{{ errors.editorial[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-edicion" label="Edición" label-for="input-edicion">
                <b-form-input
                id="input-edicion"
                v-model="form.edicion" 
                required
                ></b-form-input>
                <div v-if="errors && errors.edicion" class="text-danger">{{ errors.edicion[0] }}</div>
            </b-form-group>
            <b-button type="submit" variant="primary">Guardar</b-button>
        </b-form>
        <div v-if="success" class="alert alert-success mt-3">
            <i class="fa fa-check"></i>Libro guardado
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