<?php

use App\Enums\SubmissionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $values = implode(', ', array_map(
            fn (SubmissionStatus $status) => "'{$status->value}'",
            SubmissionStatus::cases(),
        ));

        DB::statement("CREATE TYPE submission_status AS ENUM ({$values})");
        DB::statement("ALTER TABLE registration_submissions ADD COLUMN status submission_status NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE registration_submissions DROP COLUMN status');
        DB::statement('DROP TYPE IF EXISTS submission_status');
    }
};
