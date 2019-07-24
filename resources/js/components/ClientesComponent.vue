<template>
    <div>
        <b-table :items="clientes" :fields="fields">
            <!-- <template slot="editar" slot-scope="row">
                <b-button variant="outline-warning"><i class="fa fa-pencil"></i></b-button>
            </template> -->
        </b-table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
               clientes: [],
               fields: [
                   {key: 'id', label: 'N.'},
                   {key: 'name', label: 'Nombre'},
                   {key: 'email', label: 'Correo'},
                   {key: 'telefono', label: 'Teléfono'},
                   {key: 'direccion', label: 'Dirección'},
                   {key: 'condiciones_pago', label: 'Condiciones de pago'},
                //    'editar'
                ]
            }
        },
        created: function(){
			this.getTodo();
		},
        methods: {
            getTodo(){
                axios.get('/getTodo').then(response => {
                    this.clientes = response.data;
                });
            },
        }
    }
</script>