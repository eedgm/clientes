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
        Schema::table('developer_task', function (Blueprint $table) {
            $table
                ->foreign('developer_id')
                ->references('id')
                ->on('developers')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('task_id')
                ->references('id')
                ->on('tasks')
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
        Schema::table('developer_task', function (Blueprint $table) {
            $table->dropForeign(['developer_id']);
            $table->dropForeign(['task_id']);
        });
    }
};
