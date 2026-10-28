# Spesifikasi & Activity Diagram Lengkap - Sistem Informasi SIMBA
**Balai Pemasyarakatan (Bapas) Kelas I Banjarmasin**

> [!NOTE]
> Seluruh diagram dibuat dalam format **Hitam Putih (Black & White)** tanpa warna.
> 
> File Draw.io siap buka/edit tersedia di:
> - [`c:\laragon\www\SIMBA\diagram\activity_diagram_simba.drawio`](file:///c:/laragon/www/SIMBA/diagram/activity_diagram_simba.drawio)
> 
> *Struktur 6 Tab Sheet Lengkap di Draw.io:*
> 1. `1. Login Sistem (Multi-Role)`: **Alur Autentikasi Login Utama Pengguna (Admin & PK)** dari Mulai, Validasi NIP & Password, hingga Masuk Dashboard Selesai.
> 2. `2. CRUD Surat Masuk (Admin)`: Percabangan 4 Aksi (Tambah, Lihat Detail & Unduh PDF, Edit Data/File, Hapus).
> 3. `3. CRUD Surat Keluar (Admin)`: Percabangan 4 Aksi (Tambah, Lihat Detail & Unduh PDF, Edit Data/File, Hapus).
> 4. `4. CRUD Data Klien (PK)`: Percabangan 4 Aksi (Tambah Klien Baru, Detail Profil & Histori, Edit Status, Hapus).
> 5. `5. CRUD Jadwal Bimbingan (PK)`: Percabangan 4 Aksi (Tambah Jadwal, Detail Agenda, Reschedule/Edit, Batalkan/Hapus).
> 6. `6. Pencatatan Kehadiran Klien (PK)`: Upload Foto Fisik Presensi, Catatan Evaluasi, Update Status Selesai.

---

## 1. Activity Diagram: Login Sistem (Multi-Role Admin & PK)
Diagram ini menggambarkan alur gerbang masuk utama seluruh pengguna ke dalam sistem SIMBA menggunakan kredensial **NIP (sebagai username)** dan **Password**:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PENGGUNA ["Pengguna (Admin / Bagian Umum / PK)"]
        S1(( )):::startEnd
        L1["Mulai"]
        S1 --- L1
        L1 --> A1["Akses Halaman Login SIMBA"]:::bw
        A2["Tampilkan Form Login (NIP & Password)"]:::bw -.-> A3["Masukkan NIP (Username) & Password"]:::bw
        A3 --> A4["Klik Tombol Masuk"]:::bw
    end

    subgraph SISTEM ["Sistem SIMBA"]
        A1 --> A2
        A4 --> A5["Validasi Kredensial NIP & Password"]:::bw
        A5 --> D1{"Valid?"}:::bw
        D1 -- "Tidak" --> A_ERR["Tampilkan Pesan Peringatan Error:\nNIP atau Kata Sandi Salah!"]:::bw
        A_ERR -.-> A3
        D1 -- "Ya" --> D2{"Identifikasi\nRole?"}:::bw
        D2 -- "Admin / Bagian Umum" --> A_ADM["Arahkan & Tampilkan Dashboard Admin\n(Persuratan & Pengarsipan Digital)"]:::bw
        D2 -- "Pembimbing Kemasyarakatan" --> A_PK["Arahkan & Tampilkan Dashboard PK\n(3 Metrik: Hari Ini, Minggu Ini, Total Klien)"]:::bw
        
        A_ADM --> E1((( ))):::startEnd
        L_END1["Selesai"]
        E1 --- L_END1

        A_PK --> E2((( ))):::startEnd
        L_END2["Selesai"]
        E2 --- L_END2
    end
```

---

## 2. Activity Diagram: CRUD Surat Masuk (Admin / Bagian Umum)
Diagram ini menggambarkan alur kerja Admin Bagian Umum pada tabel surat masuk dengan 4 percabangan aksi CRUD:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN ["Admin / Bagian Umum"]
        S2(( )):::startEnd
        L2["Mulai"]
        S2 --- L2
        L2 --> B1["Akses Menu Surat Masuk"]:::bw
        B2["Tampilkan Tabel Daftar Surat Masuk &\nTombol Aksi (Tambah, Detail, Edit, Hapus)"]:::bw -.-> D_CRUD1{"Pilih Aksi\nCRUD?"}:::bw

        %% Cabang CREATE
        D_CRUD1 -- "[Tambah]" --> C1["Klik Tombol Tambah Surat"]:::bw
        C2["Tampilkan Modal Form Tambah"]:::bw -.-> C3["Isi Data Surat & Unggah File Scan PDF"]:::bw
        C3 --> C4["Klik Simpan Surat"]:::bw

        %% Cabang READ DETAIL
        D_CRUD1 -- "[Detail]" --> R1["Klik Tombol Lihat Detail"]:::bw
        R2["Tampilkan Modal Detail Surat &\nPratinjau Berkas PDF"]:::bw -.-> R3["Melihat Informasi / Unduh Dokumen"]:::bw

        %% Cabang UPDATE
        D_CRUD1 -- "[Edit]" --> U1["Klik Tombol Edit Surat"]:::bw
        U2["Tampilkan Modal Edit Data Terisi"]:::bw -.-> U3["Ubah Data / Ganti File PDF &\nKlik Simpan Perubahan"]:::bw

        %% Cabang DELETE
        D_CRUD1 -- "[Hapus]" --> D1["Klik Tombol Hapus Surat"]:::bw
        D2["Tampilkan Dialog Konfirmasi Hapus"]:::bw -.-> D3["Konfirmasi: Ya, Hapus Data"]:::bw
    end

    subgraph SISTEM2 ["Sistem SIMBA"]
        B1 --> B2
        C1 --> C2
        C4 --> C5["Validasi Data & Berkas PDF"]:::bw
        C5 --> D_VAL1{"Valid?"}:::bw
        D_VAL1 -- "Tidak" --> C_ERR["Tampilkan Peringatan Validasi Gagal"]:::bw
        C_ERR -.-> C3
        D_VAL1 -- "Ya" --> C6["Simpan Data ke DB &\nSimpan File PDF ke Storage"]:::bw

        R1 --> R2
        U1 --> U2
        U3 --> U4["Validasi & Perbarui Data di DB &\nPerbarui Berkas di Storage"]:::bw

        D1 --> D2
        D3 --> D4["Hapus Rekaman Data dari DB &\nHapus File PDF dari Storage"]:::bw

        C6 --> MERGE1["Perbarui Tampilan Tabel Surat Masuk\ndan Tampilkan Notifikasi Berhasil"]:::bw
        R3 --> MERGE1
        U4 --> MERGE1
        D4 --> MERGE1

        MERGE1 --> E3((( ))):::startEnd
        L_END3["Selesai"]
        E3 --- L_END3
    end
```

---

## 3. Activity Diagram: CRUD Data Klien Pemasyarakatan (PK)
Diagram ini menggambarkan alur kerja Pembimbing Kemasyarakatan dalam mengelola master data klien bapas dengan 4 percabangan aksi CRUD:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PK ["Pembimbing Kemasyarakatan (PK)"]
        S3(( )):::startEnd
        L3["Mulai"]
        S3 --- L3
        L3 --> K1["Akses Menu Data Klien"]:::bw
        K2["Tampilkan Tabel Master Klien &\nTombol Aksi (Tambah, Detail, Edit, Hapus)"]:::bw -.-> D_CRUD2{"Pilih Aksi\nCRUD?"}:::bw

        %% Cabang CREATE
        D_CRUD2 -- "[Tambah]" --> KC1["Klik Tombol Tambah Klien"]:::bw
        KC2["Tampilkan Modal Form Registrasi Klien"]:::bw -.-> KC3["Isi Data Klien (Tanpa Jenis Bimbingan)"]:::bw
        KC3 --> KC4["Klik Simpan Data Klien"]:::bw

        %% Cabang READ DETAIL
        D_CRUD2 -- "[Detail]" --> KR1["Klik Tombol Detail Klien"]:::bw
        KR2["Tampilkan Modal Detail Komprehensif:\nProfil Klien, Riwayat Bimbingan, Foto Presensi"]:::bw -.-> KR3["Melihat Riwayat & Evaluasi"]:::bw

        %% Cabang UPDATE
        D_CRUD2 -- "[Edit]" --> KU1["Klik Tombol Edit Klien"]:::bw
        KU2["Tampilkan Modal Edit Data Terisi"]:::bw -.-> KU3["Perbarui Data / Ubah Status Klien\n(Aktif, Selesai, Cabut) & Klik Simpan"]:::bw

        %% Cabang DELETE
        D_CRUD2 -- "[Hapus]" --> KD1["Klik Tombol Hapus Klien"]:::bw
        KD2["Tampilkan Dialog Konfirmasi Hapus"]:::bw -.-> KD3["Konfirmasi: Ya, Hapus Klien"]:::bw
    end

    subgraph SISTEM3 ["Sistem SIMBA"]
        K1 --> K2
        KC1 --> KC2
        KC4 --> KC5["Validasi Keunikan No. Register & Identitas"]:::bw
        KC5 --> D_VAL2{"Valid?"}:::bw
        D_VAL2 -- "Tidak" --> KC_ERR["Tampilkan Peringatan No. Register Duplikat"]:::bw
        KC_ERR -.-> KC3
        D_VAL2 -- "Ya" --> KC6["Simpan Data Klien Baru ke DB &\nRelasikan ke PK Pendamping Terkait"]:::bw

        KR1 --> KR2
        KU1 --> KU2
        KU3 --> KU4["Validasi & Perbarui Data Klien di DB"]:::bw

        KD1 --> KD2
        KD3 --> KD4["Hapus Rekaman Data Klien dari DB"]:::bw

        KC6 --> MERGE2["Perbarui Tampilan Tabel Data Klien\ndan Tampilkan Notifikasi Berhasil"]:::bw
        KR3 --> MERGE2
        KU4 --> MERGE2
        KD4 --> MERGE2

        MERGE2 --> E4((( ))):::startEnd
        L_END4["Selesai"]
        E4 --- L_END4
    end
```
