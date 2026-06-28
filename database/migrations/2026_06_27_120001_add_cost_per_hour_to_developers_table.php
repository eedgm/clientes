<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('developers', function (Blueprint $table) {
            $table
                ->decimal('cost_per_hour')
                ->nullable()
                ->after('rol_id');
        });
    }

    public function down()
    {
        Schema::table('developers', function (Blueprint $table) {
            $table->dropColumn('cost_per_hour');
        });
    }
};
