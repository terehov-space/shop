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
        Schema::create('property_options', function (Blueprint $table) {
            $table->id();

            $table->string('stringVal')->nullable();
            $table->text('textVal')->nullable();
            $table->bigInteger('numberVal')->nullable();
            $table->float('floatVal', 25, 2)->nullable();

            $table->unsignedBigInteger('propertyId');
            $table->unsignedBigInteger('sectionId');

            $table->foreign('propertyId')->references('id')->on('properties')->onDelete('CASCADE');
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
        Schema::dropIfExists('property_options');
    }
};
