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
        </div>
        <hr>
        <b-table 
            v-if="tabla_libros" 
            id="my-table" 
            :items="libros"
            :per-page="perPage"
            :current-page="currentPage">
        </b-table>
        <b-pagination
            v-model="currentPage"
            :total-rows="libros.length"
            :per-page="perPage"
            aria-controls="my-table"
        ></b-pagination>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                libros: [],
                perPage: 10,
                currentPage: 1,
                queryTitulo: '',
                queryEditorial: '',
                tabla_libros: false,
            }
        },
        created: function(){
            this.todosLibros();
		},
        methods: {
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
            newLibro(libro){
                console.log(libro);
            }
        }
    }
</script>