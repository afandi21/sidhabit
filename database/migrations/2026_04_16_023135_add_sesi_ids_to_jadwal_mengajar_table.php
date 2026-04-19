<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->foreignId('sesi_mulai_id')->nullable()->constrained('sesi_kuliah');
            $table->foreignId('sesi_selesai_id')->nullable()->constrained('sesi_kuliah');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->dropForeign(['sesi_mulai_id']);
            $table->dropForeign(['sesi_selesai_id']);
            $table->dropColumn(['sesi_mulai_id', 'sesi_selesai_id']);
        });
    }
};
