<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosens')->cascadeOnDelete();
            $table->foreignId('jadwal_mengajar_id')->nullable()->constrained('jadwal_mengajar')->nullOnDelete();
            $table->date('tanggal');
            $table->integer('pertemuan_ke')->nullable();
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->integer('durasi_menit')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alfa', 'cuti'])->default('hadir');
            $table->enum('metode_presensi', ['fingerprint', 'face_id', 'manual', 'dispensasi'])->default('fingerprint');
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->decimal('latitude_keluar', 10, 8)->nullable();
            $table->decimal('longitude_keluar', 11, 8)->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['dosen_id', 'tanggal']);
            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
