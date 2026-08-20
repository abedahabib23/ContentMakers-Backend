<?php

use App\Enums\ApplicantLevel;
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
            fn (ApplicantLevel $level) => "'{$level->value}'",
            ApplicantLevel::cases(),
        ));

        DB::statement("CREATE TYPE applicant_level AS ENUM ({$values})");

        Schema::create('registration_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_form_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('city');
            $table->text('interests')->nullable();
            $table->text('current_skills')->nullable();
            // Sensitive documents — the `private` disk, same as trainer
            // documents. See CLAUDE.md's File Storage Architecture.
            $table->string('id_photo_path');
            $table->string('cv_path');
            $table->string('portfolio_url')->nullable();
            $table->text('motivation');
            $table->timestamps();

            // One application per email per form — not globally, since the
            // same person may legitimately apply to different projects.
            $table->unique(['registration_form_id', 'email']);
        });

        DB::statement('ALTER TABLE registration_submissions ADD COLUMN level applicant_level NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_submissions');

        DB::statement('DROP TYPE IF EXISTS applicant_level');
    }
};
