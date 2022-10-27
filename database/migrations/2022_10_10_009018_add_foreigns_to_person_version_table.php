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
        Schema::table('person_version', function (Blueprint $table) {
            $table
                ->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('version_id')
                ->references('id')
                ->on('versions')
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
        Schema::table('person_version', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropForeign(['version_id']);
        });
    }
};
