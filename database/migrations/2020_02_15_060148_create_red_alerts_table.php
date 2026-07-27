<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('red_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sheet_id')->nullable();
            $table->integer('parameter_id')->nullable();
            $table->integer('sub_parameter_id')->nullable();
            $table->text('message')->nullable();
            $table->text('file')->nullable();
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
        Schema::dropIfExists('red_alerts');
    }
}
