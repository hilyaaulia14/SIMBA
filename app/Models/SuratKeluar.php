<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';
    protected $primaryKey = 'id_surat_keluar';

    protected $fillable = [
        'id_surat',
        'no_surat',
        'tujuan_surat',
        'tgl_keluar',
        'perihal',
        'lampiran',
        'keterangan',
        'upload_dokumen',
    ];

    protected $casts = [
        'tgl_keluar' => 'date',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'id_surat', 'id_surat');
    }

    public function getDokumenUrlAttribute(): ?string
    {
        return $this->upload_dokumen ? asset('storage/' . $this->upload_dokumen) : null;
    }
}
