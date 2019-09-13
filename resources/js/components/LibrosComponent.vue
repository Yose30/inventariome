<template>
    <div>
        <div class="row">
            <div class="col-md-3">
                <b-row class="my-1">
                    <b-col sm="2">
                        <label for="input-cliente">Libro</label>
                    </b-col>
                    <b-col sm="10">
                        <b-input
                            v-model="queryTitulo"
                            @keyup="mostrarLibros"
                        ></b-input>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-3">
                <b-row class="my-1">
                    <b-col sm="3">
                        <label for="input-cliente">Editorial</label>
                    </b-col>
                    <b-col sm="9">
                        <!-- <b-input
                            v-model="queryEditorial"
                            @keyup="mostrarPorEditorial"
                        ></b-input> -->
                        <b-form-select v-model="queryEditorial" :options="options" @change="mostrarPorEditorial"></b-form-select>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-3" align="right">
                <b-button 
                    variant="success" 
                    v-if="role_id == 3" 
                    v-b-modal.modal-newLibro>
                    <i class="fa fa-plus"></i> Agregar libro
                </b-button>
            </div>
            <div class="col-md-3" align="right">
                <b-button variant="info" :disabled="loadRegisters" @click="todosLibros">
                    <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                </b-button>
                <hr>
                <b-button variant="primary" :disabled="loadRegisters" href="/downloadExcel">
                    <b-spinner small v-if="loadRegisters"></b-spinner> Descargar Excel
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
            <template  slot="piezas" slot-scope="data">
                {{ data.item.piezas | formatNumber }}
            </template>
            <template v-if="role_id == 3" slot="accion" slot-scope="data">
                <b-button variant="outline-warning" v-b-modal.modal-editar @click="editarLibro(data.item, data.index)">
                    <i class="fa fa-pencil"></i>
                </b-button>
                <!-- <b-button variant="outline-danger" v-b-modal.modal-eliminar @click="editarLibro(data.item, data.index)">
                    <i class="fa fa-trash"></i>
                </b-button> -->
            </template>
        </b-table>
        <b-pagination
            v-if="tabla_libros"
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
                queryEditorial: null,
                tabla_libros: false,
                fields: [
                    {key:'id', label:'N.'}, 
                    'ISBN', 
                    'titulo', 
                    'editorial', 
                    'piezas', 
                    {key:'accion', label:''}
                ],
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
                    { value: 'BOOKMART MÉXICO', text: 'BOOKMART MÉXICO' },
                    { value: 'ANGLO PUBLISHING', text: 'ANGLO PUBLISHING' },
                    { value: 'LAROUSSE', text: 'LAROUSSE' },
                    { value: '', text: 'TODOS LOS LIBROS'},
                ],
                loadRegisters: false
            }
        },
        // created: function(){
        //     this.todosLibros();
        // },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            //Mostrar resultados de la busqueda por titulo del libro
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.inicializar(response);
                    });
                }
                // else{
                //     this.todosLibros();
                // } 
            },
            //Mostrar libros por la editorial
            mostrarPorEditorial(){
                if(this.queryEditorial.length > 0){
                   axios.get('/mostrarPorEditorial', {params: {queryEditorial: this.queryEditorial}}).then(response => {
                        this.inicializar(response);
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
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
                this.loadRegisters = true;
                axios.get('/allLibros').then(response => {
                    this.inicializar(response);
                    this.loadRegisters = false;
                }).catch(error => {
                    this.loadRegisters = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
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
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            makeToast(variante, descripcion){
                this.$bvToast.toast('Ocurrio un error, vuelve a intentar', {
                    title: 'Error',
                    variant: 'danger',
                    solid: true
                });
            }
        }
    }
</script>