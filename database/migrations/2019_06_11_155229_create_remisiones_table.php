<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remisiones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->double('total', 8, 2)->default(0);
            $table->double('total_devolucion', 8, 2)->default(0);
            $table->double('total_pagar', 8, 2)->default(0);
            $table->date('fecha_entrega');
            $table->enum('estado', ['Iniciado', 'Proceso', 'Terminado']);
            $table->date('fecha_creacion');
            $table->date('fecha_devolucion');
            $table->timestamps();
        }); 

        Schema::create('datos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('remision_id')->nullable();
            $table->foreign('remision_id')->references('id')->on('remisiones');
            $table->unsignedInteger('libro_id')->nullable();
            $table->foreign('libro_id')->references('id')->on('libros');
            $table->float('costo_unitario', 8, 2);
            $table->integer('unidades')->default(0);
            $table->double('total', 8, 2)->default(0);
            $table->enum('estado', ['Iniciado', 'Eliminado', 'Terminado'])->default('Iniciado');
            $table->timestamps();
        });

        Schema::create('devoluciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('remision_id')->nullable();
            $table->foreign('remision_id')->references('id')->on('remisiones');
            $table->unsignedInteger('dato_id')->nullable();
            $table->foreign('dato_id')->references('id')->on('datos');
            $table->unsignedInteger('libro_id')->nullable();
            $table->foreign('libro_id')->references('id')->on('libros');
            $table->integer('unidades')->default(0);
            $table->integer('unidades_resta')->default(0);
            $table->double('total', 8, 2)->default(0);
            $table->double('total_resta', 8, 2)->default(0);
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
        Schema::dropIfExists('remisiones');
        Schema::dropIfExists('datos');
        Schema::dropIfExists('devoluciones');
    }
}
