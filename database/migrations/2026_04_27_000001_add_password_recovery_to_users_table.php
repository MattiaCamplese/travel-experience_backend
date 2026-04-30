<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS password_recovery_code VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS password_recovery_at TIMESTAMP NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS password_recovery_code');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS password_recovery_at');
    }
};
