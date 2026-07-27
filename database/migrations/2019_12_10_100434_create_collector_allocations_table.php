<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectorAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collector_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agrmnt_id')->nullable();
            $table->string('agreement_no')->nullable();
            $table->string('npa_stage_id')->nullable();
            $table->string('bom_bucket')->nullable();
            $table->string('bom_bucket_q')->nullable();
            $table->string('product_flag_1')->nullable();
            $table->string('product_flag')->nullable();
            $table->string('bom_pos')->nullable();
            $table->string('branch')->nullable();
            $table->string('mailing_state')->nullable();
            $table->string('region')->nullable();
            $table->string('collection_manager')->nullable();
            $table->string('agency_code')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('status')->nullable();
            $table->string('date_stamp')->nullable();
            $table->string('agent_code')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('agent_allocation_status')->nullable();
            $table->string('agent_allocation_date_stamp')->nullable();
            $table->string('remarks')->nullable();
            // $table->string('gap')->nullable();
            // $table->string('relocation_date')->nullable();
            // $table->string('month')->nullable();
            // $table->string('dac_agent_id')->nullable();
            // $table->string('id_status')->nullable();
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
        Schema::dropIfExists('collector_allocations');
    }
}
