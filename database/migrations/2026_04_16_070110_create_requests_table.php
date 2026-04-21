<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // link to users table
            $table->string('passenger');
            $table->string('destination');
            $table->text('purpose');
            $table->date('date');
            $table->string('status')->default('Pending');
            $table->string('driver')->nullable();
            $table->integer('tickets')->nullable();
            $table->timestamps();
        }); // ✅ FIXED
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
