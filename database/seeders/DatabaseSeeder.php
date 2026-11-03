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
        $statusMenunggu = StatusBimbingan::create(['id_status_bimbingan' => 1, 'nama_status' => 'Menunggu']);
        $statusTerlaksana = StatusBimbingan::create(['id_status_bimbingan' => 2, 'nama_status' => 'Terlaksana']);
        $statusAbsen = StatusBimbingan::create(['id_status_bimbingan' => 3, 'nama_status' => 'Absen']);
        $statusDijadwalkan = StatusBimbingan::create(['id_status_bimbingan' => 4, 'nama_status' => 'Dijadwalkan']);

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
            'no_register' => 'REG/2026/BPS/001',
            'nama_klien' => 'Ahmad Supriyadi',
            'jenis_kelamin' => 'Laki-laki',
            'tgl_lahir' => '1995-04-12',
            'jam_mulai' => '09:00:00',
            'tindak_pidana' => 'Pencurian (Pasal 363 KUHP)',
        ]);

        $klien2 = Klien::create([
            'no_register' => 'REG/2026/BPS/002',
            'nama_klien' => 'Siti Rahmawati',
            'jenis_kelamin' => 'Perempuan',
            'tgl_lahir' => '1998-08-25',
            'jam_mulai' => '10:00:00',
            'tindak_pidana' => 'Penipuan / Penggelapan (Pasal 378 KUHP)',
        ]);

        $klien3 = Klien::create([
            'no_register' => 'REG/2026/BPS/003',
            'nama_klien' => 'Muhammad Rizki Pratama',
            'jenis_kelamin' => 'Laki-laki',
            'tgl_lahir' => '2001-11-03',
            'jam_mulai' => '10:30:00',
            'tindak_pidana' => 'Narkotika (UU No. 35/2009)',
        ]);

        $klien4 = Klien::create([
            'no_register' => 'REG/2026/BPS/004',
            'nama_klien' => 'Hendro Gunawan',
            'jenis_kelamin' => 'Laki-laki',
            'tgl_lahir' => '1989-02-18',
            'jam_mulai' => '13:30:00',
            'tindak_pidana' => 'Penganiayaan (Pasal 351 KUHP)',
        ]);

        $klien5 = Klien::create([
            'no_register' => 'REG/2026/BPS/005',
            'nama_klien' => 'Dewi Lestari',
            'jenis_kelamin' => 'Perempuan',
            'tgl_lahir' => '1996-07-09',
            'jam_mulai' => '14:00:00',
            'tindak_pidana' => 'ITE / Transaksi Elektronik',
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
            'id_status_bimbingan' => $statusTerlaksana->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::now()->subDays(7)->toDateString(),
            'jam_mulai' => '09:00:00',
            'keterangan' => 'Bimbingan Tahap Awal: Penjelasan hak, kewajiban, dan tata tertib wajib lapor klien pemasyarakatan.',
        ]);

        $jadwal2 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien2->id_klien,
            'id_status_bimbingan' => $statusTerlaksana->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::now()->subDays(3)->toDateString(),
            'jam_mulai' => '10:00:00',
            'keterangan' => 'Bimbingan Kemandirian: Evaluasi pelatihan keterampilan kerja dan aktivitas wirausaha rumahan.',
        ]);

        $jadwal3 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien3->id_klien,
            'id_status_bimbingan' => $statusDijadwalkan->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::today()->toDateString(),
            'jam_mulai' => '10:30:00',
            'keterangan' => 'Bimbingan Kepribadian: Konseling pencegahan relaps dan penguatan ketahanan mental bersama keluarga.',
        ]);

        $jadwal4 = JadwalBimbingan::create([
            'id_pegawai' => $pk1->id_pegawai,
            'id_klien' => $klien4->id_klien,
            'id_status_bimbingan' => $statusDijadwalkan->id_status_bimbingan,
            'tgl_bimbingan' => Carbon::today()->addDays(2)->toDateString(),
            'jam_mulai' => '13:30:00',
            'keterangan' => 'Bimbingan Kesadaran Hukum dan Pengendalian Emosi.',
        ]);

        $jadwal5 = JadwalBimbingan::create([
            'id_pegawai' => $pk2->id_pegawai,
            'id_klien' => $klien5->id_klien,
            'id_status_bimbingan' => $statusMenunggu->id_status_bimbingan,
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
