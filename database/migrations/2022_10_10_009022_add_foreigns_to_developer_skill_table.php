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
        Schema::table('developer_skill', function (Blueprint $table) {
            $table
                ->foreign('skill_id')
                ->references('id')
                ->on('skills')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('developer_id')
                ->references('id')
                ->on('developers')
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
        Schema::table('developer_skill', function (Blueprint $table) {
            $table->dropForeign(['skill_id']);
            $table->dropForeign(['developer_id']);
        });
    }
};
