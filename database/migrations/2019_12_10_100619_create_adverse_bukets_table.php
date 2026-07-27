<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdverseBuketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverse_bukets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("AGRMNTID",255)->nullable();
            $table->string("month",150)->nullable();
            $table->string("prev_month1",150)->nullable();
            $table->string("prev_month2",150)->nullable();
            $table->string("PRODUCTFLAG",100)->nullable();
            $table->string("PRODUCTFLAG_Q",100)->nullable();
            $table->string("BRANCH",100)->nullable();
            $table->string("prev_month2_BOM_BUCKET",50)->nullable();
            $table->string("prev_month1_BOM_BUCKET",50)->nullable();
            $table->string("month_BOM_BUCKET",50)->nullable();
            $table->string("prev_month2_BOM_POS",100)->nullable();
            $table->string("prev_month1_BOM_POS",100)->nullable();
            $table->string("month_BOM_POS",100)->nullable();
            $table->string("prev_month2_Agency_Name",100)->nullable();
            $table->string("prev_month1_Agency_Name",100)->nullable();
            $table->string("month_Agency_Name",100)->nullable();
            $table->string("prev_month2_Agent_Code",50)->nullable();
            $table->string("prev_month1_Agent_Code",50)->nullable();
            $table->string("month_Agent_Code",50)->nullable();
            $table->string("Repeat_Agency",255)->nullable();
            $table->string("Buket_Match_Status",255)->nullable();
            $table->string("POS_Status",255)->nullable();
            $table->string("Formula1",255)->nullable();
            $table->string("Formula2",255)->nullable();
            $table->string("Formula3",255)->nullable();
            $table->string("Formula4",255)->nullable();
            $table->string("Formula5",255)->nullable();
            $table->string("Formula6",255)->nullable();
            $table->string("Formula7",255)->nullable();
            $table->string("Formula8",255)->nullable();
            $table->string("Formula9",255)->nullable();
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
        Schema::dropIfExists('adverse_bukets');
    }
}
