<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('code', 10);
            $table->string('remark', 255);
            $table->integer('quota');
            $table->boolean('on_request');
            $table->integer('min_paid_adult');
            $table->integer('max_adult');
            $table->integer('max_child_age');
            $table->text('description');
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('room_types');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
