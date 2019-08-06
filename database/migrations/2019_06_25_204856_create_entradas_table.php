<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntradasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio', 50)->unique();
            $table->string('editorial')->nullable();
            $table->integer('unidades')->default(0);
            $table->double('total', 12, 2)->default(0);
            $table->timestamps();
        }); 

        Schema::create('registros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('entrada_id')->nullable();
            $table->foreign('entrada_id')->references('id')->on('entradas');
            $table->unsignedInteger('libro_id')->nullable();
            $table->foreign('libro_id')->references('id')->on('libros');
            $table->float('costo_unitario', 8, 2)->default(0);
            $table->integer('unidades')->default(0);
            $table->double('total', 8, 2)->default(0);
            $table->enum('estado', ['Iniciado', 'Eliminado', 'Terminado'])->default('Iniciado');
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
        Schema::dropIfExists('entradas');
        Schema::dropIfExists('registros');
    }
}
