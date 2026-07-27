<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsInUploadsTabled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dacs', function (Blueprint $table) {
            $table->string('lob',255)->nullable();
        });
        Schema::table('collector_allocations', function (Blueprint $table) {
            $table->string('lob',255)->nullable();
        });
        Schema::table('settlement', function (Blueprint $table) {
            $table->string('lob',255)->nullable();
        });
        Schema::table('trail_intensity', function (Blueprint $table) {
            $table->string('lob',255)->nullable();
        });
        Schema::table('adverse_bukets', function (Blueprint $table) {
            $table->string('lob',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('uploads_tabled', function (Blueprint $table) {
        //     //
        // });
    }
}
