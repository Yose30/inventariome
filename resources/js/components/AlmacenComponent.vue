<template>
    <div>
        <b-tabs content-class="mt-3" fill>
            <b-tab title="Remisiones" active>
                <remisiones-component></remisiones-component>
            </b-tab>
            <!-- <b-tab title="Vendidos">
                <vendidos-component></vendidos-component>
            </b-tab> -->
            <b-tab title="Pagos">
                <pagos-component :role_id="role_id"></pagos-component>
            </b-tab>
            <b-tab title="Devoluciones">
                <devolucion-component></devolucion-component>
            </b-tab>
            <b-tab title="Notas">
                <new-nota-component :role_id="role_id"></new-nota-component>
            </b-tab>
            <b-tab title="Promociones">
                <promociones-component :role_id="role_id"></promociones-component>
            </b-tab>
            <b-tab title="Adeudos">
                <devolucion-adeudos-component :role_id="role_id"></devolucion-adeudos-component>
            </b-tab>
            <b-tab title="Entradas">
                <entradas-component :role_id="role_id"></entradas-component>
            </b-tab>
            <b-tab title="Libros">
                <libros-component :role_id="role_id"></libros-component>
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
