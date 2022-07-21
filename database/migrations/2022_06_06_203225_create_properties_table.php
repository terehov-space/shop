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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('extId')->unique()->nullable();

            $table->string('title');

            $table->string('model')->nullable();

            $table->enum('valueType', [
                'string',
                'text',
                'number',
                'float',
                'model',
            ])->default('string');

            $table->boolean('multiple')->nullable();

            $table->unsignedBigInteger('sectionId');

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
        Schema::dropIfExists('properties');
    }
};
