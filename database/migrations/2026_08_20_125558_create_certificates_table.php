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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            // A human-shareable, verifiable identifier — not the numeric
            // id, which is guessable and reveals issuance order/volume.
            $table->string('certificate_number')->unique();
            // The generated PDF — public disk, since a certificate is
            // meant to be shared/verified, same reasoning as trainee
            // outputs once published.
            $table->string('file_path')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();

            // One certificate per trainee per project.
            $table->unique(['trainee_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
