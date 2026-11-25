# Spesifikasi Physical Data Model (PDM) Sistem Informasi SIMBA
**Balai Pemasyarakatan (Bapas) Kelas I Banjarmasin**

> [!NOTE]
> Diagram PDM (*Physical Data Model*) ini merupakan representasi tingkat fisik dari basis data MySQL aplikasi SIMBA, mengikuti standar visual **PowerDesigner PDM (Format Cyan `#E5FFFF` / `#00AAAA`)** dengan **Tata Letak Bebas Tabrakan (*Zero Line Collision*)**:
> 1. **Struktur Tabel Fisik 2 Kompartemen**:
>    - **Baris Judul (*Table Name*)**: Berisi nama tabel fisik basis data (`role`, `pegawai`, `surat`, `surat_masuk`, `surat_keluar`, `klien`, `atur_jadwal`, `status_bimbingan`, `jadwal_bimbingan`, `kehadiran_klien`) berlatar cyan (`#E5FFFF`).
>    - **Daftar Kolom Fisik (*Columns*)**: Berisi nama kolom fisik, tipe data DBMS MySQL (`bigint`, `varchar(n)`, `date`, `time`, `text`), serta batasan fisik `<pk>`, `<fk>`, `<fk1..3>`, `<unique>`, dan `<not null>`.
>    - **Kompartemen Bawah `<pi>` Ditiadakan**: Sesuai kaidah baku PowerDesigner PDM, penanda identifier konseptual ditiadakan dan digantikan oleh batasan kunci fisik (`<pk>` dan `<fk>`).
> 2. **Notasi Kardinalitas Relasi PowerDesigner PDM**:
>    - Ujung tabel induk (*Primary Key Table*): Simbol garis tegak lurus `|` (*Mandatory One*).
>    - Ujung tabel anak (*Foreign Key Table*): Simbol cakar gagak bergaris `>-|` (*Mandatory Many*).
>    - Garis relasi menghubungkan tabel induk ke tabel anak dengan aliran ortogonal bersih tanpa memotong garis lain.

---

## Berkas Diagram yang Tersedia

