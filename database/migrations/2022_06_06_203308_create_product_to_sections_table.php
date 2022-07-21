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
        Schema::create('product_to_sections', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('productId');
            $table->unsignedBigInteger('sectionId');

            $table->foreign('productId')->references('id')->on('products')->onDelete('CASCADE');
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
        Schema::dropIfExists('product_to_sections');
    }
};
