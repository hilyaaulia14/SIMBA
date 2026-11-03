<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_bimbingan', function (Blueprint $table) {
            $table->id('id_jadwal_bimbingan');
            $table->foreignId('id_pegawai')->constrained('pegawai', 'id_pegawai')->onDelete('cascade');
            $table->foreignId('id_klien')->constrained('klien', 'id_klien')->onDelete('cascade');
            $table->foreignId('id_status_bimbingan')->constrained('status_bimbingan', 'id_status_bimbingan')->onDelete('cascade');
            $table->date('tgl_bimbingan');
            $table->time('jam_mulai');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_bimbingan');
    }
};
