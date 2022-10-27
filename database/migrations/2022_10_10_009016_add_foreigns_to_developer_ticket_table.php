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
        Schema::table('developer_ticket', function (Blueprint $table) {
            $table
                ->foreign('developer_id')
                ->references('id')
                ->on('developers')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('ticket_id')
                ->references('id')
                ->on('tickets')
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
        Schema::table('developer_ticket', function (Blueprint $table) {
            $table->dropForeign(['developer_id']);
            $table->dropForeign(['ticket_id']);
        });
    }
};
