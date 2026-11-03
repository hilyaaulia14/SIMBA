<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\Surat;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\Klien;
use App\Models\AturJadwal;
use App\Models\StatusBimbingan;
use App\Models\JadwalBimbingan;
use App\Models\KehadiranKlien;

class SimbaSystemTest extends TestCase
{
    protected $admin;
    protected $pk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Pegawai::where('nip_usn', '198501152010121001')->first();
        $this->pk = Pegawai::where('nip_usn', '199003202015031002')->first();
        Storage::fake('public');
    }

    /** Test 1: Halaman Login & Otentikasi */
    public function test_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIMBA');
        $response->assertSee('figma_login_logos.png');
        $response->assertSee('role_admin_card.png');
        $response->assertSee('role_pk_card.png');
        $response->assertDontSee('<path');
    }

    public function test_admin_can_login_and_redirects_to_admin_dashboard()
    {
        $response = $this->post('/login', [
            'nip_usn' => '198501152010121001',
            'password' => 'admin123',
            'role_target' => 'admin',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_pk_can_login_and_redirects_to_pk_dashboard()
    {
        $response = $this->post('/login', [
            'nip_usn' => '199003202015031002',
            'password' => 'pk123',
            'role_target' => 'pk',
        ]);

        $response->assertRedirect(route('pk.dashboard'));
        $this->assertAuthenticatedAs($this->pk);
    }

    /** Test 2: Role Isolation & Akses Terproteksi */
    public function test_pk_cannot_access_admin_panel()
    {
        $response = $this->actingAs($this->pk)->get('/admin/dashboard');
        $response->assertRedirect(route('pk.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_access_pk_panel()
    {
        $response = $this->actingAs($this->admin)->get('/pk/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    /** Test 3: Dashboard Admin & Metric Persuratan */
    public function test_admin_dashboard_metrics()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Total Surat Masuk');
        $response->assertSee('Total Surat Keluar');
        $response->assertSee('Berkas Terarsip Digital');
    }

    /** Test 4: CRUD Surat Masuk & Upload */
    public function test_admin_crud_surat_masuk()
    {
        $file = UploadedFile::fake()->create('surat_dinas.pdf', 500, 'application/pdf');

        // Store
        $response = $this->actingAs($this->admin)->post('/admin/surat-masuk', [
            'id_surat' => 1,
            'no_surat' => 'TEST-SM-999',
            'asal_surat' => 'Kementerian Hukum dan HAM Kalsel',
            'tgl_masuk' => '2026-11-01',
            'perihal' => 'Pengujian Otomatis Surat Masuk SIMBA',
            'upload_dokumen' => $file,
        ]);

        $response->assertRedirect(route('admin.surat-masuk.index'));
        $this->assertDatabaseHas('surat_masuk', ['no_surat' => 'TEST-SM-999']);

        $sm = SuratMasuk::where('no_surat', 'TEST-SM-999')->first();
        $this->assertNotNull($sm);

        // Update
        $updateResponse = $this->actingAs($this->admin)->put('/admin/surat-masuk/' . $sm->id_surat_masuk, [
            'id_surat' => 1,
            'no_surat' => 'TEST-SM-999-REVISED',
            'asal_surat' => 'Kemenkumham Kalsel',
            'tgl_masuk' => '2026-11-01',
            'perihal' => 'Perihal Terupdate',
        ]);
        $updateResponse->assertRedirect(route('admin.surat-masuk.index'));
        $this->assertDatabaseHas('surat_masuk', ['no_surat' => 'TEST-SM-999-REVISED']);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/surat-masuk/' . $sm->id_surat_masuk);
        $deleteResponse->assertRedirect(route('admin.surat-masuk.index'));
        $this->assertDatabaseMissing('surat_masuk', ['id_surat_masuk' => $sm->id_surat_masuk]);
    }

    /** Test 5: CRUD Surat Keluar */
    public function test_admin_crud_surat_keluar()
    {
        $response = $this->actingAs($this->admin)->post('/admin/surat-keluar', [
            'id_surat' => 2,
            'no_surat' => 'TEST-SK-888',
            'tujuan_surat' => 'Kejaksaan Negeri Banjarmasin',
            'tgl_keluar' => '2026-11-02',
            'perihal' => 'Penyampaian Laporan Litmas Terpadu',
        ]);

        $response->assertRedirect(route('admin.surat-keluar.index'));
        $this->assertDatabaseHas('surat_keluar', ['no_surat' => 'TEST-SK-888']);

        $sk = SuratKeluar::where('no_surat', 'TEST-SK-888')->first();
        $this->assertNotNull($sk);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/surat-keluar/' . $sk->id_surat_keluar);
        $deleteResponse->assertRedirect(route('admin.surat-keluar.index'));
        $this->assertDatabaseMissing('surat_keluar', ['id_surat_keluar' => $sk->id_surat_keluar]);
    }

    /** Test 6: Dashboard PK & 3 Metric Cards */
    public function test_pk_dashboard_metrics_and_view()
    {
        $response = $this->actingAs($this->pk)->get('/pk/dashboard');
        $response->assertStatus(200);
        // Acceptance Criteria: 3 Metric Cards: Jadwal Hari Ini, Jadwal Minggu Ini, Total Klien
        $response->assertSee('Jadwal Hari Ini');
        $response->assertSee('Jadwal Minggu Ini');
        $response->assertSee('Total Klien');
    }

    /** Test 7: Modul Data Klien (Tanpa Field Jenis Bimbingan) */
    public function test_pk_crud_klien_without_jenis_bimbingan()
    {
        $response = $this->actingAs($this->pk)->get('/pk/klien');
        $response->assertStatus(200);
        // Ensure "Jenis Bimbingan" is NOT in the client table or form
        $response->assertDontSee('Jenis Bimbingan');

        // Create client
        $createResponse = $this->actingAs($this->pk)->post('/pk/klien', [
            'no_register' => 'TEST/REG/2026/999',
            'nama_klien' => 'Klien Percobaan Unit Test',
            'jenis_kelamin' => 'Laki-laki',
            'tgl_lahir' => '1997-03-15',
            'jam_mulai' => '09:30',
            'tindak_pidana' => 'Perjudian (Pasal 303 KUHP)',
        ]);

        $createResponse->assertRedirect(route('pk.klien.index'));
        $this->assertDatabaseHas('klien', ['no_register' => 'TEST/REG/2026/999']);

        $klien = Klien::where('no_register', 'TEST/REG/2026/999')->first();

        // Update
        $updateResponse = $this->actingAs($this->pk)->put('/pk/klien/' . $klien->id_klien, [
            'no_register' => 'TEST/REG/2026/999',
            'nama_klien' => 'Klien Percobaan Updated',
            'jenis_kelamin' => 'Laki-laki',
            'tgl_lahir' => '1997-03-15',
            'jam_mulai' => '10:00',
            'tindak_pidana' => 'Perjudian (Pasal 303 KUHP)',
        ]);
        $updateResponse->assertRedirect(route('pk.klien.index'));
        $this->assertDatabaseHas('klien', ['nama_klien' => 'Klien Percobaan Updated']);

        // Delete
        $deleteResponse = $this->actingAs($this->pk)->delete('/pk/klien/' . $klien->id_klien);
        $deleteResponse->assertRedirect(route('pk.klien.index'));
        $this->assertDatabaseMissing('klien', ['id_klien' => $klien->id_klien]);
    }

    /** Test 8: Modul Atur Jadwal PK (Hari & Jam Mulai saja) */
    public function test_pk_atur_jadwal_functionality()
    {
        $klien = Klien::first();

        $response = $this->actingAs($this->pk)->post('/pk/atur-jadwal', [
            'id_klien' => $klien->id_klien,
            'hari' => 'Rabu',
            'jam_mulai' => '11:00',
        ]);

        $response->assertRedirect(route('pk.atur-jadwal.index'));
        $this->assertDatabaseHas('atur_jadwal', [
            'id_klien' => $klien->id_klien,
            'hari' => 'Rabu',
        ]);
    }

    /** Test 9: Modul Jadwal Bimbingan */
    public function test_pk_crud_jadwal_bimbingan()
    {
        $klien = Klien::first();

        $response = $this->actingAs($this->pk)->post('/pk/jadwal', [
            'id_klien' => $klien->id_klien,
            'id_status_bimbingan' => 4, // Dijadwalkan
            'tgl_bimbingan' => Carbon::tomorrow()->toDateString(),
            'jam_mulai' => '10:00',
            'keterangan' => 'Sesi Bimbingan Kepribadian Khusus',
        ]);

        $response->assertRedirect(route('pk.jadwal.index'));
        $this->assertDatabaseHas('jadwal_bimbingan', [
            'id_klien' => $klien->id_klien,
            'keterangan' => 'Sesi Bimbingan Kepribadian Khusus',
        ]);
    }

    /** Test 10: Pencatatan Bukti Kehadiran Klien */
    public function test_pk_record_kehadiran_klien()
    {
        $klien = Klien::first();
        $jadwal = JadwalBimbingan::where('id_klien', $klien->id_klien)->first();
        $file = UploadedFile::fake()->image('bukti_wajib_lapor.jpg', 640, 480);

        $response = $this->actingAs($this->pk)->post('/pk/kehadiran', [
            'id_klien' => $klien->id_klien,
            'id_jadwal_bimbingan' => $jadwal ? $jadwal->id_jadwal_bimbingan : null,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '09:00',
            'foto_kehadiran' => $file,
            'catatan' => 'Klien hadir secara tertib dan menunjukkan perkembangan yang memuaskan.',
        ]);

        $response->assertRedirect(route('pk.kehadiran.index'));
        $this->assertDatabaseHas('kehadiran_klien', [
            'id_klien' => $klien->id_klien,
            'catatan' => 'Klien hadir secara tertib dan menunjukkan perkembangan yang memuaskan.',
        ]);

        // Verify linked schedule marked as Terlaksana (id = 2)
        if ($jadwal) {
            $this->assertDatabaseHas('jadwal_bimbingan', [
                'id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan,
                'id_status_bimbingan' => 2,
            ]);
        }
    }

    /** Test 11: Admin Update Profile (Tanpa Ubah Password) */
    public function test_admin_profile_update()
    {
        $response = $this->actingAs($this->admin)->put('/admin/profile', [
            'nama_pegawai' => 'Rina Wulandari, S.H.',
            'nip_usn' => $this->admin->nip_usn,
            'jabatan' => 'PK Madya / Bagian Umum',
            'email' => 'admin@bapasbanjarmasin.go.id',
            'alamat' => 'Jl. Hasan Basri No. 12 Banjarmasin',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $this->assertDatabaseHas('pegawai', [
            'id_pegawai' => $this->admin->id_pegawai,
            'nama_pegawai' => 'Rina Wulandari, S.H.',
            'email' => 'admin@bapasbanjarmasin.go.id',
        ]);
    }

    /** Test 12: PK Update Profile (Tanpa Password & Tanpa Kontak) */
    public function test_pk_profile_update()
    {
        $response = $this->actingAs($this->pk)->put('/pk/profile', [
            'nama_pegawai' => 'Hilyatul Aulia, S.Tr.Pas.',
            'nip_usn' => $this->pk->nip_usn,
            'jabatan' => 'Pembimbing Kemasyarakatan Pertama',
            'alamat' => 'Kota Banjarmasin',
        ]);

        $response->assertRedirect(route('pk.profile.index'));
        $this->assertDatabaseHas('pegawai', [
            'id_pegawai' => $this->pk->id_pegawai,
            'nama_pegawai' => 'Hilyatul Aulia, S.Tr.Pas.',
            'alamat' => 'Kota Banjarmasin',
        ]);
    }

    /** Test 13: Kehadiran pagination, topbar radius, and menu item */
    public function test_kehadiran_page_pagination_and_topbar()
    {
        $response = $this->actingAs($this->pk)->get('/pk/kehadiran?page=1');
        $response->assertStatus(200);
        $response->assertSee('border-bottom-left-radius: 24px', false);
        $response->assertSee('class="pagination"', false);
        $response->assertDontSee('pagination.previous');
        $response->assertSee('Bukti Kehadiran');
    }
}
