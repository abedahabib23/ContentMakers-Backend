<?php

use App\Enums\OutputStatus;
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
            fn (OutputStatus $status) => "'{$status->value}'",
            OutputStatus::cases(),
        ));

        // migrate:fresh drops all tables but not custom types, so without
        // this, re-running from scratch fails with "type already exists".
        DB::statement('DROP TYPE IF EXISTS output_status');
        DB::statement("CREATE TYPE output_status AS ENUM ({$values})");

        Schema::create('trainee_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            // The work itself, in whichever form fits — a link, an
            // uploaded file (public disk, since a published output is
            // meant to be shown on the public site), or just a text
            // write-up. None are required alone; at least one is expected.
            $table->string('work_url')->nullable();
            $table->string('work_file_path')->nullable();
            $table->text('work_description')->nullable();
            $table->date('output_date')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE trainee_outputs ADD COLUMN status output_status NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainee_outputs');

        DB::statement('DROP TYPE IF EXISTS output_status');
    }
};
