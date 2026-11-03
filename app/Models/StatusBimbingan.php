<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusBimbingan extends Model
{
    use HasFactory;

    protected $table = 'status_bimbingan';
    protected $primaryKey = 'id_status_bimbingan';

    protected $fillable = [
        'nama_status',
    ];

    public function jadwalBimbingan()
    {
        return $this->hasMany(JadwalBimbingan::class, 'id_status_bimbingan', 'id_status_bimbingan');
    }
}
