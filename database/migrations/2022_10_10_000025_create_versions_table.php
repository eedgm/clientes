<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('proposal_id');
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('total');
            $table->date('time');
            $table->decimal('cost_per_hour');
            $table->decimal('hour_per_day');
            $table->decimal('months_to_pay');
            $table->decimal('unexpected');
            $table->decimal('company_gain');
            $table->decimal('bank_tax');
            $table->decimal('first_payment');

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
        Schema::dropIfExists('versions');
    }
};
