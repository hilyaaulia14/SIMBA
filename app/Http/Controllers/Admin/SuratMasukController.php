<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratMasuk;
use App\Models\Surat;

class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratMasuk::with('surat')->latest('tgl_masuk');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('asal_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_surat')) {
            $query->where('id_surat', $request->id_surat);
        }

        $suratMasukList = $query->paginate(10)->withQueryString();
        $klasifikasiSurat = Surat::all();

        return view('admin.surat-masuk.index', compact('suratMasukList', 'klasifikasiSurat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_surat' => 'nullable|exists:surat,id_surat',
            'no_surat' => 'required|string|max:50',
            'asal_surat' => 'required|string|max:100',
            'tgl_masuk' => 'required|date',
            'tgl_diterima' => 'nullable|date',
            'tujuan' => 'nullable|string|max:150',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'upload_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'no_surat.required' => 'Nomor agenda / surat wajib diisi.',
            'asal_surat.required' => 'Asal pengirim surat wajib diisi.',
            'tgl_masuk.required' => 'Tanggal masuk wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'upload_dokumen.max' => 'Ukuran berkas tidak boleh melebihi 10MB.',
            'upload_dokumen.mimes' => 'Format berkas harus PDF, DOC, DOCX, JPG, atau PNG.',
        ]);

        if (empty($validated['id_surat'])) {
            $validated['id_surat'] = 1;
        }

        if (empty($validated['tgl_diterima'])) {
            $validated['tgl_diterima'] = $validated['tgl_masuk'];
        }

        if ($request->hasFile('upload_dokumen')) {
            $file = $request->file('upload_dokumen');
            $filename = 'surat_masuk_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat', $filename, 'public');
            $validated['upload_dokumen'] = $path;
        }

        SuratMasuk::create($validated);

        return redirect()->route('admin.surat-masuk.index')->with('success', 'Data Surat Masuk berhasil ditambahkan ke dalam arsip.');
    }

    public function show($id)
    {
        $suratMasuk = SuratMasuk::with('surat')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $suratMasuk,
                'dokumen_url' => $suratMasuk->dokumen_url,
                'formatted_date' => $suratMasuk->tgl_masuk->translatedFormat('d F Y'),
            ]);
        }

        return view('admin.surat-masuk.show', compact('suratMasuk'));
    }

    public function update(Request $request, $id)
    {
        $suratMasuk = SuratMasuk::findOrFail($id);

        $validated = $request->validate([
            'id_surat' => 'nullable|exists:surat,id_surat',
            'no_surat' => 'required|string|max:50',
            'asal_surat' => 'required|string|max:100',
            'tgl_masuk' => 'required|date',
            'tgl_diterima' => 'nullable|date',
            'tujuan' => 'nullable|string|max:150',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'upload_dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'no_surat.required' => 'Nomor agenda / surat wajib diisi.',
            'asal_surat.required' => 'Asal pengirim surat wajib diisi.',
            'tgl_masuk.required' => 'Tanggal masuk wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'upload_dokumen.max' => 'Ukuran berkas tidak boleh melebihi 10MB.',
        ]);

        if (empty($validated['id_surat'])) {
            $validated['id_surat'] = $suratMasuk->id_surat ?? 1;
        }

        if (empty($validated['tgl_diterima'])) {
            $validated['tgl_diterima'] = $validated['tgl_masuk'];
        }

        if ($request->hasFile('upload_dokumen')) {
            // Delete old file if exists
            if ($suratMasuk->upload_dokumen && Storage::disk('public')->exists($suratMasuk->upload_dokumen)) {
                Storage::disk('public')->delete($suratMasuk->upload_dokumen);
            }

            $file = $request->file('upload_dokumen');
            $filename = 'surat_masuk_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat', $filename, 'public');
            $validated['upload_dokumen'] = $path;
        }

        $suratMasuk->update($validated);

        return redirect()->route('admin.surat-masuk.index')->with('success', 'Data Surat Masuk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $suratMasuk = SuratMasuk::findOrFail($id);

        if ($suratMasuk->upload_dokumen && Storage::disk('public')->exists($suratMasuk->upload_dokumen)) {
            Storage::disk('public')->delete($suratMasuk->upload_dokumen);
        }

        $suratMasuk->delete();

        return redirect()->route('admin.surat-masuk.index')->with('success', 'Data Surat Masuk berhasil dihapus.');
    }

    public function download($id)
    {
        $suratMasuk = SuratMasuk::findOrFail($id);

        if (!$suratMasuk->upload_dokumen || !Storage::disk('public')->exists($suratMasuk->upload_dokumen)) {
            return back()->with('error', 'Berkas digital surat ini belum diunggah atau tidak ditemukan.');
        }

        return Storage::disk('public')->download($suratMasuk->upload_dokumen);
    }
}
