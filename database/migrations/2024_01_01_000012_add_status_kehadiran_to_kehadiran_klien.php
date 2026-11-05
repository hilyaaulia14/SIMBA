<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadiran_klien', function (Blueprint $table) {
            if (!Schema::hasColumn('kehadiran_klien', 'status_kehadiran')) {
                $table->string('status_kehadiran', 50)->default('Hadir')->after('foto_kehadiran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kehadiran_klien', function (Blueprint $table) {
            if (Schema::hasColumn('kehadiran_klien', 'status_kehadiran')) {
                $table->dropColumn('status_kehadiran');
            }
        });
    }
};
