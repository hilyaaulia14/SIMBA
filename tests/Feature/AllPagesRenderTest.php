<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;

class AllPagesRenderTest extends TestCase
{
    protected $admin;
    protected $pk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Pegawai::where('nip_usn', '198501152010121001')->first();
        $this->pk = Pegawai::where('nip_usn', '199003202015031002')->first();
    }

    public function test_all_admin_pages_render_status_200()
    {
        $routes = [
            '/admin/dashboard',
            '/admin/surat-masuk',
            '/admin/surat-keluar',
            '/admin/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $response->assertStatus(200);
            $response->assertSee('Poppins');
            $response->assertSee('#0D2A4A');
        }
    }

    public function test_all_pk_pages_render_status_200()
    {
        $routes = [
            '/pk/dashboard',
            '/pk/jadwal',
            '/pk/atur-jadwal',
            '/pk/klien',
            '/pk/kehadiran',
            '/pk/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->pk)->get($route);
            $response->assertStatus(200);
            $response->assertSee('Poppins');
            $response->assertSee('#0D2A4A');
        }
    }
}
