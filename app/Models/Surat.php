<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';
    protected $primaryKey = 'id_surat';

    protected $fillable = [
        'nama_surat',
    ];

    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'id_surat', 'id_surat');
    }

    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'id_surat', 'id_surat');
    }
}
