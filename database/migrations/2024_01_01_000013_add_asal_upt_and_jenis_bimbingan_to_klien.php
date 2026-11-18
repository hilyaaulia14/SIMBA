<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('klien', function (Blueprint $table) {
            if (!Schema::hasColumn('klien', 'asal_upt')) {
                $table->string('asal_upt', 150)->nullable()->after('nama_klien');
            }
            if (!Schema::hasColumn('klien', 'jenis_bimbingan')) {
                $table->string('jenis_bimbingan', 50)->nullable()->after('tindak_pidana');
            }
        });

        // Set default values for existing records if null
        DB::table('klien')->whereNull('asal_upt')->update([
            'asal_upt' => 'Lapas Kelas IIA Banjarmasin',
        ]);

        DB::table('klien')->whereNull('jenis_bimbingan')->update([
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
        ]);
    }

    public function down(): void
    {
        Schema::table('klien', function (Blueprint $table) {
            if (Schema::hasColumn('klien', 'asal_upt')) {
                $table->dropColumn('asal_upt');
            }
            if (Schema::hasColumn('klien', 'jenis_bimbingan')) {
                $table->dropColumn('jenis_bimbingan');
            }
        });
    }
};
