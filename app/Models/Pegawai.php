<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';

    protected $fillable = [
        'id_role',
        'nama_pegawai',
        'nip_usn',
        'password',
        'jabatan',
        'email',
        'alamat',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function jadwalBimbingan()
    {
        return $this->hasMany(JadwalBimbingan::class, 'id_pegawai', 'id_pegawai');
    }

    public function isAdmin(): bool
    {
        return $this->id_role === 1 || ($this->role && str_contains(strtolower($this->role->nama_role), 'admin'));
    }

    public function isPK(): bool
    {
        return $this->id_role === 2 || ($this->role && str_contains(strtolower($this->role->nama_role), 'pembimbing'));
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_pegawai) . '&background=0D2A4A&color=fff&bold=true';
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->nama_pegawai));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->nama_pegawai, 0, 2));
    }
}
