<template>
    <div>
        <div class="row">
            <div class="col-md-4">
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-cliente">Libro</label>
                    </b-col>
                    <b-col sm="9">
                        <b-input
                            v-model="queryTitulo"
                            @keyup="mostrarLibros"
                        ></b-input>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-4">
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-cliente">Editorial</label>
                    </b-col>
                    <b-col sm="9">
                        <b-input
                            v-model="queryEditorial"
                            @keyup="mostrarPorEditorial"
                        ></b-input>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-4" align="right">
                <b-button 
                    variant="success" 
                    v-if="role_id == 3" 
                    v-b-modal.modal-newLibro>
                    <i class="fa fa-plus"></i> Agregar libro
                </b-button>
            </div>
        </div>
        <hr>
        <b-table 
            v-if="tabla_libros" 
            id="my-table"
            :fields="fields"
            :items="libros"
            :per-page="perPage"
            :current-page="currentPage">
            <template v-if="role_id == 3" slot="accion" slot-scope="data">
                <b-button variant="outline-warning" v-b-modal.modal-editar @click="editarLibro(data.item, data.index)">
                    <i class="fa fa-pencil"></i>
                </b-button>
                <b-button variant="outline-danger" v-b-modal.modal-eliminar @click="editarLibro(data.item, data.index)">
                    <i class="fa fa-trash"></i>
                </b-button>
            </template>
        </b-table>
        <b-pagination
            v-model="currentPage"
            :total-rows="libros.length"
            :per-page="perPage"
            aria-controls="my-table"
        ></b-pagination>

        <b-modal id="modal-newLibro" title="Agregar libro">
            <new-libro-component @actualizarLista="actLista"></new-libro-component>
            <div slot="modal-footer"></div>
        </b-modal>

        <b-modal id="modal-editar" title="Editar libro">
            <editar-libro-component :formlibro="formlibro"></editar-libro-component>
            <div slot="modal-footer"></div>
        </b-modal>

        <b-modal id="modal-eliminar" title="">
            <label>¿Seguro que deseas eliminar el libro?</label>
            <div slot="modal-footer" class="text-right">
                <b-button variant="danger" @click="eliminarLibro"><i class="fa fa-trash"></i> Eliminar</b-button>
            </div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['role_id'],
        data() {
            return {
                mostrarNewLibro: false,

                formlibro: {},
                libros: [],
                errors: {},
                posicion: 0,
                perPage: 15,
                loaded: false,
                success: false,
                currentPage: 1,
                queryTitulo: '',
                queryEditorial: '',
                tabla_libros: false,
                fields: [
                    {key:'id', label:'N.'}, 
                    'ISBN', 
                    'titulo', 
                    'editorial', 
                    'piezas', 
                    {key:'accion', label:''}]
            }
        },
        created: function(){
            this.todosLibros();
		},
        methods: {
            //Guardar un libro
            onSubmit() {

            },

            //Mostrar resultados de la busqueda por titulo del libro
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        // this.resultslibros = response.data;
                        this.inicializar(response);
                    });
                }
                else{
                    this.todosLibros();
                } 
            },
            //Mostrar libros por la editorial
            mostrarPorEditorial(){
                if(this.queryEditorial.length > 0){
                   axios.get('/mostrarPorEditorial', {params: {queryEditorial: this.queryEditorial}}).then(response => {
                        this.inicializar(response);
                    });
                }
                else{
                    this.todosLibros();
                } 
            },
            inicializar(response){
                this.libros = [];
                this.libros = response.data;
                this.tabla_libros = true;
            },
            //Mostrar resultados de libros con estado
            todosLibros(){
                axios.get('/allLibros').then(response => {
                    this.inicializar(response);
                });
            },

            editarLibro(libro, i){
                this.formlibro = libro;
                this.posicion = i;
            },
            actLista(libro){
                this.libros.push(libro);
            },
            eliminarLibro(){
                axios.delete('/eliminar_libro', {params: {id: this.formlibro.id}}).then(response => {
                    this.$bvModal.hide('modal-eliminar');
                })
                .catch(error => {
                    this.loaded = false;
                    this.$bvToast.toast('Ocurrio un error, vuelve a intentar', {
                        title: 'Error',
                        variant: 'danger',
                        solid: true
                    });
                });
            }
        }
    }
</script>