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
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description');
            $table->unsignedBigInteger('statu_id');
            $table->unsignedBigInteger('priority_id');
            $table->decimal('hours')->nullable();
            $table->decimal('total')->nullable();
            $table->date('finished_ticket')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->unsignedBigInteger('person_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
