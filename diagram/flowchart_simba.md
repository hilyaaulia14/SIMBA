# Spesifikasi & Flowchart Sistem SIMBA (Hitam Putih Murni Sesuai Standar ANSI/ISO)
**Balai Pemasyarakatan (Bapas) Kelas I Banjarmasin**

> [!NOTE]
> Flowchart sistem ini dirancang mengikuti kaidah baku simbol bagan alir (ANSI/ISO Flowchart) sesuai contoh referensi yang diberikan:
> - **Terminator (Oval / Kapsul)**: `Mulai` dan `Selesai`.
> - **Input / Output (Jajar Genjang / Parallelogram)**: Operasi masukan data oleh pengguna atau luaran tampilan dari sistem.
> - **Process (Persegi Panjang)**: Proses pemrosesan data, validasi, komputasi, atau manipulasi basis data.
> - **Decision (Belah Ketupat / Rhombus)**: Titik evaluasi kondisi percabangan logika (*Ya* / *Tidak* / *Pilihan Role/Menu*).
> - **Gaya Tampilan**: **100% Hitam Putih (Black & White)** rapi, simetris, dan bebas tabrakan garis (*zero line collision*).

Berkas diagram siap buka dan langsung dapat diedit di Draw.io tersedia pada:
- [`c:\laragon\www\SIMBA\diagram\flowchart_simba.drawio`](file:///c:/laragon/www/SIMBA/diagram/flowchart_simba.drawio)

---

## Daftar Lembar Kerja (8 Sheet Tabs pada Draw.io)

| No | Tab Name di Draw.io | Deskripsi Alur Program |
|---|---|---|
| 1 | `1. Flowchart Utama Sistem` | Alur menyeluruh (*Macro / Overview*) dari Login, percabangan Role (Admin vs PK), navigasi menu utama, hingga Logout. |
| 2 | `2. Flowchart Login Sistem` | Logika autentikasi NIP & password, pencocokan hash DB, penanganan salah sandi, dan pembentukan sesi. |
| 3 | `3. Flowchart Surat Masuk` | Pengelolaan lengkap surat masuk: Tambah & upload berkas PDF, Lihat Detail & Preview PDF, serta Hapus berkas & data DB. |
| 4 | `4. Flowchart Surat Keluar` | Pengelolaan lengkap surat keluar: Tambah & upload berkas PDF, Lihat Detail & Preview PDF, serta Hapus berkas & data DB. |
| 5 | `5. Flowchart Data Klien` | Registrasi klien *(tanpa jenis bimbingan)*, validasi keunikan No Register Bapas, update metrik Total Klien (+1/-1), dan Hapus Klien. |
| 6 | `6. Flowchart Atur Jadwal (PK)` | Pengaturan ketersediaan hari bimbingan & jam mulai *(tanpa kuota dan tanpa jam selesai)*. |
| 7 | `7. Flowchart Penjadwalan Bimbingan` | Pembuatan janji temu bimbingan tatap muka, pengecekan bentrok jadwal, dan pembaruan counter metrik dashboard PK. |
| 8 | `8. Flowchart Presensi & Bukti Hadir` | Verifikasi wajib lapor fisik, upload berkas foto presensi ke storage, input evaluasi, dan otomasi status bimbingan menjadi "Selesai". |

---

## 1. Flowchart Sistem Utama (Macro / Overview SIMBA)

Menggambarkan alur logika global sistem dari pengguna mengakses SIMBA, melakukan autentikasi, bercabang sesuai hak akses (*Role Admin* atau *Role PK*), mengeksekusi modul-modul fungsional, hingga mengakhiri sesi (*Logout*):

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    START(["Mulai"]):::term
    IN1[/"Input NIP & Password"/]:::bw
    P1["Validasi Kredensial Pengguna di DB"]:::bw
    D1{"Kredensial Valid?"}:::bw
    ERR1[/"Tampilkan Pesan: NIP / Password Salah!"/]:::bw
    D2{"Cek Role?"}:::bw

    %% Cabang Admin
    P_ADM["Buka Dashboard Admin (Persuratan)"]:::bw
    D_ADM{"Pilih Menu Admin?"}:::bw
    P_SM["Kelola Surat Masuk (CRUD & Arsip PDF)"]:::bw
    P_SK["Kelola Surat Keluar (CRUD & Arsip PDF)"]:::bw

    %% Cabang PK
    P_PK["Buka Dashboard PK (3 Metrik Bapas)"]:::bw
    D_PK{"Pilih Menu PK?"}:::bw
    P_KLN["Kelola Data Klien Pemasyarakatan"]:::bw
    P_SCH["Atur Ketersediaan Jadwal Bimbingan"]:::bw
    P_BIMB["Penjadwalan Pertemuan Bimbingan"]:::bw
    P_PRES["Pencatatan Presensi & Foto Bukti Hadir"]:::bw

    %% Logout & Selesai
    P_OUT["Pilih Menu Logout & Hapus Session Pengguna"]:::bw
    FINISH(["Selesai"]):::term

    START --> IN1
    IN1 --> P1
    P1 --> D1
    D1 -- "Tidak" --> ERR1
    ERR1 --> IN1
    D1 -- "Ya" --> D2

    D2 -- "Admin" --> P_ADM
    P_ADM --> D_ADM
    D_ADM -- "Surat Masuk" --> P_SM
    D_ADM -- "Surat Keluar" --> P_SK
    D_ADM -- "Logout" --> P_OUT
    P_SM --> P_OUT
    P_SK --> P_OUT

    D2 -- "PK" --> P_PK
    P_PK --> D_PK
    D_PK -- "Data Klien" --> P_KLN
    D_PK -- "Atur Jadwal" --> P_SCH
    D_PK -- "Penjadwalan" --> P_BIMB
    D_PK -- "Presensi" --> P_PRES
    D_PK -- "Logout" --> P_OUT
    P_KLN --> P_OUT
    P_SCH --> P_OUT
    P_BIMB --> P_OUT
    P_PRES --> P_OUT

    P_OUT --> FINISH
```

---

## 2. Flowchart Login Sistem & Hak Akses

Diagram alir program rinci untuk proses verifikasi identitas pengguna menggunakan NIP (Nomor Induk Pegawai) sebagai username:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    T2_START(["Mulai"]):::term
    T2_P1["Akses Halaman Login SIMBA"]:::bw
    T2_IN[/"Masukkan NIP & Password"/]:::bw
    T2_P2["Cari NIP & Cocokkan Hash Password di Database"]:::bw
    T2_D1{"Kredensial Sesuai?"}:::bw
    T2_ERR[/"Tampilkan Peringatan: NIP atau Password Salah!"/]:::bw
    T2_P3["Inisialisasi Session Pengguna di Server"]:::bw
    T2_D2{"Cek Role Pengguna?"}:::bw
    T2_OUT_ADM[/"Arahkan ke Dashboard Admin (Persuratan)"/]:::bw
    T2_OUT_PK[/"Arahkan ke Dashboard PK (3 Metrik Kartu)"/]:::bw
    T2_END(["Selesai"]):::term

    T2_START --> T2_P1
    T2_P1 --> T2_IN
    T2_IN --> T2_P2
    T2_P2 --> T2_D1
    T2_D1 -- "Tidak" --> T2_ERR
    T2_ERR --> T2_IN
    T2_D1 -- "Ya" --> T2_P3
    T2_P3 --> T2_D2
    T2_D2 -- "Admin" --> T2_OUT_ADM
    T2_D2 -- "PK" --> T2_OUT_PK
    T2_OUT_ADM --> T2_END
    T2_OUT_PK --> T2_END
```

---

## 3. Flowchart Pengelolaan Surat Masuk (Admin / Bagian Umum)

Diagram alir untuk seluruh siklus hidup arsip surat kedinasan masuk, meliputi penambahan berkas PDF baru, peninjauan detail & unduh dokumen, serta penghapusan fisik berkas dari server:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    SM_START(["Mulai"]):::term
    SM_P1["Akses Halaman Surat Masuk & Query Database"]:::bw
    SM_OUT1[/"Tampilkan Tabel Data Surat Masuk & Tombol Aksi"/]:::bw
    SM_D_OPSI{"Pilih Operasi?"}:::bw

    %% Sub-Alur Tambah
    SM_IN_ADD[/"Input No Surat, Pengirim, Perihal, Tgl, & File PDF"/]:::bw
    SM_P_VAL["Validasi Kelengkapan Field & Format Berkas PDF"]:::bw
    SM_D_VAL{"Valid?"}:::bw
    SM_ERR_ADD[/"Tampilkan Pesan Error Validasi"/]:::bw
    SM_P_SAVE["Simpan PDF ke /storage/surat/ & Record ke Database"]:::bw
    SM_OUT_ADD[/"Tampilkan Notifikasi Sukses & Reload Tabel"/]:::bw

    %% Sub-Alur Detail & Unduh
    SM_IN_DET[/"Klik Tombol Detail pada Baris Surat"/]:::bw
    SM_P_DET["Ambil Detail Surat & Siapkan Berkas PDF"]:::bw
    SM_OUT_DET[/"Tampilkan Modal Detail & Pratinjau PDF Langsung"/]:::bw
    SM_D_DL{"Unduh Berkas?"}:::bw
    SM_P_DL["Stream Download File Scan PDF ke Komputer"]:::bw
    SM_OUT_DL[/"File PDF Berhasil Diunduh"/]:::bw

    %% Sub-Alur Hapus
    SM_IN_DEL[/"Klik Tombol Hapus pada Baris Surat"/]:::bw
    SM_OUT_CONF[/"Tampilkan Modal Dialog Konfirmasi Hapus"/]:::bw
    SM_D_DEL{"Konfirmasi Hapus?"}:::bw
    SM_P_CAN["Tutup Modal & Batalkan Penghapusan"]:::bw
    SM_P_EXEC["Hapus Berkas PDF dari Storage & Hapus Record dari DB"]:::bw
    SM_OUT_DEL[/"Tampilkan Notifikasi Berhasil Hapus & Reload Tabel"/]:::bw

    SM_END(["Selesai"]):::term

    SM_START --> SM_P1
    SM_P1 --> SM_OUT1
    SM_OUT1 --> SM_D_OPSI

    %% Tambah
    SM_D_OPSI -- "Tambah" --> SM_IN_ADD
    SM_IN_ADD --> SM_P_VAL
    SM_P_VAL --> SM_D_VAL
    SM_D_VAL -- "Tidak" --> SM_ERR_ADD
    SM_ERR_ADD --> SM_IN_ADD
    SM_D_VAL -- "Ya" --> SM_P_SAVE
    SM_P_SAVE --> SM_OUT_ADD
    SM_OUT_ADD --> SM_END

    %% Detail
    SM_D_OPSI -- "Detail" --> SM_IN_DET
    SM_IN_DET --> SM_P_DET
    SM_P_DET --> SM_OUT_DET
    SM_OUT_DET --> SM_D_DL
    SM_D_DL -- "Ya" --> SM_P_DL
    SM_P_DL --> SM_OUT_DL
    SM_OUT_DL --> SM_END
    SM_D_DL -- "Tidak" --> SM_END

    %% Hapus
    SM_D_OPSI -- "Hapus" --> SM_IN_DEL
    SM_IN_DEL --> SM_OUT_CONF
    SM_OUT_CONF --> SM_D_DEL
    SM_D_DEL -- "Batal" --> SM_P_CAN
    SM_P_CAN --> SM_END
    SM_D_DEL -- "Ya, Hapus" --> SM_P_EXEC
    SM_P_EXEC --> SM_OUT_DEL
    SM_OUT_DEL --> SM_END
```

---

## 4. Flowchart Pengelolaan Surat Keluar (Admin / Bagian Umum)

Diagram alir untuk pengelolaan surat dinas bapas yang ditujukan ke instansi luar:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    SK_START(["Mulai"]):::term
    SK_P1["Akses Menu Surat Keluar & Query Database"]:::bw
    SK_OUT1[/"Tampilkan Tabel Data Surat Keluar & Tombol Aksi"/]:::bw
    SK_D_OPSI{"Pilih Operasi?"}:::bw

    %% Tambah
    SK_IN_ADD[/"Input No Surat, Instansi Tujuan, Perihal, Tgl, & File PDF"/]:::bw
    SK_P_VAL["Validasi Kelengkapan Isian & Format PDF"]:::bw
    SK_D_VAL{"Valid?"}:::bw
    SK_ERR_ADD[/"Tampilkan Peringatan Field/PDF Belum Sesuai"/]:::bw
    SK_P_SAVE["Simpan PDF ke /storage/surat_keluar/ & Record ke DB"]:::bw
    SK_OUT_ADD[/"Tampilkan Notifikasi Berhasil & Reload Tabel"/]:::bw

    %% Detail
    SK_IN_DET[/"Klik Tombol Detail pada Baris Surat Keluar"/]:::bw
    SK_P_DET["Query Data Rincian Surat Keluar dari Database"]:::bw
    SK_OUT_DET[/"Tampilkan Modal Detail & Pratinjau PDF"/]:::bw
    SK_D_DL{"Unduh Berkas?"}:::bw
    SK_P_DL["Alirkan Berkas Scan PDF ke Komputer Pengguna"]:::bw
    SK_OUT_DL[/"Berkas Surat Keluar Berhasil Diunduh"/]:::bw

    %% Hapus
    SK_IN_DEL[/"Klik Tombol Hapus pada Baris Surat Keluar"/]:::bw
    SK_OUT_CONF[/"Tampilkan Dialog Konfirmasi: Yakin Hapus Surat Keluar?"/]:::bw
    SK_D_DEL{"Konfirmasi Hapus?"}:::bw
    SK_P_CAN["Tutup Dialog Konfirmasi & Pertahankan Data"]:::bw
    SK_P_EXEC["Hapus File Scan PDF dari Storage & Hapus Record dari DB"]:::bw
    SK_OUT_DEL[/"Tampilkan Pesan Sukses Hapus & Reload Tabel"/]:::bw

    SK_END(["Selesai"]):::term

    SK_START --> SK_P1
    SK_P1 --> SK_OUT1
    SK_OUT1 --> SK_D_OPSI

    %% Tambah Flow
    SK_D_OPSI -- "Tambah" --> SK_IN_ADD
    SK_IN_ADD --> SK_P_VAL
    SK_P_VAL --> SK_D_VAL
    SK_D_VAL -- "Tidak" --> SK_ERR_ADD
    SK_ERR_ADD --> SK_IN_ADD
    SK_D_VAL -- "Ya" --> SK_P_SAVE
    SK_P_SAVE --> SK_OUT_ADD
    SK_OUT_ADD --> SK_END

    %% Detail Flow
    SK_D_OPSI -- "Detail" --> SK_IN_DET
    SK_IN_DET --> SK_P_DET
    SK_P_DET --> SK_OUT_DET
    SK_OUT_DET --> SK_D_DL
    SK_D_DL -- "Ya" --> SK_P_DL
    SK_P_DL --> SK_OUT_DL
    SK_OUT_DL --> SK_END
    SK_D_DL -- "Tidak" --> SK_END

    %% Hapus Flow
    SK_D_OPSI -- "Hapus" --> SK_IN_DEL
    SK_IN_DEL --> SK_OUT_CONF
    SK_OUT_CONF --> SK_D_DEL
    SK_D_DEL -- "Batal" --> SK_P_CAN
    SK_P_CAN --> SK_END
    SK_D_DEL -- "Ya, Hapus" --> SK_P_EXEC
    SK_P_EXEC --> SK_OUT_DEL
    SK_OUT_DEL --> SK_END
```

---

## 5. Flowchart Pengelolaan Data Klien Pemasyarakatan (PK)

Diagram alir untuk penambahan dan penghapusan klien bapas *(tanpa jenis bimbingan)* serta sinkronisasi metrik `Total Klien` pada Dashboard PK:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    KLN_START(["Mulai"]):::term
    KLN_P1["Akses Halaman Data Klien & Ambil Data dari DB"]:::bw
    KLN_OUT1[/"Tampilkan Tabel Klien & Tombol Aksi"/]:::bw
    KLN_D_OPSI{"Pilih Operasi?"}:::bw

    %% Tambah Klien
    KLN_IN_ADD[/"Input Data Klien (Nama, No Register, Perkara, Alamat, Telp)"/]:::bw
    KLN_P_VAL["Validasi Format Isian & Cek Keunikan No. Register di DB"]:::bw
    KLN_D_VAL{"Valid & Unik?"}:::bw
    KLN_ERR_ADD[/"Tampilkan Peringatan: No Register Sudah Terdaftar!"/]:::bw
    KLN_P_SAVE["Simpan Record Klien Baru ke DB & Hubungkan ke PK"]:::bw
    KLN_P_INC["Perbarui Metrik Dashboard PK (Total Klien + 1)"]:::bw
    KLN_OUT_ADD[/"Tampilkan Notifikasi Berhasil & Refresh Tabel Klien"/]:::bw

    %% Hapus Klien
    KLN_IN_DEL[/"Klik Tombol Hapus pada Baris Klien Terpilih"/]:::bw
    KLN_OUT_CONF[/"Tampilkan Modal: Yakin Hapus Klien & Seluruh Riwayatnya?"/]:::bw
    KLN_D_DEL{"Konfirmasi Hapus?"}:::bw
    KLN_P_CAN["Tutup Modal & Batalkan Penghapusan"]:::bw
    KLN_P_EXEC["Hapus Record Klien Beserta Seluruh Riwayat Bimbingan di DB"]:::bw
    KLN_P_DEC["Perbarui Metrik Dashboard PK (Total Klien - 1)"]:::bw
    KLN_OUT_DEL[/"Tampilkan Notifikasi Berhasil Hapus & Reload Tabel"/]:::bw

    KLN_END(["Selesai"]):::term

    KLN_START --> KLN_P1
    KLN_P1 --> KLN_OUT1
    KLN_OUT1 --> KLN_D_OPSI

    %% Tambah Klien
    KLN_D_OPSI -- "Tambah Klien" --> KLN_IN_ADD
    KLN_IN_ADD --> KLN_P_VAL
    KLN_P_VAL --> KLN_D_VAL
    KLN_D_VAL -- "Tidak" --> KLN_ERR_ADD
    KLN_ERR_ADD --> KLN_IN_ADD
    KLN_D_VAL -- "Ya" --> KLN_P_SAVE
    KLN_P_SAVE --> KLN_P_INC
    KLN_P_INC --> KLN_OUT_ADD
    KLN_OUT_ADD --> KLN_END

    %% Hapus Klien
    KLN_D_OPSI -- "Hapus Klien" --> KLN_IN_DEL
    KLN_IN_DEL --> KLN_OUT_CONF
    KLN_OUT_CONF --> KLN_D_DEL
    KLN_D_DEL -- "Batal" --> KLN_P_CAN
    KLN_P_CAN --> KLN_END
    KLN_D_DEL -- "Ya, Hapus" --> KLN_P_EXEC
    KLN_P_EXEC --> KLN_P_DEC
    KLN_P_DEC --> KLN_OUT_DEL
    KLN_OUT_DEL --> KLN_END
```

---

## 6. Flowchart Pengaturan Ketersediaan Jadwal Bimbingan (PK)

Diagram alir penentuan hari dan jam mulai bimbingan oleh PK *(tanpa kuota dan tanpa jam selesai)*:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    SCH_START(["Mulai"]):::term
    SCH_P1["Akses Menu Atur Jadwal pada Sidebar PK"]:::bw
    SCH_OUT1[/"Tampilkan Form Ketersediaan Hari & Jam Mulai"/]:::bw
    SCH_IN[/"Pilih Hari (Senin-Jumat) & Input Jam Mulai (HH:mm WITA)"/]:::bw
    SCH_P2["Validasi Format Hari & Jam Mulai"]:::bw
    SCH_D{"Format Valid?"}:::bw
    SCH_ERR[/"Tampilkan Peringatan: Format Hari/Jam Belum Sesuai!"/]:::bw
    SCH_P3["Simpan / Update Konfigurasi Jadwal PK di Database"]:::bw
    SCH_OUT_OK[/"Tampilkan Notifikasi Berhasil Disimpan & Jadwal Aktif"/]:::bw
    SCH_END(["Selesai"]):::term

    SCH_START --> SCH_P1
    SCH_P1 --> SCH_OUT1
    SCH_OUT1 --> SCH_IN
    SCH_IN --> SCH_P2
    SCH_P2 --> SCH_D
    SCH_D -- "Tidak" --> SCH_ERR
    SCH_ERR --> SCH_IN
    SCH_D -- "Ya" --> SCH_P3
    SCH_P3 --> SCH_OUT_OK
    SCH_OUT_OK --> SCH_END
```

---

## 7. Flowchart Penjadwalan Pertemuan Bimbingan Klien (PK)

Diagram alir pembuatan jadwal sesi bimbingan tatap muka dengan deteksi bentrok jadwal dan pembaruan counter metrik dashboard:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    BIMB_START(["Mulai"]):::term
    BIMB_P1["Akses Menu Jadwal Bimbingan & Query Data"]:::bw
    BIMB_IN[/"Klik Buat Jadwal -> Pilih Klien, Tanggal, & Jam Temu"/]:::bw
    BIMB_P2["Cek Ketersediaan Jam PK & Cek Jadwal Bentrok di DB"]:::bw
    BIMB_D{"Waktu Tersedia?"}:::bw
    BIMB_ERR[/"Tampilkan Peringatan: Jadwal Bentrok pada Waktu Tersebut!"/]:::bw
    BIMB_P3["Simpan Record Bimbingan ke DB (Status: Terjadwal)"]:::bw
    BIMB_P4["Perbarui Metrik Dashboard (Jadwal Hari Ini & Minggu Ini)"]:::bw
    BIMB_OUT_OK[/"Tampilkan Notifikasi Berhasil & Refresh Tabel Jadwal"/]:::bw
    BIMB_END(["Selesai"]):::term

    BIMB_START --> BIMB_P1
    BIMB_P1 --> BIMB_IN
    BIMB_IN --> BIMB_P2
    BIMB_P2 --> BIMB_D
    BIMB_D -- "Bentrok" --> BIMB_ERR
    BIMB_ERR --> BIMB_IN
    BIMB_D -- "Tersedia" --> BIMB_P3
    BIMB_P3 --> BIMB_P4
    BIMB_P4 --> BIMB_OUT_OK
    BIMB_OUT_OK --> BIMB_END
```

---

## 8. Flowchart Pencatatan Presensi & Bukti Kehadiran Klien (PK)

Diagram alir pelaksanaan wajib lapor klien, evaluasi bimbingan, upload bukti foto kehadiran fisik, dan pembaruan status menjadi "Selesai":

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef term fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;

    PRES_START(["Mulai"]):::term
    PRES_P1["Klien Hadir Wajib Lapor; PK Akses Menu Presensi"]:::bw
    PRES_OUT1[/"Tampilkan Daftar Jadwal Klien Hari Ini & Tombol Input Presensi"/]:::bw
    PRES_IN[/"Pilih Klien Hadir, Input Catatan Evaluasi, & Upload Foto Hadir"/]:::bw
    PRES_P2["Validasi Kelengkapan Catatan & Format File Foto (JPG/PNG)"]:::bw
    PRES_D{"Data & Foto Lengkap?"}:::bw
    PRES_ERR[/"Tampilkan Peringatan: Catatan & Foto Bukti Wajib Diisi!"/]:::bw
    PRES_P3["Simpan Berkas Foto Fisik ke /storage/presensi/ Server"]:::bw
    PRES_P4["Simpan Catatan & Ubah Status Bimbingan Menjadi 'Selesai' di DB"]:::bw
    PRES_OUT_OK[/"Tampilkan Notifikasi Berhasil & Perbarui Status di Tabel"/]:::bw
    PRES_END(["Selesai"]):::term

    PRES_START --> PRES_P1
    PRES_P1 --> PRES_OUT1
    PRES_OUT1 --> PRES_IN
    PRES_IN --> PRES_P2
    PRES_P2 --> PRES_D
    PRES_D -- "Tidak" --> PRES_ERR
    PRES_ERR --> PRES_IN
    PRES_D -- "Ya" --> PRES_P3
    PRES_P3 --> PRES_P4
    PRES_P4 --> PRES_OUT_OK
    PRES_OUT_OK --> PRES_END
```

---

## Ringkasan Standar Simbol Flowchart yang Digunakan

| Simbol Flowchart | Nama Simbol | Fungsi dalam Sistem SIMBA |
|---|---|---|
| Kapsul / Oval | **Terminator** | Menandai awal (*Mulai*) dan akhir (*Selesai*) dari suatu alur sistem atau prosedur. |
| Jajar Genjang (*Parallelogram*) | **Input / Output** | Masukan dari pengguna (misal: input NIP, input formulir surat/klien, unggah berkas PDF/foto) serta keluaran sistem (misal: pesan sukses, pesan kesalahan, unduhan berkas). |
| Persegi Panjang (*Rectangle*) | **Process** | Eksekusi logika internal komputer (misal: validasi field, pencocokan hash password, operasi simpan/hapus di database, pembaruan metrik dashboard). |
| Belah Ketupat (*Rhombus / Diamond*) | **Decision** | Penentu cabang keputusan berbasis kondisi boolean (misal: *Kredensial Valid?*, *Konfirmasi Hapus?*, *Jadwal Bentrok?*). |
| Garis Panah (*Directed Flowline*) | **Flowline** | Aliran urutan instruksi program yang berjalan secara orthogonal teratur dari atas ke bawah (*top-down*). |
