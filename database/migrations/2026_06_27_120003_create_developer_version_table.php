<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('developer_version', function (Blueprint $table) {
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('developer_id');
            $table->decimal('cost_per_hour')->nullable();
            $table->timestamps();

            $table->primary(['version_id', 'developer_id']);
        });

        Schema::table('developer_version', function (Blueprint $table) {
            $table
                ->foreign('version_id')
                ->references('id')
                ->on('versions')
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

    public function down()
    {
        Schema::table('developer_version', function (Blueprint $table) {
            $table->dropForeign(['version_id']);
            $table->dropForeign(['developer_id']);
        });

        Schema::dropIfExists('developer_version');
    }
};
