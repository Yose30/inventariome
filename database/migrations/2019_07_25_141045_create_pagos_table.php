<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vendidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('remisione_id')->nullable();
            $table->foreign('remisione_id')->references('id')->on('remisiones');
            $table->unsignedInteger('dato_id')->nullable();
            $table->foreign('dato_id')->references('id')->on('datos');
            $table->unsignedInteger('libro_id')->nullable();
            $table->foreign('libro_id')->references('id')->on('libros');
            $table->integer('unidades')->default(0);
            $table->double('total', 12, 2)->default(0);
            $table->integer('unidades_resta')->default(0);
            $table->double('total_resta', 12, 2)->default(0);
            $table->integer('unidades_base')->default(0);
            $table->double('total_base', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('vendido_id');
            $table->foreign('vendido_id')->references('id')->on('vendidos');
            $table->integer('unidades')->default(0);
            $table->double('pago', 12, 2)->default(0);
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
        Schema::dropIfExists('vendidos');
        Schema::dropIfExists('pagos');
    }
}
