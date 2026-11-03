<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klien', function (Blueprint $table) {
            $table->id('id_klien');
            $table->string('no_register', 50);
            $table->string('nama_klien', 100);
            $table->string('jenis_kelamin', 20);
            $table->date('tgl_lahir')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->string('tindak_pidana', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klien');
    }
};
