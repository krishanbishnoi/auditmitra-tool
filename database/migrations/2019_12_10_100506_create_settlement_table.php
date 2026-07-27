<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettlementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text("Month")->nullable();
            $table->string("REQUEST_NO",100)->nullable();
            $table->string("LOAN_NO",100)->nullable();
            $table->text("CUSTOMERNAME")->nullable();
            $table->text("BRANCH")->nullable();
            $table->string("STATE",150)->nullable();
            $table->string("PRODUCT_1",100)->nullable();
            $table->string("SCHEMEDESC",100)->nullable();
            $table->string("PENALTY",100)->nullable();
            $table->string("LOANAMT",100)->nullable();
            $table->string("EMI",100)->nullable();
            $table->string("SETTLEMENTAMT",100)->nullable();
            $table->string("REQUEST_DATE",100)->nullable();
            $table->text("REQUESTED_BY")->nullable();
            $table->string("SETTLEMENTSTART_DATE",100)->nullable();
            $table->string("SETTLEMENTEND_DATE",100)->nullable();
            $table->string("SETTLEMENT_STROKES",100)->nullable();
            $table->text("MAKER_REMARKS")->nullable();
            $table->text("VERIFIER_REMARKS")->nullable();
            $table->text("VERIFIED_DATE")->nullable();
            $table->text("VERIFIER")->nullable();
            $table->text("APPROVER_REAMRKS")->nullable();
            $table->text("APPROVED_DATE")->nullable();
            $table->text("APPROVER")->nullable();
            $table->string("STATUS1",100)->nullable();
            $table->text("APPROVED_BY")->nullable();
            $table->text("STATUS_DATE")->nullable();
            $table->text("APPROVER_EMAIL")->nullable();
            $table->text("TOTAL_POS")->nullable();
            $table->text("CURRENT_MONTH_INTEREST")->nullable();
            $table->text("INSTALLMENT_OVERDUE")->nullable();
            $table->text("TOTAL_OVERDUE_PRINCIPAL")->nullable();
            $table->text("TOTAL_OVERDUE_INTEREST")->nullable();
            $table->string("PENALTYCHARGES",100)->nullable();
            $table->text("ST_ON_PENALTY")->nullable();
            $table->string("BOUNCE_CHARGES",100)->nullable();
            $table->string("PENAL_CHARGES",100)->nullable();
            $table->string("OTHER_CHARGES",100)->nullable();
            $table->text("TOTAL_DUES")->nullable();
            $table->text("TOTAL_POSCOLL")->nullable();
            $table->text("CURRENT_MONTH_INTERESTCOLL")->nullable();
            $table->text("INSTALLMENT_OVERDUECOLL")->nullable();
            $table->text("TOTAL_OVERDUE_PRINCIPALCOLL")->nullable();
            $table->text("TOTAL_OVERDUE_INTERESTCOLL")->nullable();
            $table->text("PENALTYCOLLECTED")->nullable();
            $table->text("ST_ON_FC_CHARGESCOLL")->nullable();
            $table->text("BOUNCE_CHARGESCOLL")->nullable();
            $table->text("PENAL_CHARGESCOLL")->nullable();
            $table->text("OTHER_CHARGESCOLL")->nullable();
            $table->text("TOTAL_AMOUNT_COLLECTED")->nullable();
            $table->text("TOTAL_POSWAIVER")->nullable();
            $table->text("CURRENT_MONTH_INTERESTWAIVER")->nullable();
            $table->text("INSTALLMENT_OVERDUEWAIVER")->nullable();
            $table->text("TOTAL_OVERDUE_PRINCIPALWAIVER")->nullable();
            $table->text("TOTAL_OVERDUE_INTERESTWAIVER")->nullable();
            $table->text("PENALTYWIAVER")->nullable();
            $table->text("ST_ON_FC_CHARGESWAIVER")->nullable();
            $table->text("BOUNCE_CHARGESWAIVER")->nullable();
            $table->text("PENAL_CHARGESWAIVER")->nullable();
            $table->text("OTHER_CHARGESWAIVER")->nullable();
            $table->text("TOTAL_WAIVER")->nullable();
            $table->text("TOTAL_CHARGES_WAIVER")->nullable();
            $table->string("per_of_POS_Waiver",50)->nullable();
            $table->string("per_of_Charges_Waiver",50)->nullable();
            $table->string("BUCKET",10)->nullable();
            $table->text("DPD")->nullable();
            $table->text("DPDSTRING")->nullable();
            $table->text("STAGE")->nullable();
            $table->string("LOAN_STATUS",100)->nullable();
            $table->text("PAYMENT_RECEIVED")->nullable();
            $table->text("LAST_PAYMENT_DATE")->nullable();
            $table->text("SYSTEM")->nullable();
            $table->text("Product1")->nullable();
            $table->string("Status2",100)->nullable();
            $table->text("Hold_Category")->nullable();
            $table->text("Remark")->nullable();
            $table->text("Received_Date")->nullable();
            $table->text("1st_action_date")->nullable();
            $table->text("Hold_Date")->nullable();
            $table->text("Resolution_Date")->nullable();
            $table->text("Action_Date")->nullable();
            $table->string("Status3",100)->nullable();
            $table->text("Request_received_month")->nullable();
            $table->string("TOTAL_SETTLEMENT_AMT",100)->nullable();
            $table->text("Agency_Name")->nullable();
            $table->text("SETTLEMENT_Close_day")->nullable();
            $table->text("SETTLEMENT_Status")->nullable();
            $table->string("DAC_Amount",100)->nullable();
            $table->text("Online_Amount")->nullable();
            $table->string("Total_Payment_Reacive",100)->nullable();
            $table->string("SETTLEMENTAMT_Amount_Status",100)->nullable();
            $table->text("Amount_Deffrance")->nullable();
            $table->text("PRODUCT")->nullable();
            $table->text("BUCKET_Q")->nullable();
            $table->text("DAC_Received")->nullable();
            $table->text("Online_pay_date")->nullable();
            $table->text("Actual_Date")->nullable();
            $table->text("Date_GAP")->nullable();
            $table->text("Date_GAP_BKT")->nullable();
            // $table->text("Sep_19")->nullable();
            // $table->text("Oct_19")->nullable();
            // $table->text("Nov_19")->nullable();
            $table->string("Scheme",100)->nullable();
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
        Schema::dropIfExists('settlement');
    }
}
