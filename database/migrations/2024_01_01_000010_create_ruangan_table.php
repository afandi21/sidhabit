<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan', 15)->unique();
            $table->string('nama_ruangan');
            $table->string('gedung')->nullable();
            $table->integer('lantai')->nullable();
            $table->integer('kapasitas')->nullable();
            $table->foreignId('lokasi_kampus_id')->nullable()->constrained('lokasi_kampus')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
