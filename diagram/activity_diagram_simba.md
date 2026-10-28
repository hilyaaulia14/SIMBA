# Spesifikasi & Activity Diagram Rapi (100% Hitam Putih & Tanpa Garis Bertabrakan) - Sistem Informasi SIMBA
**Balai Pemasyarakatan (Bapas) Kelas I Banjarmasin**

> [!NOTE]
> Seluruh Activity Diagram telah dirancang lengkap dan terstruktur rapi untuk seluruh proses operasional SIMBA, termasuk **operasi Hapus Surat Masuk, Surat Keluar (Tambah, Detail, Hapus), dan Hapus Data Klien**.
> - **Format Visual**: Menggunakan gaya **Hitam Putih Murni (Black & White)** sesuai kaidah akademik UML tanpa warna warni.
> - **Jalur Aliran**: Menggunakan pemisahan swimlane (*Aktor* vs *Sistem SIMBA*) dengan jalur orthogonal presisi sehingga **tidak ada garis atau panah yang bertabrakan / saling menumpuk**.
> - **Cabang Keputusan (Decision)**: Percabangan *Ya* / *Tidak* / *Batal* dialirkan melalui koridor vertikal terpisah dan jalur loopback memutar di sisi terluar kanvas.

File diagram Draw.io multi-sheet siap buka dan edit tersedia di:
- [`c:\laragon\www\SIMBA\diagram\activity_diagram_simba.drawio`](file:///c:/laragon/www/SIMBA/diagram/activity_diagram_simba.drawio)

---

## Daftar Lembar Kerja (12 Sheet Tabs pada Draw.io)

| No | Tab Name di Draw.io | Role / Aktor | Deskripsi Proses Utama |
|---|---|---|---|
| 1 | `1. Login Sistem` | Multi-Role (Admin & PK) | Autentikasi NIP & Password, validasi kegagalan, dan routing dashboard sesuai role. |
| 2 | `2. Tambah Surat Masuk` | Admin / Bagian Umum | Penginputan data nomor & asal surat, upload scan PDF, penyimpanan database & folder server. |
| 3 | `3. Detail & Unduh Surat Masuk` | Admin / Bagian Umum | Pratinjau (preview) dokumen PDF surat masuk dan streaming pengunduhan arsip. |
| 4 | `4. Hapus Surat Masuk` | Admin / Bagian Umum | Dialog konfirmasi hapus, pembersihan file fisik PDF dari storage, dan penghapusan data DB. |
| 5 | `5. Tambah Surat Keluar` | Admin / Bagian Umum | Pencatatan surat dinas keluar ke instansi tujuan, upload PDF, dan pencatatan arsip digital. |
| 6 | `6. Detail & Unduh Surat Keluar` | Admin / Bagian Umum | Membuka rincian surat keluar, review isi berkas, dan unduh salinan digital. |
| 7 | `7. Hapus Surat Keluar` | Admin / Bagian Umum | Dialog konfirmasi, penghapusan file scan surat keluar dari storage, dan pembaruan tabel. |
| 8 | `8. Pendaftaran Klien (PK)` | Pembimbing Kemasyarakatan | Registrasi klien bapas baru *(tanpa jenis bimbingan)* dan penambahan metrik Total Klien (+1). |
| 9 | `9. Hapus Data Klien (PK)` | Pembimbing Kemasyarakatan | Konfirmasi hapus klien beserta riwayatnya, penghapusan DB, dan pengurangan metrik Total Klien (-1). |
| 10 | `10. Atur Jadwal (PK)` | Pembimbing Kemasyarakatan | Pengaturan ketersediaan hari & jam mulai bimbingan *(tanpa kuota dan tanpa jam selesai)*. |
| 11 | `11. Penjadwalan Bimbingan (PK)`| Pembimbing Kemasyarakatan | Pembuatan jadwal tatap muka klien dan pembaruan metrik Jadwal Hari Ini & Minggu Ini. |
| 12 | `12. Presensi Kehadiran Klien (PK)` | Pembimbing Kemasyarakatan | Verifikasi wajib lapor fisik, upload foto presensi, evaluasi bimbingan, & auto-status "Selesai". |

---

## 1. Activity Diagram: Login Sistem (Multi-Role)
Menggambarkan alur masuk pengguna (Admin / Bagian Umum dan Pembimbing Kemasyarakatan) dengan validasi kredensial NIP dan percabangan role:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PENGGUNA ["Pengguna (Admin / PK)"]
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
        D1 -- "Tidak" --> A_ERR["Tampilkan Peringatan: NIP/Password Salah"]:::bw
        A_ERR -.-> A3
        D1 -- "Ya" --> D2{"Cek Role"}:::bw
        D2 -- "Admin" --> A_ADM["Arahkan ke Dashboard Admin"]:::bw
        D2 -- "PK" --> A_PK["Arahkan ke Dashboard PK"]:::bw
        
        A_ADM --> E1((( ))):::startEnd
        L_END1["Selesai"]
        E1 --- L_END1

        A_PK --> E2((( ))):::startEnd
        L_END2["Selesai"]
        E2 --- L_END2
    end
```

---

## 2. Activity Diagram: Tambah Surat Masuk (Admin / Bagian Umum)
Alur pengisian formulir surat masuk dan pengunggahan berkas digital (PDF) dengan validasi format:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN ["Admin / Bagian Umum"]
        S2(( )):::startEnd
        L2["Mulai"]
        S2 --- L2
        L2 --> B1["Akses Menu Surat Masuk"]:::bw
        B2["Tampilkan Tabel Surat & Tombol Tambah"]:::bw -.-> B3["Klik Tombol Tambah Surat"]:::bw
        B4["Tampilkan Modal Form Tambah Surat"]:::bw -.-> B5["Isi Data Surat & Unggah File Scan PDF"]:::bw
        B5 --> B6["Klik Tombol Simpan Surat"]:::bw
    end

    subgraph SISTEM2 ["Sistem SIMBA"]
        B1 --> B2
        B3 --> B4
        B6 --> B7["Validasi Isian Form & Berkas PDF"]:::bw
        B7 --> D3{"Valid?"}:::bw
        D3 -- "Tidak" --> B_ERR["Tampilkan Peringatan Form / File Belum Valid"]:::bw
        B_ERR -.-> B5
        D3 -- "Ya" --> B8["Simpan Data ke Database & Simpan File ke Storage"]:::bw
        B8 --> B9["Tampilkan Notifikasi Berhasil & Perbarui Tabel"]:::bw
        B9 --> E3((( ))):::startEnd
        L_END3["Selesai"]
        E3 --- L_END3
    end
```

---

## 3. Activity Diagram: Lihat Detail & Unduh Surat Masuk (Admin)
Alur pembacaan rincian arsip surat kedinasan, pratinjau dokumen PDF langsung di browser, dan pengunduhan berkas:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN2 ["Admin / Bagian Umum"]
        S3(( )):::startEnd
        L3["Mulai"]
        S3 --- L3
        L3 --> C1["Pilih Baris Surat pada Tabel Surat Masuk"]:::bw
        C1 --> C2["Klik Tombol Aksi Detail"]:::bw
        C4["Tampilkan Modal Detail & Pratinjau PDF"]:::bw -.-> C5["Membaca Rincian Informasi Surat"]:::bw
        C5 --> D_UNDUH{"Ingin Unduh Berkas?"}:::bw
        D_UNDUH -- "Ya" --> C6["Klik Tombol Unduh Berkas PDF"]:::bw
        D_UNDUH -- "Tidak" --> C_CLOSE["Klik Tombol Tutup Modal"]:::bw
    end

    subgraph SISTEM3 ["Sistem SIMBA"]
        C2 --> C3["Ambil Data Rincian Surat dari Database"]:::bw
        C3 --> C4
        C6 --> C7["Ambil File PDF dari Storage Server"]:::bw
        C7 --> C8["Stream Berkas PDF ke Komputer Admin"]:::bw
        C8 --> E4((( ))):::startEnd
        L_END4["Selesai"]
        E4 --- L_END4

        C_CLOSE --> C9["Tutup Modal Pratinjau"]:::bw
        C9 --> E4_2((( ))):::startEnd
        L_END4_2["Selesai"]
        E4_2 --- L_END4_2
    end
```

---

## 4. Activity Diagram: Hapus Surat Masuk (Admin / Bagian Umum)
Alur penghapusan arsip surat masuk dengan dialog konfirmasi keamanan serta pembersihan berkas scan fisik di server storage:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN_DEL_SM ["Admin / Bagian Umum"]
        S4_SM(( )):::startEnd
        L4_SM["Mulai"]
        S4_SM --- L4_SM
        L4_SM --> SM_D1["Akses Halaman Daftar Surat Masuk"]:::bw
        SM_D2["Tampilkan Tabel Surat Masuk & Tombol Hapus"]:::bw -.-> SM_D3["Klik Tombol Hapus pada Baris Surat"]:::bw
        SM_D4["Tampilkan Dialog Konfirmasi Hapus"]:::bw -.-> D_CONF_SM{"Konfirmasi Hapus?"}:::bw
        D_CONF_SM -- "Batal" --> SM_CAN["Klik Tombol Batal"]:::bw
        D_CONF_SM -- "Ya, Hapus" --> SM_DEL["Klik Tombol Ya, Hapus"]:::bw
    end

    subgraph SISTEM_DEL_SM ["Sistem SIMBA"]
        SM_D1 --> SM_D2
        SM_D3 --> SM_D4
        SM_CAN --> SM_C1["Tutup Modal Konfirmasi & Pertahankan Data"]:::bw
        SM_C1 --> E_CAN_SM((( ))):::startEnd
        L_CAN_SM["Selesai (Dibatalkan)"]
        E_CAN_SM --- L_CAN_SM

        SM_DEL --> SM_F1["Hapus Berkas Scan PDF dari Folder Storage Server"]:::bw
        SM_F1 --> SM_DB1["Hapus Record Surat Masuk dari Database SIMBA"]:::bw
        SM_DB1 --> SM_NOT1["Tampilkan Notifikasi Berhasil & Refresh Tabel"]:::bw
        SM_NOT1 --> E_OK_SM((( ))):::startEnd
        L_OK_SM["Selesai"]
        E_OK_SM --- L_OK_SM
    end
```

---

## 5. Activity Diagram: Tambah Surat Keluar (Admin / Bagian Umum)
Alur pencatatan surat dinas bapas yang ditujukan ke instansi luar dan penyimpanan arsip digital PDF:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN_ADD_SK ["Admin / Bagian Umum"]
        S5_SK(( )):::startEnd
        L5_SK["Mulai"]
        S5_SK --- L5_SK
        L5_SK --> SK1["Akses Menu Surat Keluar"]:::bw
        SK2["Tampilkan Tabel Surat Keluar & Tombol Tambah"]:::bw -.-> SK3["Klik Tombol Tambah Surat Keluar"]:::bw
        SK4["Tampilkan Modal Formulir Tambah Surat Keluar"]:::bw -.-> SK5["Isi Data (No Surat, Tujuan, Perihal, Tgl) & Unggah PDF"]:::bw
        SK5 --> SK6["Klik Tombol Simpan Surat Keluar"]:::bw
    end

    subgraph SISTEM_ADD_SK ["Sistem SIMBA"]
        SK1 --> SK2
        SK3 --> SK4
        SK6 --> SK7["Validasi Kelengkapan Field & Format Berkas PDF"]:::bw
        SK7 --> D_SK1{"Valid?"}:::bw
        D_SK1 -- "Tidak" --> SK_ERR["Tampilkan Peringatan Field/PDF Belum Valid"]:::bw
        SK_ERR -.-> SK5
        D_SK1 -- "Ya" --> SK8["Simpan Record ke Database & File PDF ke Storage"]:::bw
        SK8 --> SK9["Tampilkan Notifikasi Berhasil & Perbarui Tabel"]:::bw
        SK9 --> E_SK_ADD((( ))):::startEnd
        L_SK_ADD["Selesai"]
        E_SK_ADD --- L_SK_ADD
    end
```

---

## 6. Activity Diagram: Lihat Detail & Unduh Surat Keluar (Admin)
Alur verifikasi isi surat keluar, pratinjau dokumen PDF, dan pengunduhan berkas:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN_DET_SK ["Admin / Bagian Umum"]
        S6_SK(( )):::startEnd
        L6_SK["Mulai"]
        S6_SK --- L6_SK
        L6_SK --> SKD1["Pilih Baris Surat Keluar pada Tabel"]:::bw
        SKD1 --> SKD2["Klik Tombol Aksi Detail"]:::bw
        SKD4["Tampilkan Modal Detail Surat Keluar & Pratinjau PDF"]:::bw -.-> SKD5["Review Rincian Surat & Dokumen"]:::bw
        SKD5 --> D_UNDUH_SK{"Ingin Unduh Berkas?"}:::bw
        D_UNDUH_SK -- "Ya" --> SKD6["Klik Tombol Unduh Berkas PDF"]:::bw
        D_UNDUH_SK -- "Tidak" --> SKD_CLOSE["Klik Tombol Tutup Modal"]:::bw
    end

    subgraph SISTEM_DET_SK ["Sistem SIMBA"]
        SKD2 --> SKD3["Ambil Rincian Surat Keluar dari Database"]:::bw
        SKD3 --> SKD4
        SKD6 --> SKD7["Ambil Berkas PDF Surat Keluar dari Storage Server"]:::bw
        SKD7 --> SKD8["Stream Berkas PDF ke Komputer Pengguna"]:::bw
        SKD8 --> E_SK_DL((( ))):::startEnd
        L_SK_DL["Selesai (Diunduh)"]
        E_SK_DL --- L_SK_DL

        SKD_CLOSE --> SKD9["Tutup Modal Pratinjau Dokumen"]:::bw
        SKD9 --> E_SK_CLS((( ))):::startEnd
        L_SK_CLS["Selesai"]
        E_SK_CLS --- L_SK_CLS
    end
```

---

## 7. Activity Diagram: Hapus Surat Keluar (Admin / Bagian Umum)
Alur penghapusan surat keluar dan penghapusan berkas scan terkait dari folder server:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph ADMIN_DEL_SK ["Admin / Bagian Umum"]
        S7_SK(( )):::startEnd
        L7_SK["Mulai"]
        S7_SK --- L7_SK
        L7_SK --> SK_DEL1["Akses Halaman Daftar Surat Keluar"]:::bw
        SK_DEL2["Tampilkan Tabel Surat Keluar & Tombol Hapus"]:::bw -.-> SK_DEL3["Klik Tombol Hapus pada Baris Terpilih"]:::bw
        SK_DEL4["Tampilkan Modal Dialog Konfirmasi Hapus"]:::bw -.-> D_CONF_SK{"Konfirmasi Hapus?"}:::bw
        D_CONF_SK -- "Batal" --> SK_CAN["Klik Tombol Batal"]:::bw
        D_CONF_SK -- "Ya, Hapus" --> SK_OK["Klik Tombol Ya, Hapus"]:::bw
    end

    subgraph SISTEM_DEL_SK ["Sistem SIMBA"]
        SK_DEL1 --> SK_DEL2
        SK_DEL3 --> SK_DEL4
        SK_CAN --> SK_C1["Tutup Modal Konfirmasi & Pertahankan Data"]:::bw
        SK_C1 --> E_CAN_SK((( ))):::startEnd
        L_CAN_SK["Selesai (Dibatalkan)"]
        E_CAN_SK --- L_CAN_SK

        SK_OK --> SK_F1["Hapus Berkas Scan PDF Surat Keluar dari Storage"]:::bw
        SK_F1 --> SK_DB1["Hapus Record Surat Keluar dari Database SIMBA"]:::bw
        SK_DB1 --> SK_NOT1["Tampilkan Notifikasi Berhasil & Refresh Tabel"]:::bw
        SK_NOT1 --> E_OK_SK((( ))):::startEnd
        L_OK_SK["Selesai"]
        E_OK_SK --- L_OK_SK
    end
```

---

## 8. Activity Diagram: Pendaftaran Klien Pemasyarakatan Baru (PK)
Alur pendaftaran master klien bapas *(tanpa jenis bimbingan)* dan penambahan otomatis nilai metrik Total Klien pada Dashboard PK:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PK_ADD ["Pembimbing Kemasyarakatan (PK)"]
        S8_K(( )):::startEnd
        L8_K["Mulai"]
        S8_K --- L8_K
        L8_K --> K1["Akses Menu Data Klien"]:::bw
        K2["Tampilkan Tabel Master Klien & Tombol Tambah"]:::bw -.-> K3["Klik Tombol Tambah Klien"]:::bw
        K4["Tampilkan Form Klien (Tanpa Jenis Bimbingan)"]:::bw -.-> K5["Isi Data Klien (Nama, No Register, Perkara, Alamat)"]:::bw
        K5 --> K6["Klik Tombol Simpan Klien"]:::bw
    end

    subgraph SISTEM_K_ADD ["Sistem SIMBA"]
        K1 --> K2
        K3 --> K4
        K6 --> K7["Validasi Format Data & Keunikan No. Register"]:::bw
        K7 --> D_K1{"Valid?"}:::bw
        D_K1 -- "Tidak" --> K_ERR["Tampilkan Peringatan Field Belum Lengkap / Terdaftar"]:::bw
        K_ERR -.-> K5
        D_K1 -- "Ya" --> K8["Simpan Record Klien Baru ke Database"]:::bw
        K8 --> K9["Perbarui Metrik Dashboard PK (Total Klien + 1)"]:::bw
        K9 --> K10["Tampilkan Notifikasi Berhasil & Refresh Tabel Klien"]:::bw
        K10 --> E_K_ADD((( ))):::startEnd
        L_K_ADD["Selesai"]
        E_K_ADD --- L_K_ADD
    end
```

---

## 9. Activity Diagram: Hapus Data Klien Pemasyarakatan (PK)
Alur penghapusan data klien dengan proteksi modal konfirmasi, pembersihan riwayat bimbingan/presensi terkait di database, serta pembaruan otomatis penurunan metrik Total Klien pada Dashboard PK:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PK_DEL ["Pembimbing Kemasyarakatan (PK)"]
        S9_KD(( )):::startEnd
        L9_KD["Mulai"]
        S9_KD --- L9_KD
        L9_KD --> KD1["Akses Halaman Tabel Data Klien"]:::bw
        KD2["Tampilkan Tabel Data Klien & Tombol Hapus"]:::bw -.-> KD3["Klik Tombol Hapus pada Baris Klien Terpilih"]:::bw
        KD4["Tampilkan Dialog Konfirmasi Hapus Data Klien"]:::bw -.-> D_CONF_KD{"Konfirmasi Hapus?"}:::bw
        D_CONF_KD -- "Batal" --> KD_CAN["Klik Tombol Batal"]:::bw
        D_CONF_KD -- "Ya, Hapus" --> KD_OK["Klik Tombol Ya, Hapus Klien"]:::bw
    end

    subgraph SISTEM_K_DEL ["Sistem SIMBA"]
        KD1 --> KD2
        KD3 --> KD4
        KD_CAN --> KD_C1["Tutup Modal Konfirmasi & Pertahankan Data Klien"]:::bw
        KD_C1 --> E_CAN_KD((( ))):::startEnd
        L_CAN_KD["Selesai (Dibatalkan)"]
        E_CAN_KD --- L_CAN_KD

        KD_OK --> KD_DB1["Hapus Record Klien & Seluruh Relasi Riwayat Bimbingan di DB"]:::bw
        KD_DB1 --> KD_DASH["Perbarui Metrik Dashboard PK (Total Klien - 1)"]:::bw
        KD_DASH --> KD_NOT1["Tampilkan Notifikasi Berhasil & Refresh Tabel Klien"]:::bw
        KD_NOT1 --> E_OK_KD((( ))):::startEnd
        L_OK_KD["Selesai"]
        E_OK_KD --- L_OK_KD
    end
```

---

## 10. Activity Diagram: Pengaturan Jadwal Ketersediaan Bimbingan (PK)
Alur penentuan hari dan jam mulai bimbingan oleh PK *(tanpa kuota dan tanpa jam selesai)*:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PK_SCH ["Pembimbing Kemasyarakatan (PK)"]
        S10_J(( )):::startEnd
        L10_J["Mulai"]
        S10_J --- L10_J
        L10_J --> J1["Akses Menu Atur Jadwal"]:::bw
        J2["Tampilkan Form Jadwal (Hari & Jam Mulai Saja)"]:::bw -.-> J3["Pilih Hari & Masukkan Jam Mulai Bimbingan"]:::bw
        J3 --> J4["Klik Tombol Simpan Jadwal PK"]:::bw
    end

    subgraph SISTEM_SCH ["Sistem SIMBA"]
        J1 --> J2
        J4 --> J5["Validasi Format Hari & Jam Mulai"]:::bw
        J5 --> D_J1{"Valid?"}:::bw
        D_J1 -- "Tidak" --> J_ERR["Tampilkan Peringatan Input Jadwal Tidak Valid"]:::bw
        J_ERR -.-> J3
        D_J1 -- "Ya" --> J6["Simpan / Perbarui Konfigurasi Jadwal PK ke Database"]:::bw
        J6 --> J7["Tampilkan Notifikasi Berhasil Disimpan"]:::bw
        J7 --> E_SCH((( ))):::startEnd
        L_SCH["Selesai"]
        E_SCH --- L_SCH
    end
```

---

## 11. Activity Diagram: Penjadwalan Pertemuan Bimbingan Klien (PK)
Alur pembuatan jadwal bimbingan tatap muka klien dan pembaruan otomatis metrik Jadwal Hari Ini & Jadwal Minggu Ini:

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PK_MEET ["Pembimbing Kemasyarakatan (PK)"]
        S11_M(( )):::startEnd
        L11_M["Mulai"]
        S11_M --- L11_M
        L11_M --> M1["Akses Menu Jadwal Bimbingan"]:::bw
        M2["Tampilkan Tabel Jadwal & Tombol Buat Jadwal"]:::bw -.-> M3["Klik Tombol Buat Jadwal Bimbingan"]:::bw
        M4["Tampilkan Modal (Dropdown Klien & Picker Tanggal)"]:::bw -.-> M5["Pilih Klien Terdaftar, Tanggal, & Jam Pertemuan"]:::bw
        M5 --> M6["Klik Tombol Simpan Jadwal Pertemuan"]:::bw
    end

    subgraph SISTEM_MEET ["Sistem SIMBA"]
        M1 --> M2
        M3 --> M4
        M6 --> M7["Validasi Tanggal & Ketersediaan Jadwal PK"]:::bw
        M7 --> D_M1{"Tersedia?"}:::bw
        D_M1 -- "Bentrok" --> M_ERR["Tampilkan Peringatan: Jadwal Bentrok!"]:::bw
        M_ERR -.-> M5
        D_M1 -- "Ya" --> M8["Simpan Jadwal ke Database (Status: Terjadwal)"]:::bw
        M8 --> M9["Perbarui Metrik Dashboard (Jadwal Hari Ini & Minggu Ini)"]:::bw
        M9 --> M10["Tampilkan Notifikasi Berhasil & Refresh Tabel Jadwal"]:::bw
        M10 --> E_MEET((( ))):::startEnd
        L_MEET["Selesai"]
        E_MEET --- L_MEET
    end
```

---

## 12. Activity Diagram: Pencatatan Bukti Kehadiran Klien / Wajib Lapor (PK)
Alur verifikasi wajib lapor fisik klien, unggah berkas foto fisik kehadiran, catatan evaluasi, hingga otomasi status pertemuan menjadi "Selesai":

```mermaid
flowchart TD
    classDef bw fill:#ffffff,stroke:#000000,stroke-width:1.5px,color:#000000;
    classDef startEnd fill:#000000,stroke:#000000,stroke-width:2px,color:#ffffff;

    subgraph PK_ATT ["Pembimbing Kemasyarakatan (PK)"]
        S12_P(( )):::startEnd
        L12_P["Mulai"]
        S12_P --- L12_P
        L12_P --> P1["Klien Datang Wajib Lapor; PK Akses Menu Presensi"]:::bw
        P2["Tampilkan Daftar Jadwal Klien Hari Ini"]:::bw -.-> P3["Pilih Klien Hadir & Klik Tombol Input Presensi"]:::bw
        P4["Tampilkan Modal Presensi & Upload Foto"]:::bw -.-> P5["Isi Catatan Bimbingan & Unggah Foto Fisik Kehadiran"]:::bw
        P5 --> P6["Klik Tombol Simpan Presensi"]:::bw
    end

    subgraph SISTEM_ATT ["Sistem SIMBA"]
        P1 --> P2
        P3 --> P4
        P6 --> P7["Validasi Kelengkapan Catatan & File Foto Presensi"]:::bw
        P7 --> D_P1{"Lengkap?"}:::bw
        D_P1 -- "Tidak" --> P_ERR["Tampilkan Peringatan Foto/Catatan Belum Lengkap"]:::bw
        P_ERR -.-> P5
        D_P1 -- "Ya" --> P8["Simpan Berkas Foto Fisik ke Direktori Storage Server"]:::bw
        P8 --> P9["Simpan Presensi & Ubah Status Jadwal Menjadi Selesai di DB"]:::bw
        P9 --> P10["Tampilkan Notifikasi Berhasil & Refresh Tabel Presensi"]:::bw
        P10 --> E_ATT((( ))):::startEnd
        L_ATT["Selesai"]
        E_ATT --- L_ATT
    end
```

---

## Ringkasan Ketentuan Desain Visual & UML

1. **Hitam Putih Murni (*Black & White*)**:
   - Initial Node (Start State): Lingkaran solid hitam (`fillColor=#000000`).
   - Final Node (End State): Lingkaran dengan lingkaran ganda / bullseye (`shape=endState;fillColor=#000000;strokeColor=#000000`).
   - Action / Activity: Persegi panjang bersudut melengkung (*rounded rectangle*) dengan latar putih (`fillColor=#ffffff`) dan garis tepi hitam (`strokeColor=#000000;strokeWidth=1.5`).
   - Decision Node: Belah ketupat (*rhombus*) dengan garis tepi hitam tegas.
   - Swimlane: Pembatas vertikal kolom (*lane*) jelas antara peran pengguna (*Pengguna / Admin / PK*) dan *Sistem SIMBA*.

2. **Bebas Tabrakan Garis (*Zero Line Collision*)**:
   - Seluruh konektor panah dialirkan menggunakan *orthogonal routing* dengan titik belok (*waypoints*) yang terdefinisi pada ruang bebas di tepi luar kanvas (*margin lane*).
   - Jalur alternatif pembatalan (*Batal*) dan kegagalan validasi diposisikan simetris tanpa memotong elemen aktif lainnya.
