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
}
