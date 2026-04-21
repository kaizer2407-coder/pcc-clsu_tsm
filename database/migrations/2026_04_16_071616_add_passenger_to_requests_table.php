<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'passenger')) {
                $table->string('passenger');
            }
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('passenger');
        });
    }
};
