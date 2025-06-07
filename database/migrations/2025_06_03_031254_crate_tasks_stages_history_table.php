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

        Schema::create('tasks_stages_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('stage_id')->constrained('stages');
            $table->foreignUuid('previous_stage_id')->nullable()->constrained('stages');
            $table->foreignUuid('user_id')->constrained('users'); // usuario que realizó el cambio de etapa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks_stages_history');
    }
};
