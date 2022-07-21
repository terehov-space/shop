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
        Schema::create('product_to_baskets', function (Blueprint $table) {
            $table->id();

            $table->float('price', 25, 2)->nullable();
            $table->integer('count')->nullable();

            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('basketId');

            $table->foreign('productId')->references('id')->on('products')->onDelete('CASCADE');
            $table->foreign('basketId')->references('id')->on('baskets')->onDelete('CASCADE');

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
        Schema::dropIfExists('product_to_baskets');
    }
};
