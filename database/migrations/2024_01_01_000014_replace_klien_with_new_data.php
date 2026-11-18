<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus seluruh data klien lama (cascade ke atur_jadwal, jadwal_bimbingan, kehadiran_klien)
        DB::table('klien')->delete();

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE klien AUTO_INCREMENT = 1');
        }

        $baseTime = Carbon::now();

        $clients = [
            [
                'no_register' => '1976/DW/XI/2026',
                'nama_klien' => 'Mokhammad Anhar Sugiarto BIN Harimotohar (ALM)',
                'asal_upt' => 'LAPAS KELAS IIB BANJARBARU',
                'tindak_pidana' => 'Narkotika',
                'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. A. Yani Km. 57,800 Gg. Balu Rt. 002 Rw. 001 Desa Bawahan Selan Kec. Mataraman Kab. Banjar Kalsel',
            ],
            [
                'no_register' => '1977/DW/XI/2026',
                'nama_klien' => 'Salman BIN Bahtiar',
                'asal_upt' => 'LAPAS KELAS IIB BANJARBARU',
                'tindak_pidana' => 'Narkotika',
                'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Pekapuran Raya A Rt. 030 Rw. 002 Kel. Karang Mekar Kec. Banjarmasin Timur Kota Banjarmasin Kalsel',
            ],
            [
                'no_register' => '1978/DW/XI/2026',
                'nama_klien' => 'Darma BIN Imansyah',
                'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
                'tindak_pidana' => 'Korupsi',
                'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl.Perintis Rt 003/001 Desa Pulau Karya Kecamatan Tabukan Kabupaten Barito Kuala',
            ],
            [
                'no_register' => '1979/DW/XI/2026',
                'nama_klien' => 'Suhaimi ALS Imi BIN Suliyadi (Alm)',
                'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
                'tindak_pidana' => 'Narkotika',
                'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Komp. Handil Bakti Jalur Lii No. 08 Rt 018 Rw 002 Kelurahan Berangas Timur Kecamatan Alalak Kabupaten Barito Kuala',
            ],
            [
                'no_register' => '1980/DW/XI/2026',
                'nama_klien' => 'Heriansyah ALS Heri BIN Riduansyah (ALM)',
                'asal_upt' => 'LAPAS TANJUNG',
                'tindak_pidana' => 'Narkotika',
                'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Kelayan B Tengah Gg. Gembira Rt. 016 Rw. 002 Kel. Kelayan Tengah Kec. Banjarmasin Selatan Kota Banjarmasin',
            ],
            [
                'no_register' => '1800/DW/XI/2026',
                'nama_klien' => 'Zaenal Abidin BIN Koderi (ALM)',
                'asal_upt' => 'LAPAS KELAS IIB BANJARBARU',
                'tindak_pidana' => 'Narkotika',
                'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jln. Sidomulyo Raya III Komp.Astp Blok B NO.16 RT.07 RW.09 Kel. Landasan Ulin Kota Banjarbaru',
            ],
            [
                'no_register' => '1070/DW/VIII/2026',
                'nama_klien' => 'Rachmat Sandi BIN Ahmad Al Kusasi (ALM)',
                'asal_upt' => 'RUTAN KELAS IIB PELAIHARI',
                'tindak_pidana' => 'Narkotika',
                'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Komplek Griya Indah E No. 07 Rt. 004 Rw. 001 Kel. Sungai Paring Kec. Martapura Kab. Banjar Prov. Kalimantan Selatan',
            ],
            [
                'no_register' => '1972/DW/XI/2026',
                'nama_klien' => 'Supi Yadi BIN Syah Yani',
                'asal_upt' => 'RUTAN KELAS IIB PELAIHARI',
                'tindak_pidana' => 'Pencurian',
                'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. A. Yani Rt. 005 Rw. 002 Desa Jorong Kec.jorong Kab. Tanah Laut Prov. Kalimantan Selatan',
            ],
            [
                'no_register' => '1135/DW/IX/2026',
                'nama_klien' => 'Putri Agustina BINTI Dikansyah',
                'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
                'tindak_pidana' => 'Penggelapan',
                'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Aes. Nasution Rt 004 Rw 001 Kelurahan Marabahan Kota Kecamatan Marabahan Kabupaten Barito Kuala Provinsi Kalimantan Selatan',
            ],
            [
                'no_register' => '1136/DW/IX/2026',
                'nama_klien' => 'Sifa Rahayu BINTI Mardani (ALM)',
                'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
                'tindak_pidana' => 'Penggelapan',
                'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Jendral Sudirman Rt 015 Rw 001 Kelurahan Marabahan Kota Kecamatan Marabahan Kabupaten Barito Kuala Provinsi Kalimantan Selatan',
            ],
        ];

        $rows = [];
        foreach ($clients as $index => $client) {
            $timestamp = $baseTime->copy()->subSeconds($index);
            $rows[] = array_merge($client, [
                'telepon' => null,
                'tgl_lahir' => null,
                'jam_mulai' => null,
                'keterangan' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        DB::table('klien')->insert($rows);
    }

    public function down(): void
    {
        DB::table('klien')->whereIn('no_register', [
            '1976/DW/XI/2026',
            '1977/DW/XI/2026',
            '1978/DW/XI/2026',
            '1979/DW/XI/2026',
            '1980/DW/XI/2026',
            '1800/DW/XI/2026',
            '1070/DW/VIII/2026',
            '1972/DW/XI/2026',
            '1135/DW/IX/2026',
            '1136/DW/IX/2026',
        ])->delete();
    }
};
