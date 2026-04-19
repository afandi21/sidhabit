<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen_ketersediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->foreignId('hari_id')->constrained('hari')->onDelete('cascade');
            $table->boolean('is_bersedia')->default(true); // true = bisa mengajar, false = tidak bisa
            $table->timestamps();
            
            $table->unique(['dosen_id', 'hari_id']); // Satu dosen hanya punya satu status per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_ketersediaan');
    }
};
