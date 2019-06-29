<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave', 50)->nullable(); //No se utiliza
            $table->string('ISBN');
            $table->string('titulo');
            $table->string('autor');
            $table->string('editorial');
            $table->string('edicion');
            $table->float('costo_unitario', 8, 2);
            $table->integer('piezas')->default(0);
            $table->float('iva')->default(0); //No se utiliza
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('libros');
    }
}
