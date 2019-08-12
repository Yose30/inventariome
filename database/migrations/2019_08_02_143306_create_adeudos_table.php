<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdeudosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adeudos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->string('remision_num')->nullable();
            $table->date('fecha_adeudo')->nullable();
            $table->double('total_adeudo', 16, 2)->default(0);
            $table->double('total_abonos', 16, 2)->default(0);
            $table->double('total_pendiente', 16, 2)->default(0);
            $table->double('total_devolucion', 16, 2)->default(0);
            $table->timestamps(); 
        });

        Schema::create('abonos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('adeudo_id');
            $table->foreign('adeudo_id')->references('id')->on('adeudos');
            $table->double('pago', 16, 2)->default(0);
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
        Schema::dropIfExists('adeudos');
        Schema::dropIfExists('abonos');
    }
}
