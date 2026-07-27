<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDacsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dacs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('si')->nullable();
            $table->string('PaymentId',100)->nullable();
            $table->text('Location')->nullable();
            $table->string('BranchId',100)->nullable();
            $table->string('BranchName')->nullable();
            $table->string('AgencyId',100)->nullable();
            $table->string('AgencyName')->nullable();
            $table->string('AgentEmail')->nullable();
            $table->string('AgentName')->nullable();
            $table->string('Agent_Id',100)->nullable();
            $table->string('ReceiptBookNo',100)->nullable();
            $table->string('ReceiptNo',100)->nullable();
            $table->string('PhysicalReceiptNo_online_transaction_ID')->nullable();
            $table->string('ReceiptDate',100)->nullable();
            $table->string('Month',100)->nullable();
            $table->string('ReferenceNo')->nullable();
            $table->string('CUSTOMERNAME')->nullable();
            $table->string('PRODUCT',100)->nullable();
            $table->string('CURRENT_BUCKET')->nullable();
            $table->string('PaymentTowards')->nullable();
            $table->string('EMIAMT',100)->nullable();
            $table->string('LatePaymentPenalty')->nullable();
            $table->string('BounceChargesAmt')->nullable();
            $table->text('Excess')->nullable();
            $table->string('IMD')->nullable();
            $table->string('ProcFee')->nullable();
            $table->string('Swap')->nullable();
            $table->string('EBCCharge')->nullable();
            $table->string('CollectionPickupCharge')->nullable();
            $table->string('ForeclosureAmount')->nullable();
            $table->string('TotalReceiptAmount')->nullable();
            $table->string('PaymentMode')->nullable();
            $table->string('InstrumentDate')->nullable();
            $table->string('InstrumentNo')->nullable();
            $table->string('InstrumentAmount')->nullable();
            $table->string('MICRCode')->nullable();
            $table->string('PANCardNo')->nullable();
            $table->string('BatchID')->nullable();
            $table->string('BatchIDCreatedDate')->nullable();
            $table->string('DepositDate')->nullable();
            $table->string('ENCollect_Pay_in_slip_ID')->nullable();
            $table->string('CMS_Pay_In_Slip_ID')->nullable();
            $table->string('DepositAccountNumber',100)->nullable();
            $table->string('Rectified_Depslip_number',100)->nullable();
            $table->string('DepositAmount',100)->nullable();
            $table->string('PaymentStatus',100)->nullable();
            $table->string('DepositSlipNo_Status',100)->nullable();
            $table->string('Finnone_Update')->nullable();
            $table->string('Vintage',100)->nullable();
            $table->string('a')->nullable();
            $table->string('s')->nullable();
            $table->string('MerchantReferenceNumber')->nullable();
            $table->string('MID')->nullable();
            $table->string('BankTransactionId')->nullable();
            $table->string('BankTId')->nullable();
            $table->string('Amount',100)->nullable();
            $table->string('StatusCode',100)->nullable();
            $table->string('CreatedDate')->nullable();
            $table->string('RRN')->nullable();
            $table->string('AuthCode')->nullable();
            $table->string('CardNumber')->nullable();
            $table->string('CardType')->nullable();
            $table->string('CardHolderName')->nullable();
            $table->string('ProductGroup')->nullable();
            $table->string('MerchantId')->nullable();
            $table->string('MerchantTransactionId')->nullable();
            $table->string('BBPayPartnerAgentCode')->nullable();
            $table->string('BBPayPartnerAgentEmailId')->nullable();
            $table->string('BBPayPartnerAgentMobileNumber',20)->nullable();
            $table->string('BBPayPartnerBranchCode')->nullable();
            $table->string('BBPayBatchAckDate')->nullable();
            $table->string('DepositeBankName')->nullable();
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
        Schema::dropIfExists('dacs');
    }
}

