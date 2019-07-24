<template>
    <div> 
        <b-form @submit.prevent="onUpdate">
            <b-row class="my-1">
                <b-col sm="3">
                    <label>Contraseña</label>
                </b-col>
                <b-col sm="9">
                    <b-form-input type="password"></b-form-input>
                </b-col>
            </b-row>
            <b-row class="my-1">
                <b-col sm="3">
                    <label>Confirmar contraseña</label>
                </b-col>
                <b-col sm="9">
                    <b-form-input type="password"></b-form-input>
                </b-col>
            </b-row>
        </b-form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
               
            }
        },
        
        methods: {
            update(){

            }
        }
    }
</script>