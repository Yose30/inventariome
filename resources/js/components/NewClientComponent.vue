<template>
    <div>
        <b-form @submit.prevent="onSubmit">
            <b-form-group id="input-group-name" label="Nombre" label-for="input-name">
                <b-form-input
                id="input-name"
                v-model="form.name"
                required
                ></b-form-input>
                <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-last_name" label="Apellidos" label-for="input-last_name">
                <b-form-input
                id="input-last_name"
                v-model="form.last_name"
                required
                ></b-form-input>
                <div v-if="errors && errors.last_name" class="text-danger">{{ errors.last_name[0] }}</div>
            </b-form-group>
            <b-form-group
            id="input-group-email"
            label="Correo electrónico"
            label-for="input-email"
            >
                <b-form-input
                id="input-email"
                v-model="form.email"
                type="email"
                required
                ></b-form-input>
                <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-telefono" label="Telefono" label-for="input-telefono">
                <b-form-input
                id="input-telefono"
                v-model="form.telefono" 
                required
                ></b-form-input>
                <div v-if="errors && errors.telefono" class="text-danger">{{ errors.telefono[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-direccion" label="Dirección" label-for="input-direccion">
                <b-form-input
                id="input-direccion"
                v-model="form.direccion" 
                required
                ></b-form-input>
                <div v-if="errors && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-descuento" label="Descuento" label-for="input-descuento">
                <b-form-input
                id="input-descuento"
                v-model="form.descuento" 
                required
                ></b-form-input>
                <div v-if="errors && errors.descuento" class="text-danger">{{ errors.descuento[0] }}</div>
            </b-form-group>
            <b-form-group id="input-group-condiciones_pago" label="Condiciones de pago" label-for="input-condiciones_pago">
                <b-form-input
                id="input-condiciones_pago"
                v-model="form.condiciones_pago" 
                required
                ></b-form-input>
                <div v-if="errors && errors.condiciones_pago" class="text-danger">{{ errors.condiciones_pago[0] }}</div>
            </b-form-group>
            <b-button type="submit" variant="primary">Guardar</b-button>
        </b-form>
        <div v-if="success" class="alert alert-success mt-3">
            <i class="fa fa-check"></i>Cliente guardado
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