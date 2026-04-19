<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beban_mengajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->string('kelas', 10);
            $table->integer('total_sks'); // Bawaan dari MK saat ditambahkan, agar tidak perlu join terus
            $table->integer('sks_terjadwal')->default(0); // Progress auto-generate (0 -> total_sks)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beban_mengajar');
    }
};
