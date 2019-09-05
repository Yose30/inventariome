<template>
    <div>
        <b-tabs content-class="mt-3" fill>
            <b-tab title="Remisiones" active>
                <listado-component :role_id="role_id"></listado-component>
            </b-tab>
            <b-tab title="Crear remisión">
                <remision-component></remision-component>
            </b-tab>
            <b-tab title="Pagos">
                <pagos-component :role_id="role_id"></pagos-component>
            </b-tab>
            <b-tab title="Clientes">
                <clientes-component :role_id="role_id"></clientes-component>
            </b-tab>
            <b-tab title="Libros">
                <libros-component :role_id="role_id"></libros-component>
            </b-tab>
            <b-tab title="Adeudos">
                <adeudos-component :role_id="role_id"></adeudos-component>
            </b-tab>
            <b-tab title="Entradas">
                <editar-entradas-component :role_id="role_id"></editar-entradas-component>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                
            }
        },
        methods: {
            
        }
    }
</script>
