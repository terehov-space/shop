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
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->string('model')->nullable();
            $table->unsignedBigInteger('modelId')->nullable();

            $table->unsignedBigInteger('imageId')->nullable();
            $table->unsignedBigInteger('mobileImage')->nullable();

            $table->foreign('imageId')->references('id')->on('images')->onDelete('SET NULL');
            $table->foreign('mobileImage')->references('id')->on('images')->onDelete('SET NULL');

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
        Schema::dropIfExists('carousels');
    }
};
