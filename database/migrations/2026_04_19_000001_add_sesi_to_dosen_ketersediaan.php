<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen_ketersediaan', function (Blueprint $table) {
            // Drop Foreign Keys first
            $table->dropForeign(['dosen_id']);
            $table->dropForeign(['hari_id']);
            
            // Now drop unique index
            $table->dropUnique(['dosen_id', 'hari_id']);
            
            // Add sesi_id
            $table->foreignId('sesi_id')->nullable()->after('hari_id')->constrained('sesi_kuliah')->onDelete('cascade');
            
            // Re-add Foreign Keys for dosen and hari
            $table->foreign('dosen_id')->references('id')->on('dosens')->onDelete('cascade');
            $table->foreign('hari_id')->references('id')->on('hari')->onDelete('cascade');
            
            // Add new unique index: Dosen - Hari - Sesi
            $table->unique(['dosen_id', 'hari_id', 'sesi_id'], 'dosen_hari_sesi_unique');
        });
    }

    public function down(): void
    {
        Schema::table('dosen_ketersediaan', function (Blueprint $table) {
            $table->dropUnique('dosen_hari_sesi_unique');
            $table->dropColumn('sesi_id');
            $table->unique(['dosen_id', 'hari_id']);
        });
    }
};
