<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrasi ini dibuat aman jika kolom sudah ada
        if (!Schema::hasColumn('dosens', 'tanggal_lahir')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dosens', 'tanggal_lahir')) {
            Schema::table('dosens', function (Blueprint $table) {
                $table->dropColumn('tanggal_lahir');
            });
        }
    }
};
