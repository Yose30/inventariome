<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cliente');
            $table->double('total_salida', 8, 2)->default(0);
            $table->double('total_devolucion', 8, 2)->default(0);
            $table->double('total_pagar', 8, 2)->default(0);
            $table->double('pagos', 8, 2)->default(0);
            $table->date('fecha_devolucion')->nullable();
            $table->timestamps();
        }); 

        Schema::create('registers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('note_id')->nullable();
            $table->foreign('note_id')->references('id')->on('notes');
            $table->unsignedInteger('libro_id')->nullable();
            $table->foreign('libro_id')->references('id')->on('libros');
            $table->float('costo_unitario', 8, 2);
            $table->integer('unidades')->default(0);
            $table->double('total', 8, 2)->default(0);
            $table->integer('unidades_pagado')->default(0);
            $table->double('total_pagado', 8, 2)->default(0);
            $table->integer('unidades_pendiente')->default(0);
            $table->double('total_pendiente', 8, 2)->default(0);
            $table->integer('unidades_base')->default(0);
            $table->double('total_base', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('register_id');
            $table->foreign('register_id')->references('id')->on('registers');
            $table->integer('unidades')->default(0);
            $table->double('pago', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('repayments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('note_id')->nullable();
            $table->foreign('note_id')->references('id')->on('notes');
            $table->unsignedInteger('register_id');
            $table->foreign('register_id')->references('id')->on('registers');
            $table->integer('unidades')->default(0);
            $table->double('total', 8, 2)->default(0);
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
        Schema::dropIfExists('notes');
        Schema::dropIfExists('registers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('repayments');
    }
}
