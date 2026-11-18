<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Klien;
use App\Models\JadwalBimbingan;
use App\Models\KehadiranKlien;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Carbon\Carbon;

class ComprehensiveSearchFilterTest extends TestCase
{
    protected $admin;
    protected $pk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Pegawai::where('nip_usn', '198501152010121001')->first();
        $this->pk = Pegawai::where('nip_usn', '199003202015031002')->first();
    }

    public function test_klien_search_by_all_specified_fields()
    {
        $uniqueNo = 'REG/' . rand(1000, 9999) . '/XYZ';
        $klien = Klien::create([
            'no_register' => $uniqueNo,
            'nama_klien' => 'Bambang Sudarmono',
            'asal_upt' => 'Lapas Khusus Narkotika Karang Intan',
            'tindak_pidana' => 'Penyalahgunaan Narkotika Golongan I',
            'jenis_bimbingan' => 'Pembebasan Bersyarat (PB)',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Mahligai Indah No. 77, Kertak Hanyar',
            'keterangan' => 'Klien wajib lapor seminggu sekali',
            'tgl_lahir' => '1992-05-20',
        ]);

        // 1. Cari berdasarkan Nama
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=Sudarmono');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 2. Cari berdasarkan Nomor Registrasi
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=' . $uniqueNo);
        $res->assertStatus(200);
        $res->assertSee('Bambang Sudarmono');

        // 3. Cari berdasarkan Asal UPT
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=Karang Intan');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 4. Cari berdasarkan Tindak Pidana
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=Golongan I');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 5. Cari berdasarkan Jenis Bimbingan (singkatan PB & teks panjang)
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=PB');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        $res = $this->actingAs($this->pk)->get('/pk/klien?search=Pembebasan Bersyarat');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 6. Cari berdasarkan Jenis Kelamin (Laki-laki & Pria)
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=Laki-laki');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        $res = $this->actingAs($this->pk)->get('/pk/klien?search=pria');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 7. Cari tanggal lahir (format DD/MM/YYYY)
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=20/05/1992');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 8. Cari tanggal lahir (format YYYY-MM-DD)
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=1992-05-20');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 9. Cari tanggal lahir dengan nama bulan "Mei 1992"
        $res = $this->actingAs($this->pk)->get('/pk/klien?search=20 Mei 1992');
        $res->assertStatus(200);
        $res->assertSee($uniqueNo);

        // 10. Cari "laki laki" dan pastikan tidak ada perempuan
        $resLaki = $this->actingAs($this->pk)->get('/pk/klien?search=laki laki');
        $resLaki->assertStatus(200);
        $resLaki->assertSee('Salman BIN Bahtiar');
        $resLaki->assertDontSee('Putri Agustina BINTI Dikansyah');
        $resLaki->assertDontSee('Sifa Rahayu BINTI Mardani (ALM)');

        // 11. Cari "perempuan" dan pastikan tidak ada laki-laki
        $resPerempuan = $this->actingAs($this->pk)->get('/pk/klien?search=perempuan');
        $resPerempuan->assertStatus(200);
        $resPerempuan->assertSee('Putri Agustina BINTI Dikansyah');
        $resPerempuan->assertDontSee('Salman BIN Bahtiar');
        $resPerempuan->assertDontSee('Darma BIN Imansyah');

        // 12. Cari nama "ahmad" - hanya Rachmat Sandi BIN Ahmad Al Kusasi yang muncul, Putri Agustina TIDAK boleh muncul
        $resAhmad = $this->actingAs($this->pk)->get('/pk/klien?search=ahmad');
        $resAhmad->assertStatus(200);
        $resAhmad->assertSee('Rachmat Sandi BIN Ahmad Al Kusasi (ALM)');
        $resAhmad->assertDontSee('Putri Agustina BINTI Dikansyah');

        $klien->delete();
    }

    public function test_jadwal_search_and_date_filter()
    {
        $today = Carbon::today()->toDateString();
        $res = $this->actingAs($this->pk)->get('/pk/jadwal?tgl_bimbingan=' . $today);
        $res->assertStatus(200);

        // Search tanggal format DD/MM/YYYY pada jadwal
        $formattedDate = Carbon::today()->format('d/m/Y');
        $searchRes = $this->actingAs($this->pk)->get('/pk/jadwal?search=' . $formattedDate);
        $searchRes->assertStatus(200);
    }

    public function test_kehadiran_search_and_date_filter()
    {
        $res = $this->actingAs($this->pk)->get('/pk/kehadiran?search=Ahmad');
        $res->assertStatus(200);

        $dateRes = $this->actingAs($this->pk)->get('/pk/kehadiran?tgl_hadir=' . Carbon::now()->subDays(7)->toDateString());
        $dateRes->assertStatus(200);
    }

    public function test_surat_masuk_and_keluar_search_and_date_filter()
    {
        $resSM = $this->actingAs($this->admin)->get('/admin/surat-masuk?search=Lapas');
        $resSM->assertStatus(200);

        $resSK = $this->actingAs($this->admin)->get('/admin/surat-keluar?search=Pemanggilan');
        $resSK->assertStatus(200);
    }

    public function test_atur_jadwal_supports_dropdown_and_typed_client()
    {
        // 1. Metode Dropdown (ID klien)
        $klien = Klien::first();
        $res1 = $this->actingAs($this->pk)->post('/pk/atur-jadwal', [
            'id_klien' => $klien->id_klien,
            'tgl_bimbingan' => '2026-11-20',
            'jam_mulai' => '09:30',
            'keterangan' => 'Jadwal via dropdown ID',
        ]);
        $res1->assertRedirect(route('pk.atur-jadwal.index'));
        $this->assertDatabaseHas('atur_jadwal', ['id_klien' => $klien->id_klien, 'jam_mulai' => '09:30']);

        // 2. Metode Ketik Sendiri (Nama Klien yang sudah ada)
        $res2 = $this->actingAs($this->pk)->post('/pk/atur-jadwal', [
            'id_klien' => $klien->nama_klien,
            'tgl_bimbingan' => '2026-11-21',
            'jam_mulai' => '10:00',
            'keterangan' => '',
        ]);
        $res2->assertRedirect(route('pk.atur-jadwal.index'));

        // 3. Menolak input klien yang tidak ada di Data Klien
        $nonExistentName = 'Klien Tidak Terdaftar ' . rand(1000, 9999);
        $res3 = $this->actingAs($this->pk)->post('/pk/atur-jadwal', [
            'id_klien' => $nonExistentName,
            'tgl_bimbingan' => '2026-11-22',
            'jam_mulai' => '11:00',
            'keterangan' => 'Jadwal klien tidak terdaftar',
        ]);
        $res3->assertSessionHas('error');
        $this->assertDatabaseMissing('klien', ['nama_klien' => $nonExistentName]);
    }
}
