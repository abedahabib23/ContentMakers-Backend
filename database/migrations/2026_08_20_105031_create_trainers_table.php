<?php

use App\Enums\TrainerStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values = implode(', ', array_map(
            fn (TrainerStatus $status) => "'{$status->value}'",
            TrainerStatus::cases(),
        ));

        DB::statement("CREATE TYPE trainer_status AS ENUM ({$values})");

        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('specialization')->nullable();
            $table->unsignedSmallInteger('years_of_experience')->nullable();
            // Sensitive documents — only the storage path lives here, on the
            // `private` disk (see CLAUDE.md's File Storage Architecture),
            // never the file itself.
            $table->string('id_photo_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->json('certificate_paths')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE trainers ADD COLUMN status trainer_status NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');

        DB::statement('DROP TYPE IF EXISTS trainer_status');
    }
};
