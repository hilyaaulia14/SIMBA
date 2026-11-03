<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalBimbingan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_bimbingan';
    protected $primaryKey = 'id_jadwal_bimbingan';

    protected $fillable = [
        'id_pegawai',
        'id_klien',
        'id_status_bimbingan',
        'tgl_bimbingan',
        'jam_mulai',
        'keterangan',
    ];

    protected $casts = [
        'tgl_bimbingan' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function klien()
    {
        return $this->belongsTo(Klien::class, 'id_klien', 'id_klien');
    }

    public function statusBimbingan()
    {
        return $this->belongsTo(StatusBimbingan::class, 'id_status_bimbingan', 'id_status_bimbingan');
    }

    public function kehadiran()
    {
        return $this->hasOne(KehadiranKlien::class, 'id_jadwal_bimbingan', 'id_jadwal_bimbingan');
    }
}
