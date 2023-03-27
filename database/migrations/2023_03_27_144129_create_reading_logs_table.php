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
        Schema::create('reading_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('changer_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->string('message');
            $table->timestamps();

            $table->foreign('changer_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reading_logs');
    }
};
