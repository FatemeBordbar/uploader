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
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('caption', 4000);
            $table->string('path', 1000);
            $table->string('extensions', 1000);
            $table->string('hash', 3000);
            $table->string('original_name', 1000)->nullable();
            $table->integer('size')->default('0');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('entity_type')->unsigned()->nullable();
            $table->foreign('entity_type')->references('id')->on('entities')->onDelete('set null');
            $table->integer('entity_id')->unsigned()->nullable();
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
        Schema::dropIfExists('files');
    }
};
