<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInRedAlerts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('red_alerts', function (Blueprint $table) {
            //
            $table->string('lob',255)->nullable();
            $table->string('type',255)->nullable();
            $table->string('type_id',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('red_alerts', function (Blueprint $table) {
            //
        });
    }
}
