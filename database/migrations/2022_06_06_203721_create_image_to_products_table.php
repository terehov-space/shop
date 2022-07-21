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
        Schema::create('image_to_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('imageId');

            $table->foreign('productId')->references('id')->on('products')->onDelete('CASCADE');
            $table->foreign('imageId')->references('id')->on('images')->onDelete('CASCADE');

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
        Schema::dropIfExists('image_to_products');
    }
};
