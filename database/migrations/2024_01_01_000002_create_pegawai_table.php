<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id('id_pegawai');
            $table->foreignId('id_role')->constrained('role', 'id_role')->onDelete('cascade');
            $table->string('nama_pegawai', 100);
            $table->string('nip_usn', 30)->unique();
            $table->string('password', 255);
            $table->string('jabatan', 50);
            $table->string('foto', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
