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
        Schema::table('tickets', function (Blueprint $table) {
            $table
                ->foreign('statu_id')
                ->references('id')
                ->on('status')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('priority_id')
                ->references('id')
                ->on('priorities')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('receipt_id')
                ->references('id')
                ->on('receipts')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['statu_id']);
            $table->dropForeign(['priority_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['receipt_id']);
            $table->dropForeign(['person_id']);
        });
    }
};
