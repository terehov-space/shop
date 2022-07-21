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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('title');

            $table->longText('body');

            $table->string('seoTitlePostfix')->nullable();
            $table->string('seoDescription')->nullable();

            $table->boolean('openLink')->nullable();

            $table->unsignedBigInteger('pageId')->nullable();

            $table->foreign('pageId')->references('id')->on('pages')->onDelete('CASCADE');

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
        Schema::dropIfExists('pages');
    }
};