Diagram PDM tersimpan pada direktori [`diagram/`](file:///c:/laragon/www/SIMBA/diagram):

| No | Nama Berkas | Tipe Berkas | Deskripsi & Lokasi Akses |
|---|---|---|---|
| 1 | **[`pdm_simba.drawio`](file:///c:/laragon/www/SIMBA/diagram/pdm_simba.drawio)** | Standalone Draw.io | Berkas mandiri Draw.io khusus PDM SIMBA untuk dibuka & diedit langsung di VS Code. |
| 2 | **[`pdm_simba.svg`](file:///c:/laragon/www/SIMBA/diagram/pdm_simba.svg)** | Vektor SVG Standar | Gambar vektor resolusi tinggi siap pratinjau langsung di peramban web maupun laporan dokumen. |
| 3 | **[`pdm_simba.png`](file:///c:/laragon/www/SIMBA/diagram/pdm_simba.png)** | Raster PNG | Berkas gambar PNG resolusi tinggi siap disematkan ke dalam laporan/skripsi. |

---

## 1. Visualisasi Mermaid PDM SIMBA

```mermaid
erDiagram
    direction TB

    role ||--|{ pegawai : "mempunyai"
    pegawai ||--|{ surat : "mengelola"
    surat ||--|{ surat_masuk : "mengarsipkan"
    surat ||--|{ surat_keluar : "mencatat"

    pegawai ||--|{ jadwal_bimbingan : "menjadwalkan"
    klien ||--|{ atur_jadwal : "menerima"
    klien ||--|{ jadwal_bimbingan : "memiliki"
    klien ||--|{ kehadiran_klien : "menghadiri"
    status_bimbingan ||--|{ jadwal_bimbingan : "menandai"
    jadwal_bimbingan ||--|| kehadiran_klien : "memverifikasi"

    role {
        bigint id_role PK
        varchar_50 nama_role "NOT NULL"
    }

    pegawai {
        bigint id_pegawai PK
        bigint id_role FK "NOT NULL"
        varchar_100 nama_pegawai "NOT NULL"
        varchar_30 nip_usn "UNIQUE, NOT NULL"
        varchar_255 password "NOT NULL"
        varchar_50 jabatan "NOT NULL"
        varchar_100 email "NULL"
        text alamat "NULL"
        varchar_255 foto "NULL"
    }

    surat {
        bigint id_surat PK
        varchar_100 nama_surat "NOT NULL"
    }

    surat_masuk {
        bigint id_surat_masuk PK
        bigint id_surat FK "NOT NULL"
        varchar_100 no_surat "NOT NULL"
        varchar_150 asal_surat "NOT NULL"
        date tgl_masuk "NOT NULL"
        date tgl_diterima "NULL"
        varchar_150 tujuan "NULL"
        varchar_255 perihal "NOT NULL"
        varchar_100 lampiran "NULL"
        text keterangan "NULL"
        varchar_255 upload_dokumen "NULL"
    }

    surat_keluar {
        bigint id_surat_keluar PK
        bigint id_surat FK "NOT NULL"
        varchar_100 no_surat "NOT NULL"
        varchar_150 tujuan_surat "NOT NULL"
        date tgl_keluar "NOT NULL"
        varchar_255 perihal "NOT NULL"
        varchar_100 lampiran "NULL"
        text keterangan "NULL"
        varchar_255 upload_dokumen "NULL"
    }

    klien {
        bigint id_klien PK
        varchar_50 no_register "NOT NULL"
        varchar_100 nama_klien "NOT NULL"
        varchar_150 asal_upt "NULL"
        varchar_30 telepon "NULL"
        varchar_20 jenis_kelamin "NOT NULL"
        date tgl_lahir "NULL"
        time jam_mulai "NULL"
        varchar_100 tindak_pidana "NOT NULL"
        varchar_50 jenis_bimbingan "NULL"
        text alamat "NULL"
        text keterangan "NULL"
    }

    atur_jadwal {
        bigint id_atur_jadwal PK
        bigint id_klien FK "NOT NULL"
        varchar_50 hari "NOT NULL"
        time jam_mulai "NOT NULL"
    }

    status_bimbingan {
        bigint id_status_bimbingan PK
        varchar_50 nama_status "NOT NULL"
    }

    jadwal_bimbingan {
        bigint id_jadwal_bimbingan PK
        bigint id_pegawai FK1 "NOT NULL"
        bigint id_klien FK2 "NOT NULL"
        bigint id_status_bimbingan FK3 "NOT NULL"
        date tgl_bimbingan "NOT NULL"
        time jam_mulai "NOT NULL"
        varchar_255 keterangan "NULL"
    }

    kehadiran_klien {
        bigint id_kehadiran PK
        bigint id_jadwal_bimbingan FK1 "NULL"
        bigint id_klien FK2 "NOT NULL"
        date tgl_hadir "NOT NULL"
        time jam_hadir "NOT NULL"
        varchar_255 foto_kehadiran "NULL"
        varchar_50 status_kehadiran "NOT NULL"
        varchar_255 catatan "NULL"
    }
```

---

## 2. Kamus Data Fisik MySQL (PDM)

| Tabel Fisik | Daftar Kolom Fisik, Tipe Data & Batasan Relasional | Batasan Kunci (*Key Constraints*) | Keterangan Tabel Fisik |
|---|---|---|---|
| **`role`** | `id_role : bigint <pk>`, `nama_role : varchar(50) <not null>` | PRIMARY KEY (`id_role`) | Tabel referensi hak akses sistem. |
| **`pegawai`** | `id_pegawai : bigint <pk>`, `id_role : bigint <fk>`, `nama_pegawai : varchar(100)`, `nip_usn : varchar(30) <unique>`, `password : varchar(255)`, `jabatan : varchar(50)`, `email : varchar(100)`, `alamat : text`, `foto : varchar(255)` | PRIMARY KEY (`id_pegawai`), FOREIGN KEY (`id_role`) REFERENCES `role`(`id_role`) | Tabel akun pengguna pegawai Bapas. |
| **`surat`** | `id_surat : bigint <pk>`, `nama_surat : varchar(100) <not null>` | PRIMARY KEY (`id_surat`) | Tabel klasifikasi surat dinas. |
| **`surat_masuk`** | `id_surat_masuk : bigint <pk>`, `id_surat : bigint <fk>`, `no_surat : varchar(100)`, `asal_surat : varchar(150)`, `tgl_masuk : date`, `tgl_diterima : date`, `tujuan : varchar(150)`, `perihal : varchar(255)`, `lampiran : varchar(100)`, `keterangan : text`, `upload_dokumen : varchar(255)` | PRIMARY KEY (`id_surat_masuk`), FOREIGN KEY (`id_surat`) REFERENCES `surat`(`id_surat`) | Tabel catatan berkas surat masuk. |
| **`surat_keluar`** | `id_surat_keluar : bigint <pk>`, `id_surat : bigint <fk>`, `no_surat : varchar(100)`, `tujuan_surat : varchar(150)`, `tgl_keluar : date`, `perihal : varchar(255)`, `lampiran : varchar(100)`, `keterangan : text`, `upload_dokumen : varchar(255)` | PRIMARY KEY (`id_surat_keluar`), FOREIGN KEY (`id_surat`) REFERENCES `surat`(`id_surat`) | Tabel catatan berkas surat keluar. |
| **`klien`** | `id_klien : bigint <pk>`, `no_register : varchar(50) <not null>`, `nama_klien : varchar(100) <not null>`, `asal_upt : varchar(150)`, `telepon : varchar(30)`, `jenis_kelamin : varchar(20)`, `tgl_lahir : date`, `jam_mulai : time`, `tindak_pidana : varchar(100)`, `jenis_bimbingan : varchar(50)`, `alamat : text`, `keterangan : text` | PRIMARY KEY (`id_klien`) | Tabel master klien pemasyarakatan. |
| **`atur_jadwal`** | `id_atur_jadwal : bigint <pk>`, `id_klien : bigint <fk>`, `hari : varchar(50) <not null>`, `jam_mulai : time <not null>` | PRIMARY KEY (`id_atur_jadwal`), FOREIGN KEY (`id_klien`) REFERENCES `klien`(`id_klien`) | Tabel ketersediaan hari bimbingan mingguan. |
| **`status_bimbingan`** | `id_status_bimbingan : bigint <pk>`, `nama_status : varchar(50) <not null>` | PRIMARY KEY (`id_status_bimbingan`) | Tabel master status jadwal bimbingan. |
| **`jadwal_bimbingan`** | `id_jadwal_bimbingan : bigint <pk>`, `id_pegawai : bigint <fk1>`, `id_klien : bigint <fk2>`, `id_status_bimbingan : bigint <fk3>`, `tgl_bimbingan : date`, `jam_mulai : time`, `keterangan : varchar(255)` | PRIMARY KEY (`id_jadwal_bimbingan`), FOREIGN KEYs to `pegawai`, `klien`, `status_bimbingan` | Tabel jadwal pertemuan tatap muka klien. |
| **`kehadiran_klien`** | `id_kehadiran : bigint <pk>`, `id_jadwal_bimbingan : bigint <fk1>`, `id_klien : bigint <fk2>`, `tgl_hadir : date`, `jam_hadir : time`, `foto_kehadiran : varchar(255)`, `status_kehadiran : varchar(50)`, `catatan : varchar(255)` | PRIMARY KEY (`id_kehadiran`), FOREIGN KEYs to `jadwal_bimbingan` and `klien` | Tabel presensi kehadiran fisik klien di Bapas. |

---

## 3. Matriks Relasi Fisik MySQL (PDM)

| No | Relasi Fisik | Tabel Induk (Parent) | Kardinalitas | Tabel Anak (Child) | Simbol Induk | Simbol Anak | Definisi Foreign Key DDL |
|---|---|---|---|---|---|---|---|
| 1 | `mempunyai` | `role` | $1 : N$ | `pegawai` | `|` | `>-|` | `FOREIGN KEY (id_role) REFERENCES role(id_role) ON DELETE CASCADE` |
| 2 | `mengelola` | `pegawai` | $1 : N$ | `surat` | `|` | `>-|` | Relasi logis administrasi tata kelola surat |
| 3 | `mengarsipkan` | `surat` | $1 : N$ | `surat_masuk` | `|` | `>-|` | `FOREIGN KEY (id_surat) REFERENCES surat(id_surat) ON DELETE CASCADE` |
| 4 | `mencatat` | `surat` | $1 : N$ | `surat_keluar` | `|` | `>-|` | `FOREIGN KEY (id_surat) REFERENCES surat(id_surat) ON DELETE CASCADE` |
| 5 | `menjadwalkan` | `pegawai` | $1 : N$ | `jadwal_bimbingan` | `|` | `>-|` | `FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai) ON DELETE CASCADE` |
| 6 | `menandai` | `status_bimbingan` | $1 : N$ | `jadwal_bimbingan` | `|` | `>-|` | `FOREIGN KEY (id_status_bimbingan) REFERENCES status_bimbingan(id_status_bimbingan)` |
| 7 | `menerima` | `klien` | $1 : N$ | `atur_jadwal` | `|` | `>-|` | `FOREIGN KEY (id_klien) REFERENCES klien(id_klien) ON DELETE CASCADE` |
| 8 | `memiliki` | `klien` | $1 : N$ | `jadwal_bimbingan` | `|` | `>-|` | `FOREIGN KEY (id_klien) REFERENCES klien(id_klien) ON DELETE CASCADE` |
| 9 | `menghadiri` | `klien` | $1 : N$ | `kehadiran_klien` | `|` | `>-|` | `FOREIGN KEY (id_klien) REFERENCES klien(id_klien) ON DELETE CASCADE` |
| 10 | `memverifikasi` | `jadwal_bimbingan` | $1 : 1$ | `kehadiran_klien` | `|` | `|` | `FOREIGN KEY (id_jadwal_bimbingan) REFERENCES jadwal_bimbingan(id_jadwal_bimbingan) ON DELETE SET NULL` |
