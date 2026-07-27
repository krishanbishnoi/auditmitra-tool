<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchReposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_repos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('branch_id')->nullable();            
            $table->string('name')->nullable();            
            $table->integer('product_id')->nullable();            
            $table->string('location',255)->nullable();            
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
        Schema::dropIfExists('branch_repos');
    }
}
