<?php

use App\Enums\UserType;
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
            fn (UserType $type) => "'{$type->value}'",
            UserType::cases(),
        ));

        DB::statement("CREATE TYPE user_type AS ENUM ({$values})");
        DB::statement('ALTER TABLE users ADD COLUMN type user_type NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP COLUMN type');
        DB::statement('DROP TYPE IF EXISTS user_type');
    }
};
