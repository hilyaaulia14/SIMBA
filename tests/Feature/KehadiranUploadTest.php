<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Models\Klien;
use App\Models\JadwalBimbingan;

class KehadiranUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected $pk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pk = Pegawai::where('nip_usn', '199003202015031002')->first()
            ?? Pegawai::whereHas('role', fn($q) => $q->where('nama_role', 'like', '%PK%')->orWhere('nama_role', 'like', '%Pembimbing%'))->first();
        Storage::fake('public');
    }

    public function test_upload_bukti_kehadiran_png_berhasil()
    {
        $klien = Klien::first();
        $jadwal = JadwalBimbingan::first();
        $file = UploadedFile::fake()->image('bukti_kehadiran.png', 640, 480);

        $response = $this->actingAs($this->pk)->post('/pk/kehadiran', [
            'id_klien' => $klien->id_klien,
            'id_jadwal_bimbingan' => $jadwal ? $jadwal->id_jadwal_bimbingan : null,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '10:30',
            'status_kehadiran' => 'Hadir',
            'foto_kehadiran' => $file,
            'catatan' => 'Upload PNG berhasil',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pk.kehadiran.index'));
        $this->assertDatabaseHas('kehadiran_klien', [
            'id_klien' => $klien->id_klien,
            'catatan' => 'Upload PNG berhasil',
        ]);
    }

    public function test_upload_bukti_kehadiran_pdf_berhasil()
    {
        $klien = Klien::first();
        $file = UploadedFile::fake()->create('dokumen_hadir.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->pk)->post('/pk/kehadiran', [
            'id_klien' => $klien->id_klien,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '11:00',
            'status_kehadiran' => 'Hadir',
            'foto_kehadiran' => $file,
            'catatan' => 'Upload PDF berhasil',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pk.kehadiran.index'));
        $this->assertDatabaseHas('kehadiran_klien', [
            'id_klien' => $klien->id_klien,
            'catatan' => 'Upload PDF berhasil',
        ]);
    }

    public function test_upload_bukti_kehadiran_format_tidak_valid_menampilkan_pesan_indonesia()
    {
        $klien = Klien::first();
        $file = UploadedFile::fake()->create('malicious.txt', 10, 'text/plain');

        $response = $this->actingAs($this->pk)->from('/pk/jadwal')->post('/pk/kehadiran', [
            'id_klien' => $klien->id_klien,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '11:00',
            'status_kehadiran' => 'Hadir',
            'foto_kehadiran' => $file,
            'catatan' => 'Tes format salah',
        ]);

        $response->assertSessionHasErrors('foto_kehadiran');
        $errors = session('errors')->get('foto_kehadiran');
        $this->assertStringNotContainsString('validation.mimes', $errors[0]);
        $this->assertStringContainsString('PNG', $errors[0]);
    }

    public function test_hapus_bukti_kehadiran_juga_menghapus_jadwal_bimbingan_terkait()
    {
        $klien = Klien::first();
        $jadwal = JadwalBimbingan::create([
            'id_pegawai' => $this->pk->id_pegawai,
            'id_klien' => $klien->id_klien,
            'id_status_bimbingan' => 4,
            'tgl_bimbingan' => Carbon::today()->toDateString(),
            'jam_mulai' => '09:00',
            'keterangan' => 'Jadwal uji hapus sinkron',
        ]);

        $kehadiran = \App\Models\KehadiranKlien::create([
            'id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan,
            'id_klien' => $klien->id_klien,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '09:05',
            'status_kehadiran' => 'Hadir',
            'catatan' => 'Catatan hadir uji hapus',
        ]);

        $response = $this->actingAs($this->pk)->delete('/pk/kehadiran/' . $kehadiran->id_kehadiran);

        $response->assertRedirect(route('pk.kehadiran.index'));
        $this->assertDatabaseMissing('kehadiran_klien', ['id_kehadiran' => $kehadiran->id_kehadiran]);
        $this->assertDatabaseMissing('jadwal_bimbingan', ['id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan]);
    }

    public function test_jam_kedatangan_sesuai_dengan_jam_pembuatan_jadwal()
    {
        $klien = Klien::first();
        $jadwal = JadwalBimbingan::create([
            'id_pegawai' => $this->pk->id_pegawai,
            'id_klien' => $klien->id_klien,
            'id_status_bimbingan' => 4,
            'tgl_bimbingan' => Carbon::today()->toDateString(),
            'jam_mulai' => '14:15:00',
            'keterangan' => 'Jadwal jam 14:15',
        ]);

        $response = $this->actingAs($this->pk)->post('/pk/kehadiran', [
            'id_klien' => $klien->id_klien,
            'id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '11:03',
            'status_kehadiran' => 'Hadir',
            'catatan' => 'Tes sinkronisasi jam',
        ]);

        $response->assertRedirect(route('pk.kehadiran.index'));
        $kehadiran = \App\Models\KehadiranKlien::where('id_jadwal_bimbingan', $jadwal->id_jadwal_bimbingan)->first();
        $this->assertNotNull($kehadiran);
        $this->assertEquals('14:15', substr($kehadiran->jam_hadir, 0, 5));
    }

    public function test_hapus_bukti_kehadiran_tanpa_id_jadwal_tetap_menghapus_jadwal_hadir_klien()
    {
        $klien = Klien::first();
        $jadwal = JadwalBimbingan::create([
            'id_pegawai' => $this->pk->id_pegawai,
            'id_klien' => $klien->id_klien,
            'id_status_bimbingan' => 2, // Hadir
            'tgl_bimbingan' => '2026-11-20',
            'jam_mulai' => '10:00:00',
            'keterangan' => 'Jadwal status hadir',
        ]);

        $kehadiran = \App\Models\KehadiranKlien::create([
            'id_jadwal_bimbingan' => null, // Simulasi id_jadwal null
            'id_klien' => $klien->id_klien,
            'tgl_hadir' => '2026-11-19',
            'jam_hadir' => '10:00:00',
            'status_kehadiran' => 'Hadir',
            'catatan' => 'Presensi tanpa link id jadwal',
        ]);

        $response = $this->actingAs($this->pk)->delete('/pk/kehadiran/' . $kehadiran->id_kehadiran);

        $response->assertRedirect(route('pk.kehadiran.index'));
        $this->assertDatabaseMissing('kehadiran_klien', ['id_kehadiran' => $kehadiran->id_kehadiran]);
        $this->assertDatabaseMissing('jadwal_bimbingan', ['id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan]);
    }

    public function test_hapus_jadwal_bimbingan_juga_menghapus_kehadiran_terkait()
    {
        $klien = Klien::first();
        $jadwal = JadwalBimbingan::create([
            'id_pegawai' => $this->pk->id_pegawai,
            'id_klien' => $klien->id_klien,
            'id_status_bimbingan' => 2,
            'tgl_bimbingan' => Carbon::today()->toDateString(),
            'jam_mulai' => '10:00:00',
            'keterangan' => 'Jadwal uji hapus cascade',
        ]);

        $kehadiran = \App\Models\KehadiranKlien::create([
            'id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan,
            'id_klien' => $klien->id_klien,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '10:00:00',
            'status_kehadiran' => 'Hadir',
            'catatan' => 'Presensi cascade',
        ]);

        $response = $this->actingAs($this->pk)->delete('/pk/jadwal/' . $jadwal->id_jadwal_bimbingan);

        $response->assertRedirect(route('pk.jadwal.index'));
        $this->assertDatabaseMissing('jadwal_bimbingan', ['id_jadwal_bimbingan' => $jadwal->id_jadwal_bimbingan]);
        $this->assertDatabaseMissing('kehadiran_klien', ['id_kehadiran' => $kehadiran->id_kehadiran]);
    }

    public function test_simpan_bukti_kehadiran_dengan_kamera_realtime_base64_berhasil()
    {
        $klien = Klien::first();
        // 1x1 transparent/red JPEG pixel in base64
        $fakeBase64 = 'data:image/jpeg;base64,' . base64_encode(imagecreate(10, 10) ? "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0c\x14\r\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a\x1f\x1e\x1d\x1a\x1c\x1c $.\' \",#\x1c\x1c(7),01444\x1f\'9=82<.342\xFF\xC0\x00\x0b\x08\x00\x01\x00\x01\x01\x01\x11\x00\xFF\xC4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b\xFF\xDA\x00\x08\x01\x01\x00\x00?\x00\xbf\x00\xFF\xD9" : 'fake');

        $response = $this->actingAs($this->pk)->post('/pk/kehadiran', [
            'id_klien' => $klien->id_klien,
            'tgl_hadir' => Carbon::today()->toDateString(),
            'jam_hadir' => '11:15',
            'status_kehadiran' => 'Hadir',
            'foto_kehadiran_base64' => $fakeBase64,
            'catatan' => 'Presensi via kamera real-time',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pk.kehadiran.index'));
        
        $kehadiran = \App\Models\KehadiranKlien::where('catatan', 'Presensi via kamera real-time')->first();
        $this->assertNotNull($kehadiran);
        $this->assertNotNull($kehadiran->foto_kehadiran);
        $this->assertTrue(Storage::disk('public')->exists($kehadiran->foto_kehadiran));
    }

    public function test_halaman_kehadiran_memiliki_elemen_kamera_realtime()
    {
        $response = $this->actingAs($this->pk)->get('/pk/kehadiran');
        $response->assertStatus(200);
        $response->assertSee('Kamera Real-Time');
        $response->assertSee('btn_mode_camera_tambah');
        $response->assertSee('camera_video_tambah');
        $response->assertSee('Ambil Foto');
    }
}
