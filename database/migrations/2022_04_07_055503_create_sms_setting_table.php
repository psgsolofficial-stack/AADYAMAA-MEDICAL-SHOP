<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_setting', function (Blueprint $table) {
            $table->id();
            $table->string('enable_notification');
            $table->string('domain_name');
            $table->string('account_email');
            $table->string('token_key');
            $table->string('test_no');
            $table->integer('created_by');
            $table->string('status');
            $table->integer('branch_id');
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
        Schema::dropIfExists('sms_setting');
    }
}
