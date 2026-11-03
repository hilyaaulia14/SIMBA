<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_klien', function (Blueprint $table) {
            $table->id('id_kehadiran');
            $table->foreignId('id_jadwal_bimbingan')->nullable()->constrained('jadwal_bimbingan', 'id_jadwal_bimbingan')->onDelete('set null');
            $table->foreignId('id_klien')->constrained('klien', 'id_klien')->onDelete('cascade');
            $table->date('tgl_hadir');
            $table->time('jam_hadir');
            $table->string('foto_kehadiran', 255)->nullable();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_klien');
    }
};
