<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRoomsTable extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('min_paid_adult')->after('room_type_id');
            $table->integer('max_adult')->after('min_paid_adult');
            $table->integer('max_child_age')->after('max_adult');
        });
    }

    /**
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['min_paid_adult', 'max_adult', 'max_child_age']);
        });
    }
}
