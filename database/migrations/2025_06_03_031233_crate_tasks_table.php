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

        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('stage_id')->constrained('stages'); // etapa actual de la tarea
            $table->boolean('is_active')->default(true); // estado de la tarea
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users');
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
