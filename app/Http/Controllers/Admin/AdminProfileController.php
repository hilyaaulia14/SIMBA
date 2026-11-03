<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pegawai;

class AdminProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    public function update(Request $request)
    {
        /** @var Pegawai $admin */
        $admin = Auth::user();

        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:100',
            'nip_usn' => 'required|string|max:30|unique:pegawai,nip_usn,' . $admin->id_pegawai . ',id_pegawai',
            'jabatan' => 'required|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_pegawai.required' => 'Nama lengkap wajib diisi.',
            'nip_usn.required' => 'NIP (Username) wajib diisi.',
            'nip_usn.unique' => 'NIP sudah digunakan oleh pegawai lain.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'foto.image' => 'Berkas foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('foto')) {
            if ($admin->foto && Storage::disk('public')->exists($admin->foto)) {
                Storage::disk('public')->delete($admin->foto);
            }

            $file = $request->file('foto');
            $filename = 'admin_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('foto_pegawai', $filename, 'public');
            $validated['foto'] = $path;
        }

        $admin->update($validated);

        return redirect()->route('admin.profile.index')->with('success', 'Informasi profil Admin berhasil diperbarui.');
    }
}
