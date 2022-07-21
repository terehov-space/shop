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
        Schema::create('digitals', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->unsignedBigInteger('imageId')->nullable();
            $table->unsignedBigInteger('fileId')->nullable();
            $table->unsignedBigInteger('vendorId')->nullable();

            $table->foreign('imageId')->references('id')->on('images')->onDelete('SET NULL');
            $table->foreign('fileId')->references('id')->on('files')->onDelete('SET NULL');
            $table->foreign('vendorId')->references('id')->on('vendors')->onDelete('SET NULL');

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
        Schema::dropIfExists('digitals');
    }
};
