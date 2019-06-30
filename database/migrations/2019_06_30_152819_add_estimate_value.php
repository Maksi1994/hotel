<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEstimateValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->bigInteger('value');
            $table->bigInteger('guest_id')->unsigned();
            $table->foreign('guest_id')->references('id')->on('guests')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropForeign('estimates_guest_id_foreign');
            $table->dropColumn(['value', 'guest_id']);
        });
    }
}
