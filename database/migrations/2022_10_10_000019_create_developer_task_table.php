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
        Schema::create('developer_task', function (Blueprint $table) {
            $table->unsignedBigInteger('developer_id');
            $table->unsignedBigInteger('task_id');
            $table->text('comments')->nullable();
            $table->text('assignations')->nullable();
            $table->decimal('gain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('developer_task');
    }
};
