<?php

namespace App\Http\Controllers\PK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Pegawai;

class PKProfileController extends Controller
{
    public function index()
    {
        $pk = Auth::user();
        return view('pk.profile.index', compact('pk'));
    }

    public function update(Request $request)
    {
        /** @var Pegawai $pk */
        $pk = Auth::user();

        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:100',
            'nip_usn' => 'required|string|max:30|unique:pegawai,nip_usn,' . $pk->id_pegawai . ',id_pegawai',
            'jabatan' => 'required|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_pegawai.required' => 'Nama lengkap beserta gelar wajib diisi.',
            'nip_usn.required' => 'NIP (Username) wajib diisi.',
            'nip_usn.unique' => 'NIP sudah digunakan oleh pegawai lain.',
            'jabatan.required' => 'Jabatan fungsional PK wajib diisi.',
            'foto.image' => 'Berkas foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('foto')) {
            if ($pk->foto && Storage::disk('public')->exists($pk->foto)) {
                Storage::disk('public')->delete($pk->foto);
            }

            $file = $request->file('foto');
            $filename = 'pk_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('foto_pegawai', $filename, 'public');
            $validated['foto'] = $path;
        }

        $pk->update($validated);

        return redirect()->route('pk.profile.index')->with('success', 'Profil Pembimbing Kemasyarakatan berhasil diperbarui.');
    }
}
