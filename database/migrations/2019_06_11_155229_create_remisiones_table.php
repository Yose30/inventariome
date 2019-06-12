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
            $table->string('folio', 50)->unique();
            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->enum('tipo', ['Salida', 'Devolucion']);
            $table->double('total', 8, 2)->default(0);
            $table->date('fecha_entrega');
            $table->enum('estado', ['Iniciado', 'Proceso', 'Terminado']);
            $table->timestamps();
        });

        Schema::create('datos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('remision_id');
            $table->foreign('remision_id')->references('id')->on('remisiones');
            $table->string('clave_libro', 50);
            $table->float('costo_unitario', 8, 2);
            $table->integer('unidades');
            $table->double('total', 8, 2);
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
    }
}
