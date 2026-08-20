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
        // Singleton — exactly one row, created on first access via
        // CenterSetting::current(). Contact fields are nullable since an
        // admin fills them in over time via the settings screen.
        Schema::create('center_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('accepts_registrations')->default(true);
            $table->boolean('auto_issue_certificate')->default(true);
            $table->boolean('notify_email_on_new_request')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_settings');
    }
};
