<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromRoomTypesTable extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['min_paid_adult', 'max_adult', 'max_child_age']);
        });
    }

    /**
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->integer('min_paid_adult');
            $table->integer('max_adult');
            $table->integer('max_child_age');
        });
    }
}
