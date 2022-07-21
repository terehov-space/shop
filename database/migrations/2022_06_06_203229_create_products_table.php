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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('extId')->unique()->nullable();
            $table->string('bitrixExtId')->unique()->nullable();
            $table->string('vendorCode')->nullable();

            $table->string('title');
            $table->longText('description')->nullable();

            $table->float('price', 25, 2)->nullable();
            $table->float('priceEur', 25, 2)->nullable();
            $table->float('priceUsd', 25, 2)->nullable();

            $table->boolean('updateEur')->nullable();
            $table->boolean('updateUsd')->nullable();

            $table->boolean('active')->nullable();
            $table->boolean('showMain')->nullable();
            $table->boolean('showPrice')->nullable();

            $table->string('seoTitle')->nullable();
            $table->text('seoDescription')->nullable();

            $table->integer('sort')->nullable();

            $table->unsignedBigInteger('sectionId')->nullable();
            $table->unsignedBigInteger('imageId')->nullable();

            $table->foreign('sectionId')->references('id')->on('sections')->onDelete('SET NULL');
            $table->foreign('imageId')->references('id')->on('images')->onDelete('SET NULL');

            $table->softDeletes();

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
        Schema::dropIfExists('products');
    }
};
