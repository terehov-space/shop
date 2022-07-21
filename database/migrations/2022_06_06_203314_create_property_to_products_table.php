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
        Schema::create('property_to_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('propertyId');
            $table->unsignedBigInteger('optionId')->nullable();
            $table->unsignedBigInteger('modelId')->nullable();
            $table->unsignedBigInteger('sectionId');

            $table->foreign('productId')->references('id')->on('products')->onDelete('CASCADE');
            $table->foreign('propertyId')->references('id')->on('properties')->onDelete('CASCADE');
            $table->foreign('optionId')->references('id')->on('property_options')->onDelete('SET NULL');
            $table->foreign('sectionId')->references('id')->on('sections')->onDelete('CASCADE');

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
        Schema::dropIfExists('property_to_products');
    }
};
