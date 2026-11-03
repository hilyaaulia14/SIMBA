<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atur_jadwal', function (Blueprint $table) {
            $table->id('id_atur_jadwal');
            $table->foreignId('id_klien')->constrained('klien', 'id_klien')->onDelete('cascade');
            $table->string('hari', 20);
            $table->time('jam_mulai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atur_jadwal');
    }
};
