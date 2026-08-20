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
        Schema::create('task_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainee_id')->constrained()->cascadeOnDelete();
            // Null until the trainer actually grades it — a task being
            // assigned isn't the same as it being evaluated.
            $table->unsignedInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            // One evaluation per trainee per task.
            $table->unique(['task_id', 'trainee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_evaluations');
    }
};
