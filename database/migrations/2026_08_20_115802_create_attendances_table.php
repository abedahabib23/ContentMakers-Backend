<?php

use App\Enums\AttendanceStatus;
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
            fn (AttendanceStatus $status) => "'{$status->value}'",
            AttendanceStatus::cases(),
        ));

        DB::statement("CREATE TYPE attendance_status AS ENUM ({$values})");

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // One attendance record per trainee per session.
            $table->unique(['trainee_id', 'training_session_id']);
        });

        DB::statement('ALTER TABLE attendances ADD COLUMN status attendance_status NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');

        DB::statement('DROP TYPE IF EXISTS attendance_status');
    }
};
