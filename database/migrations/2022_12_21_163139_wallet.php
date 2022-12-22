<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Wallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('wallet', function (Blueprint $table) {
            $table->id();
            $table->integer('currency_id');
            $table->integer('user_id');
            $table->integer('amount');
            $table->string('pb_key')->nullable();
            $table->string('pv_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('wallet');
    }
}
