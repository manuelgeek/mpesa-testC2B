<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSTKTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_t_k_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('merchant_id');
            $table->string('checkout_id');
            $table->string('result_code');
            $table->string('phone_no')->nullable();
            $table->string('amount')->nullable();
            $table->string('mpesa_receipt_no')->nullable()->unique();;
            $table->boolean('valid')->default(false);
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
        Schema::dropIfExists('s_t_k_transactions');
    }
}
