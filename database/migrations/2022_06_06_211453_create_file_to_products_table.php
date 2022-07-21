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
        Schema::create('file_to_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('fileId');

            $table->foreign('productId')->references('id')->on('products')->onDelete('CASCADE');
            $table->foreign('fileId')->references('id')->on('files')->onDelete('CASCADE');

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
        Schema::dropIfExists('file_to_products');
    }
};
