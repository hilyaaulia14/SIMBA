<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Pegawai;
use App\Models\Surat;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Klien;
use App\Models\AturJadwal;
use App\Models\StatusBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\KehadiranKlien;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roleAdmin = Role::create([
            'id_role' => 1,
            'nama_role' => 'Admin / Bagian Umum',
        ]);

        $rolePK = Role::create([
            'id_role' => 2,
            'nama_role' => 'Pembimbing Kemasyarakatan',
        ]);

        // 2. Status Bimbingan
        $statusDijadwalkanUlang = StatusBimbingan::create(['id_status_bimbingan' => 1, 'nama_status' => 'Dijadwalkan Ulang']);
        $statusHadir = StatusBimbingan::create(['id_status_bimbingan' => 2, 'nama_status' => 'Hadir']);
        $statusTidakHadir = StatusBimbingan::create(['id_status_bimbingan' => 3, 'nama_status' => 'Tidak Hadir']);
        $statusTerjadwal = StatusBimbingan::create(['id_status_bimbingan' => 4, 'nama_status' => 'Terjadwal']);

        // 3. Klasifikasi Surat
        $suratSK = Surat::create(['id_surat' => 1, 'nama_surat' => 'Surat Keputusan (SK)']);
        $suratLitmas = Surat::create(['id_surat' => 2, 'nama_surat' => 'Surat Pengantar Penelitian Kemasyarakatan (Litmas)']);
        $suratPanggilan = Surat::create(['id_surat' => 3, 'nama_surat' => 'Surat Panggilan Klien']);
        $suratPemberitahuan = Surat::create(['id_surat' => 4, 'nama_surat' => 'Surat Pemberitahuan Bimbingan']);
        $suratIntegrasi = Surat::create(['id_surat' => 5, 'nama_surat' => 'Berkas Usulan Integrasi PB/CB/CMB']);

        // 4. Pegawai
        $admin = Pegawai::create([
            'id_role' => $roleAdmin->id_role,
            'nama_pegawai' => 'Budi Santoso, S.Sos.',
            'nip_usn' => '198501152010121001',
            'password' => Hash::make('admin123'),
            'jabatan' => 'Staf Bagian Umum / Arsiparis',
            'foto' => null,
        ]);

        $pk1 = Pegawai::create([
            'id_role' => $rolePK->id_role,
            'nama_pegawai' => 'Hilyatul Aulia, S.Tr.Pas.',
            'nip_usn' => '199003202015031002',
            'password' => Hash::make('pk123'),
            'jabatan' => 'Pembimbing Kemasyarakatan Ahli Pertama',
            'foto' => null,
        ]);

        $pk2 = Pegawai::create([
            'id_role' => $rolePK->id_role,
            'nama_pegawai' => 'Ahmad Fauzi, S.Psi.',
            'nip_usn' => '199205122018012003',
            'password' => Hash::make('pk123'),
            'jabatan' => 'Pembimbing Kemasyarakatan Muda',
            'foto' => null,
        ]);

        // 5. Surat Masuk
        SuratMasuk::create([
            'id_surat' => $suratLitmas->id_surat,
            'no_surat' => 'W19.PAS.PAS.1-PK.01.04-128',
            'asal_surat' => 'Lapas Kelas IIA Banjarmasin',
            'tgl_masuk' => Carbon::now()->subDays(5)->toDateString(),
            'perihal' => 'Pengantar Permohonan Litmas Integrasi Pembebasan Bersyarat WBP',
            'upload_dokumen' => null,
        ]);

        SuratMasuk::create([
            'id_surat' => $suratSK->id_surat,
            'no_surat' => 'W19.PAS-UM.01.01-445',
            'asal_surat' => 'Kantor Wilayah Kemenkumham Kalsel',
            'tgl_masuk' => Carbon::now()->subDays(3)->toDateString(),
            'perihal' => 'Surat Keputusan Pelaksanaan Monitoring dan Evaluasi Kearsipan Bapas',
            'upload_dokumen' => null,
        ]);

        SuratMasuk::create([
            'id_surat' => $suratPemberitahuan->id_surat,
            'no_surat' => 'B-882/POLRES-BJM/2026',
            'asal_surat' => 'Polresta Banjarmasin',
            'tgl_masuk' => Carbon::now()->subDay()->toDateString(),
            'perihal' => 'Pemberitahuan Diversi Anak Berhadapan dengan Hukum (ABH)',
            'upload_dokumen' => null,
        ]);

        // 6. Surat Keluar
        SuratKeluar::create([
            'id_surat' => $suratLitmas->id_surat,
            'no_surat' => 'W19.PAS.PAS.10-PK.02.01-094',
            'tujuan_surat' => 'Lapas Kelas IIA Banjarmasin',
            'tgl_keluar' => Carbon::now()->subDays(4)->toDateString(),
            'perihal' => 'Penyampaian Laporan Hasil Penelitian Kemasyarakatan (Litmas) Program PB',
            'upload_dokumen' => null,
        ]);

        SuratKeluar::create([
            'id_surat' => $suratPanggilan->id_surat,
            'no_surat' => 'W19.PAS.PAS.10-PK.02.02-112',
            'tujuan_surat' => 'Klien Pemasyarakatan & Penjamin',
            'tgl_keluar' => Carbon::now()->subDays(2)->toDateString(),
            'perihal' => 'Surat Pemanggilan Sesi Bimbingan Kepribadian Khusus Tahap Lanjutan',
            'upload_dokumen' => null,
        ]);

        // 7. Klien
        $klien1 = Klien::create([
            'no_register' => '1976/DW/XI/2026',
            'nama_klien' => 'Mokhammad Anhar Sugiarto BIN Harimotohar (ALM)',
            'asal_upt' => 'LAPAS KELAS IIB BANJARBARU',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Narkotika',
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
            'alamat' => 'Jl. A. Yani Km. 57,800 Gg. Balu Rt. 002 Rw. 001 Desa Bawahan Selan Kec. Mataraman Kab. Banjar Kalsel',
        ]);

        $klien2 = Klien::create([
            'no_register' => '1977/DW/XI/2026',
            'nama_klien' => 'Salman BIN Bahtiar',
            'asal_upt' => 'LAPAS KELAS IIB BANJARBARU',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Narkotika',
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
            'alamat' => 'Jl. Pekapuran Raya A Rt. 030 Rw. 002 Kel. Karang Mekar Kec. Banjarmasin Timur Kota Banjarmasin Kalsel',
        ]);

        $klien3 = Klien::create([
            'no_register' => '1978/DW/XI/2026',
            'nama_klien' => 'Darma BIN Imansyah',
            'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Korupsi',
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
            'alamat' => 'Jl.Perintis Rt 003/001 Desa Pulau Karya Kecamatan Tabukan Kabupaten Barito Kuala',
        ]);

        $klien4 = Klien::create([
            'no_register' => '1979/DW/XI/2026',
            'nama_klien' => 'Suhaimi ALS Imi BIN Suliyadi (Alm)',
            'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Narkotika',
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
            'alamat' => 'Komp. Handil Bakti Jalur Lii No. 08 Rt 018 Rw 002 Kelurahan Berangas Timur Kecamatan Alalak Kabupaten Barito Kuala',
        ]);

        $klien5 = Klien::create([
            'no_register' => '1980/DW/XI/2026',
            'nama_klien' => 'Heriansyah ALS Heri BIN Riduansyah (ALM)',
            'asal_upt' => 'LAPAS TANJUNG',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Narkotika',
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
            'alamat' => 'Jl. Kelayan B Tengah Gg. Gembira Rt. 016 Rw. 002 Kel. Kelayan Tengah Kec. Banjarmasin Selatan Kota Banjarmasin',
        ]);

        Klien::create([
            'no_register' => '1800/DW/XI/2026',
            'nama_klien' => 'Zaenal Abidin BIN Koderi (ALM)',
            'asal_upt' => 'LAPAS KELAS IIB BANJARBARU',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Narkotika',
            'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
            'alamat' => 'Jln. Sidomulyo Raya III Komp.Astp Blok B NO.16 RT.07 RW.09 Kel. Landasan Ulin Kota Banjarbaru',
        ]);

        Klien::create([
            'no_register' => '1070/DW/VIII/2026',
            'nama_klien' => 'Rachmat Sandi BIN Ahmad Al Kusasi (ALM)',
            'asal_upt' => 'RUTAN KELAS IIB PELAIHARI',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Narkotika',
            'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
            'alamat' => 'Komplek Griya Indah E No. 07 Rt. 004 Rw. 001 Kel. Sungai Paring Kec. Martapura Kab. Banjar Prov. Kalimantan Selatan',
        ]);

        Klien::create([
            'no_register' => '1972/DW/XI/2026',
            'nama_klien' => 'Supi Yadi BIN Syah Yani',
            'asal_upt' => 'RUTAN KELAS IIB PELAIHARI',
            'jenis_kelamin' => 'Laki-laki',
            'tindak_pidana' => 'Pencurian',
            'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
            'alamat' => 'Jl. A. Yani Rt. 005 Rw. 002 Desa Jorong Kec.jorong Kab. Tanah Laut Prov. Kalimantan Selatan',
        ]);

        Klien::create([
            'no_register' => '1135/DW/IX/2026',
            'nama_klien' => 'Putri Agustina BINTI Dikansyah',
            'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
            'jenis_kelamin' => 'Perempuan',
            'tindak_pidana' => 'Penggelapan',
            'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
            'alamat' => 'Jl. Aes. Nasution Rt 004 Rw 001 Kelurahan Marabahan Kota Kecamatan Marabahan Kabupaten Barito Kuala Provinsi Kalimantan Selatan',
        ]);

        Klien::create([
            'no_register' => '1136/DW/IX/2026',
            'nama_klien' => 'Sifa Rahayu BINTI Mardani (ALM)',
            'asal_upt' => 'RUTAN KELAS IIB MARABAHAN',
            'jenis_kelamin' => 'Perempuan',
            'tindak_pidana' => 'Penggelapan',
            'jenis_bimbingan' => 'Cuti Bersyarat (CB)',
            'alamat' => 'Jl. Jendral Sudirman Rt 015 Rw 001 Kelurahan Marabahan Kota Kecamatan Marabahan Kabupaten Barito Kuala Provinsi Kalimantan Selatan',
        ]);

        // 8. Atur Jadwal
        AturJadwal::create([
            'id_klien' => $klien1->id_klien,
            'hari' => 'Senin',
            'jam_mulai' => '09:00:00',
        ]);

        AturJadwal::create([
            'id_klien' => $klien2->id_klien,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00:00',
        ]);

        AturJadwal::create([
            'id_klien' => $klien3->id_klien,
            'hari' => 'Rabu',
            'jam_mulai' => '10:30:00',
        ]);

        AturJadwal::create([
            'id_klien' => $klien4->id_klien,
            'hari' => 'Kamis',
            'jam_mulai' => '13:30:00',
        ]);

        AturJadwal::create([
            'id_klien' => $klien5->id_klien,
            'hari' => 'Jumat',
            'jam_mulai' => '14:00:00',
        ]);

        // 9. Jadwal Bimbingan
        $jadwal1 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien1->id_klien,
            'id_status_bimbingan' => $statusHadir->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::now()->subDays(7)->toDateString(),
            'jam_mulai' => '09:00:00',
            'keterangan' => 'Bimbingan Tahap Awal: Penjelasan hak, kewajiban, dan tata tertib wajib lapor klien pemasyarakatan.',
        ]);

        $jadwal2 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien2->id_klien,
            'id_status_bimbingan' => $statusHadir->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::now()->subDays(3)->toDateString(),
            'jam_mulai' => '10:00:00',
            'keterangan' => 'Bimbingan Kemandirian: Evaluasi pelatihan keterampilan kerja dan aktivitas wirausaha rumahan.',
        ]);

        $jadwal3 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien3->id_klien,
            'id_status_bimbingan' => $statusTerjadwal->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::today()->toDateString(),
            'jam_mulai' => '10:30:00',
            'keterangan' => 'Bimbingan Kepribadian: Konseling pencegahan relaps dan penguatan ketahanan mental bersama keluarga.',
        ]);

        $jadwal4 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien4->id_klien,
            'id_status_bimbingan' => $statusTerjadwal->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::today()->addDays(2)->toDateString(),
            'jam_mulai' => '13:30:00',
            'keterangan' => 'Bimbingan Kesadaran Hukum dan Pengendalian Emosi.',
        ]);

        $jadwal5 = JadwalBimbingan::create([
            'id_pegawai' => $pk2->id_pegawai,
            'id_klien' => $klien5->id_klien,
            'id_status_bimbingan' => $statusDijadwalkanUlang->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::today()->addDays(4)->toDateString(),
            'jam_mulai' => '14:00:00',
            'keterangan' => 'Sesi Evaluasi Bimbingan Berkala Bulanan.',
        ]);

        // 10. Kehadiran Klien (Presensi)
        KehadiranKlien::create([
            'id_jadwal_bimbingan' => $jadwal1->id_jadwal_bimbingan,
            'id_klien' => $klien1->id_klien,
            'tgl_hadir' => Carbon::now()->subDays(7)->toDateString(),
            'jam_hadir' => '08:55:00',
            'foto_kehadiran' => null,
            'catatan' => 'Klien hadir tepat waktu, bersikap kooperatif, dan menandatangani pakta integritas wajib lapor.',
        ]);

        KehadiranKlien::create([
            'id_jadwal_bimbingan' => $jadwal2->id_jadwal_bimbingan,
            'id_klien' => $klien2->id_klien,
            'tgl_hadir' => Carbon::now()->subDays(3)->toDateString(),
            'jam_hadir' => '10:05:00',
            'foto_kehadiran' => null,
            'catatan' => 'Klien menunjukkan perkembangan positif dalam aktivitas menjahit mandiri di lingkungan tempat tinggal.',
        ]);
    }
}
