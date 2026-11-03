<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawai', 'email')) {
                $table->string('email', 100)->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('pegawai', 'alamat')) {
                $table->text('alamat')->nullable()->after('email');
            }
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_masuk', 'tgl_diterima')) {
                $table->date('tgl_diterima')->nullable()->after('tgl_masuk');
            }
            if (!Schema::hasColumn('surat_masuk', 'tujuan')) {
                $table->string('tujuan', 150)->nullable()->after('tgl_diterima');
            }
            if (!Schema::hasColumn('surat_masuk', 'lampiran')) {
                $table->string('lampiran', 100)->nullable()->after('perihal');
            }
            if (!Schema::hasColumn('surat_masuk', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('lampiran');
            }
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_keluar', 'lampiran')) {
                $table->string('lampiran', 100)->nullable()->after('perihal');
            }
            if (!Schema::hasColumn('surat_keluar', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('lampiran');
            }
            if (!Schema::hasColumn('surat_keluar', 'upload_dokumen')) {
                $table->string('upload_dokumen', 255)->nullable()->after('keterangan');
            }
        });

        Schema::table('klien', function (Blueprint $table) {
            if (!Schema::hasColumn('klien', 'telepon')) {
                $table->string('telepon', 30)->nullable()->after('nama_klien');
            }
            if (!Schema::hasColumn('klien', 'alamat')) {
                $table->text('alamat')->nullable()->after('tindak_pidana');
            }
            if (!Schema::hasColumn('klien', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('alamat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['email', 'alamat']);
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn(['tgl_diterima', 'tujuan', 'lampiran', 'keterangan']);
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['lampiran', 'keterangan', 'upload_dokumen']);
        });

        Schema::table('klien', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'alamat', 'keterangan']);
        });
    }
};
