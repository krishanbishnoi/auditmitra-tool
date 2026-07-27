<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qcs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('qm_sheet_id')->nullable();
            $table->integer('audit_id')->nullable();
            $table->integer('qc_by_id')->nullable();
            $table->integer('status')->comment('1 for pass or 2 faild')->nullable();
            $table->text('feedback')->nullable();

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
        Schema::dropIfExists('qcs');
    }
}
