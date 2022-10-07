<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('meterman_id')->nullable();
            $table->float('prev_reading', 10, 2);
            $table->float('current_reading', 10, 2);
            $table->float('price', 10, 2);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users');
            $table->foreign('meterman_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('readings');
    }
};
