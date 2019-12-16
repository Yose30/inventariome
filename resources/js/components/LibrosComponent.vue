<template>
    <div>
        <check-connection-component></check-connection-component>
        <div class="row">
            <div class="col-md-4">
                <!-- BUSCAR LIBRO POR TITULO -->
                <b-row>
                    <b-col sm="2"><label>Titulo</label></b-col>
                    <b-col sm="10">
                        <b-input
                            style="text-transform:uppercase;"
                            v-model="queryTitulo"
                            @keyup="mostrarLibros()"
                        ></b-input>
                    </b-col>
                </b-row>
            </div>
            <div class="col-md-4">
                <!-- BUSCAR LIBRO POR ISBN -->
                <b-row>
                    <b-col sm="2">
                        <label>ISBN</label>
                    </b-col>
                    <b-col sm="10">
                        <b-input
                            v-model="isbn"
                            @keyup.enter="buscarLibroISBN()">
                        </b-input>
                    </b-col>
                </b-row>
            </div>
            <!-- BUSCAR LIBROS POR EDITORIAL -->
            <div class="col-md-4">
                <b-row>
                    <b-col sm="2">
                        <label for="input-cliente">Editorial</label>
                    </b-col>
                    <b-col sm="10">
                        <b-form-select v-model="queryEditorial" :options="options" @change="mostrarPorEditorial()"></b-form-select>
                    </b-col>
                </b-row>
            </div>
        </div>
        <hr>
        <b-row>
            <b-col>
                <!-- PAGINACIÓN -->
                <b-pagination
                    v-model="currentPage"
                    :total-rows="libros.length"
                    :per-page="perPage"
                    aria-controls="my-table"
                ></b-pagination>
            </b-col>
            <!-- <b-col>
                <b-button variant="info" pill v-b-modal.modal-ayudaL><i class="fa fa-info-circle"></i> Ayuda</b-button>
            </b-col> -->
            <b-col class="text-right">
                <!-- DESCARGAR LIBROS downloadExcel -->
                <b-button variant="dark" :href="'/downloadExcel/' + queryEditorial"> 
                    <i class="fa fa-download"></i> Descargar lista
                </b-button>
            </b-col>
            <b-col sm="3" class="text-right">
                <!-- AGREGAR UN NUEVO LIBRO -->
                <b-button 
                    variant="success" 
                    v-if="role_id === 3" 
                    v-b-modal.modal-newLibro>
                    <i class="fa fa-plus"></i> Nuevo libro
                </b-button>
            </b-col>
        </b-row>
        <!-- LISTADO DE LIBROS -->
        <b-table  
            responsive
            id="my-table"
            :fields="fields"
            :items="libros"
            :per-page="perPage"
            :current-page="currentPage">
            <template  slot="piezas" slot-scope="data">
                {{ data.item.piezas | formatNumber }}
            </template>
            <template slot="accion" slot-scope="data">
                <b-button v-if="role_id == 3" style="color:white;" variant="warning" v-b-modal.modal-editar @click="editarLibro(data.item, data.index)">
                    <i class="fa fa-pencil"></i>
                </b-button>
            </template>
        </b-table>

        <!-- MODAL PARA AGREGAR UN LIBRO -->
        <b-modal id="modal-newLibro" title="Nuevo libro">
            <new-libro-component @actualizarLista="actLista" :listEditoriales="listEditoriales"></new-libro-component>
            <div slot="modal-footer"></div>
        </b-modal>
        <!-- MODAL PARA EDITAR UN LIBRO -->
        <b-modal id="modal-editar" title="Editar libro">
            <editar-libro-component :formlibro="formlibro" :listEditoriales="listEditoriales" @actualizarLibro="libroModificado"></editar-libro-component>
            <div slot="modal-footer"></div>
        </b-modal>
        <!-- MODAL DE AYUDA GRAL-->
        <b-modal id="modal-ayudaL" hide-backdrop hide-footer title="Ayuda">
            <h5 id="titleA"><b>Búsqueda por titulo</b></h5>
            <p>Escribir el título del libro y aparecerán las coincidencias conforme vaya escribiendo.</p>
            <h5 id="titleA"><b>Búsqueda por ISBN</b></h5>
            <p>Escribir el ISBN (completo) y presionar <label id="ctrlS">Enter</label>.</p>
            <h5 id="titleA"><b>Búsqueda por editorial</b></h5>
            <p>Elegir la editorial que desee y aparecerán todos los libros relacionados a esta.</p>
            <h5 id="titleA"><b>Descargar lista completa</b></h5>
            <p>
                Si la opción de TODOS LOS LIBROS esta activa en la búsqueda por editorial, se descargará la lista completa de libros en formato EXCEL.
            </p>
            <h5 id="titleA"><b>Descargar lista por editorial</b></h5>
            <p>
                Si alguna editorial esta activa en la búsqueda por editorial se descargará la lista de libros relacionados a esta en formato EXCEL.
            </p>
            <div v-if="role_id === 3">
                <h5 id="titleA"><b>Nuevo libro</b></h5>
                <p>Puede agregar un libro proporcionando el Titulo, ISBN, Autor y Editorial.</p>
                <h5 id="titleA"><b>Editar libro</b></h5>
                <p>Puede modificar Titulo, ISBN, Autor o Editorial de cualquier libro.</p>
                <p>
                    <b><i class="fa fa-info-circle"></i> Nota:</b>
                    <ul>
                        <li>Todos los campos son obligatorios, excepto el autor.</li>
                        <li>El titulo e ISBN son únicos, es decir no se pueden agregar los mismos datos de un libro ya existente.</li>
                    </ul>
                </p>
            </div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['role_id', 'registersall', 'editoriales'],
        data() {
            return {
                formlibro: {},
                libros: this.registersall,
                errors: {},
                posicion: 0,
                perPage: 10,
                loaded: false,
                success: false,
                currentPage: 1,
                queryTitulo: '',
                queryEditorial: 'TODO',
                fields: [
                    'ISBN', 
                    'titulo', 
                    'editorial', 
                    'piezas', 
                    {key:'accion', label:''}
                ],
                options: [],
                listEditoriales: [],
                loadRegisters: false,
                isbn: '',
                libro: {}
            }
        },
        created: function(){
            this.assign_editorial();
        },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        methods: {
            assign_editorial(){
                this.options.push({
                    value: 'TODO',
                    text: 'MOSTRAR TODO'
                });
                this.editoriales.forEach(editorial => {
                    this.options.push({
                        value: editorial.editorial,
                        text: editorial.editorial
                    });
                });

                this.editoriales.forEach(editorial => {
                    this.listEditoriales.push({
                        value: editorial.editorial,
                        text: editorial.editorial
                    });
                });
            },
            // MOSTRAR LIBROS POR COINCIDENCIA DE TITULO
            mostrarLibros(){
                axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                    this.libros = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // BUSCAR LIBRO POR ISBN
            buscarLibroISBN () {
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.libros = [];
                    this.libro = response.data;
                    this.libros.push(this.libro);  
                }).catch(error => {
                   this.makeToast('danger', 'ISBN incorrecto');
                });
            },
            // MOSTRAR LIBROS POR EDITORIAL
            mostrarPorEditorial(){
                axios.get('/mostrarPorEditorial', {params: {queryEditorial: this.queryEditorial}}).then(response => {
                    this.libros = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // INICIALIZAR PARA EDITAR LIBRO
            editarLibro(libro, i){
                this.formlibro = libro;
                this.posicion = i;
            },
            // AGREGAR LIBRO AL LISTADO (EVENTO)
            actLista(libro){
                this.libros.unshift(libro);
                this.$bvModal.hide('modal-newLibro');
                this.makeToast('success', 'El libro de agrego correctamente.');
            },
            libroModificado(libro){
                this.$bvModal.hide('modal-editar');
                this.libros[this.posicion].ISBN = libro.ISBN;
                this.libros[this.posicion].titulo = libro.titulo;
                this.libros[this.posicion].editorial = libro.editorial;
                this.makeToast('success', 'El libro se modifico correctamente.');
            },
            // ELIMINAR LIBRO (FUNCIÓN NO UTILIZADA)
            eliminarLibro(){
                axios.delete('/eliminar_libro', {params: {id: this.formlibro.id}}).then(response => {
                    this.$bvModal.hide('modal-eliminar');
                })
                .catch(error => {
                    this.loaded = false;
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            makeToast(variant = null, descripcion) {
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variant,
                    solid: true
                })
            }
        }
    }
</script>