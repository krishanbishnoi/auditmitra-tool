<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrailIntensityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trail_intensity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agreement_id')->nullable();
            $table->string('agreement_no')->nullable();
            $table->string('npa_stage_id')->nullable();
            $table->string('bom_bucket')->nullable();
            $table->string('product_flag_1')->nullable();
            $table->string('bom_pos')->nullable();
            $table->string('branch')->nullable();
            $table->string('mailing_state')->nullable();
            $table->string('region')->nullable();
            $table->string('collection_manager_name')->nullable();
            $table->string('agency_code')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('status')->nullable();
            $table->string('date_stamp_1')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_met')->nullable();
            $table->string('last_payment_date')->nullable();
            $table->string('ptp_date')->nullable();
            $table->string('ptp_amount')->nullable();
            $table->string('collection_name')->nullable();
            $table->string('feetback_date')->nullable();
            $table->string('disposition_code')->nullable();
            $table->string('trail_status')->nullable();
            $table->string('date_stamp')->nullable();
            $table->text('remarks')->nullable();
            $table->string('attempts')->nullable();
            $table->string('agent_id')->nullable();
            // $table->string('comp_non_comp')->nullable();
            // $table->string('month')->nullable();
            // $table->string('product_flag')->nullable();
            // $table->string('bom_bucket_q')->nullable();
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
        Schema::dropIfExists('trail_intensity');
    }
}
