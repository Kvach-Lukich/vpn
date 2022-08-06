<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedSmallInteger('code')->nullable();
            $table->tinyInteger('no2fa')->nullable();
            $table->timestamps();
            $table->string('invite_token', 10)->nullable()->unique('invite_token');
            $table->bigInteger('parent_id')->nullable();
            $table->string('parent_invite_token', 10)->nullable();
            $table->unsignedSmallInteger('invite_count')->default(0);
            $table->unsignedSmallInteger('invite_limit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
