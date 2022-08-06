<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('check_count')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->string('payment_id', 20)->nullable();
            $table->float('price_amount', 10);
            $table->string('price_currency', 4)->nullable();
            $table->string('pay_address')->nullable();
            $table->string('payin_extra_id', 100)->nullable();
            $table->float('pay_amount', 10, 0)->nullable();
            $table->float('actually_paid', 10, 0)->nullable();
            $table->string('pay_currency', 10)->nullable();
            $table->string('order_id', 20)->index('order_id');
            $table->string('order_description', 100)->nullable();
            $table->string('purchase_id', 20)->nullable();
            $table->string('invoice_id', 10)->nullable();
            $table->float('outcome_amount', 10, 0)->nullable();
            $table->string('outcome_currency', 6)->nullable();
            $table->string('payment_status', 10)->nullable();
            $table->smallInteger('status_id')->nullable();
            $table->string('payout_hash')->nullable();
            $table->string('payin_hash')->nullable();
            $table->dateTime('np_created_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
