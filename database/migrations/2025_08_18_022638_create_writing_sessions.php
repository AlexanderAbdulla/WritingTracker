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
        Schema::create('writing_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->integer('wordcount');
            $table->integer('minutes_spent');
            $table->timestamp('time_finished')->useCurrent();
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_session');
    }
};
