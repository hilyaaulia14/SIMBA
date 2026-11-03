<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id('id_surat_masuk');
            $table->foreignId('id_surat')->constrained('surat', 'id_surat')->onDelete('cascade');
            $table->string('no_surat', 50);
            $table->string('asal_surat', 100);
            $table->date('tgl_masuk');
            $table->string('perihal', 255);
            $table->string('upload_dokumen', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
