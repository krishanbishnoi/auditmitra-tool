<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeatPlanSubPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beat_plan_sub_parts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date',255)->nullable();
            $table->bigInteger('beat_id', false, true)->nullable();
            $table->bigInteger('branch_id', false, true)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::table('beat_plans', function ($table) {
            // $table->foreign('beat_id')->references('id')->on('beat_plans')
            // ->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('branch_id')->references('id')->on('branches')
            // ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beat_plan_sub_parts');
    }
}
