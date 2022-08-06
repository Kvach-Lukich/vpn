<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->float('balance', 10)->nullable()->default(0);
            $table->tinyInteger('active_subscription')->default(0);
            $table->boolean('trial')->nullable();
            $table->dateTime('last_paid')->nullable();
            $table->json('wg_json')->nullable();
            $table->string('url_safe_public_key', 44)->nullable();
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
        Schema::dropIfExists('billings');
    }
}
