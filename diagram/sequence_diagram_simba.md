# Spesifikasi & Sequence Diagram Lengkap - Sistem Informasi SIMBA
**Balai Pemasyarakatan (Bapas) Kelas I Banjarmasin**

> [!TIP]
> File diagram siap buka & edit untuk **Draw.io / diagrams.net** tersedia di folder `diagram/`:
> - [`c:\laragon\www\SIMBA\diagram\sequence_diagram_simba.drawio`](file:///c:/laragon/www/SIMBA/diagram/sequence_diagram_simba.drawio)
> 
> *Struktur 6 Tab Sheet Lengkap di Draw.io:*
> 1. `1. Sequence Login Sistem`: Alur autentikasi login pengguna (NIP & Password), pengecekan hash password, penanganan salah, hingga pengalihan ke dashboard sesuai role.
> 2. `2. Sequence Tambah Surat Masuk`: Alur input data surat masuk, validasi file scan PDF, penyimpanan ke *storage*, dan perekaman ke database MySQL.
> 3. `3. Sequence Detail & Unduh Surat`: Alur pengambilan data spesifik surat dari database, pratinjau PDF di browser, dan pengunduhan berkas PDF.
> 4. `4. Sequence Tambah Data Klien`: Alur registrasi klien bapas baru, validasi keunikan nomor register, relasi ke PK pendamping, dan pembaruan tabel.
> 5. `5. Sequence Penjadwalan Bimbingan`: Alur pembuatan jadwal pertemuan tatap muka, validasi waktu operasional, dan pembaruan otomatis kartu metrik dashboard PK.
> 6. `6. Sequence Bukti Kehadiran Klien`: Alur pengunggahan foto bukti kehadiran fisik, paraf verifikasi, catatan evaluasi, dan otomasi update status jadwal bimbingan menjadi "Selesai".

---

## 1. Sequence Diagram: Autentikasi / Login Sistem (Multi-Role)
Menggambarkan interaksi antara **Pengguna (Admin/PK)**, **Halaman Login (View)**, **AuthController (Controller)**, **Database MySQL (Model User)**, dan **Dashboard View**.

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna (Admin / PK)
    participant View as Halaman Login
    participant Ctrl as AuthController
    participant DB as Database (User)
    participant Dash as Dashboard View

    User->>View: Masukkan NIP & Password, klik "Masuk"
    activate View
    View->>Ctrl: postLogin(nip, password)
    activate Ctrl
    Ctrl->>DB: findUserByNIP(nip)
    activate DB
    DB-->>Ctrl: Return data user & hash password
    deactivate DB
    
    Ctrl->>Ctrl: Validasi Kredensial & Hash Password

    alt Kredensial Tidak Valid / Salah
        Ctrl-->>View: Return Response Error: "NIP atau Kata Sandi Salah!"
        View-->>User: Tampilkan Notifikasi Error di Layar Login
    else Kredensial Valid & Berhasil
        Ctrl->>Ctrl: Set Auth Session (User ID & Role)
        Ctrl->>Dash: Redirect ke Dashboard sesuai Role
        activate Dash
        Dash-->>Ctrl: Render Data Metrik & Ringkasan
        deactivate Dash
        Ctrl-->>View: Response: Redirect Success
        View-->>User: Tampilkan Dashboard Admin / PK
    end
    deactivate Ctrl
    deactivate View
```

---

## 2. Sequence Diagram: Pengelolaan Surat Masuk - Tambah & Upload PDF (Admin)
Menggambarkan proses pencatatan surat masuk kedinasan, penanganan upload file PDF ke *storage*, dan perekaman metadata ke basis data.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant Modal as Modal Form Surat Masuk
    participant Ctrl as SuratMasukController
    participant Store as Storage Disk (/storage/surat)
    participant DB as Database (surat_masuk)

    Admin->>Modal: Isi Form (No, Pengirim, Tgl, Perihal) & Upload PDF
    activate Modal
    Modal->>Ctrl: store(Request $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Input & Ekstensi PDF (Max 2MB)
    
    Ctrl->>Store: storeFile(file_pdf, "storage/surat")
    activate Store
    Store-->>Ctrl: Return $filePath (storage/surat/scan_01.pdf)
    deactivate Store

    Ctrl->>DB: INSERT INTO surat_masuk (no_surat, asal, tgl, perihal, file, id_user)
    activate DB
    DB-->>Ctrl: Return Status: Success
    deactivate DB

    Ctrl-->>Modal: Response: Success (JSON / Redirect with Flash Message)
    deactivate Ctrl
    Modal-->>Admin: Tutup Modal, Perbarui Tabel Surat, & Tampilkan Toast Sukses
    deactivate Modal
```

---

## 3. Sequence Diagram: Lihat Detail & Unduh Berkas Surat (Admin)
Menggambarkan proses melihat rincian surat, pratinjau berkas digital secara *inline*, serta proses pengunduhan (*download*) dokumen fisik dari penyimpanan server.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant View as Tabel Surat Masuk
    participant Ctrl as SuratMasukController
    participant DB as Database (surat_masuk)
    participant Store as Storage Disk (File PDF)

    Admin->>View: Klik Tombol "Lihat Detail" pada baris data
    activate View
    View->>Ctrl: show($id)
    activate Ctrl
    Ctrl->>DB: SELECT * FROM surat_masuk WHERE id = $id
    activate DB
    DB-->>Ctrl: Return Data Rincian Surat
    deactivate DB
    Ctrl-->>View: Render Modal Detail & Pratinjau PDF di Layar
    View-->>Admin: Tampilkan Modal Detail & Preview Dokumen

    Admin->>View: Klik Tombol "Unduh Berkas PDF"
    View->>Ctrl: download($id)
    Ctrl->>Store: Storage::download($filePath)
    activate Store
    Store-->>Ctrl: Stream File Binary PDF
    deactivate Store
    Ctrl-->>View: Header Content-Disposition: attachment
    deactivate Ctrl
    View-->>Admin: Berkas PDF Berhasil Terunduh ke Komputer
    deactivate View
```

---

## 4. Sequence Diagram: Pendaftaran Master Klien Pemasyarakatan (PK)
Menggambarkan alur pendaftaran klien pemasyarakatan baru di Bapas, validasi keunikan nomor registrasi perkara, dan relasi langsung ke akun PK pembimbing terkait.

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant Modal as Modal Form Klien
    participant Ctrl as KlienController
    participant DB as Database (klien)

    PK->>Modal: Isi Form (No Register, Nama, Tindak Pidana, Masa Bimbingan)
    activate Modal
    Modal->>Ctrl: store(KlienRequest $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Keunikan No. Register & Kelengkapan Identitas

    Ctrl->>DB: SELECT id FROM klien WHERE no_register = ?
    activate DB
    DB-->>Ctrl: Return: Unique / Not Found
    deactivate DB

    Ctrl->>DB: INSERT INTO klien (no_register, nama, tindak_pidana, masa_mulai, masa_selesai, id_user)
    activate DB
    DB-->>Ctrl: Return $klien (ID Baru Klien)
    deactivate DB

    Ctrl-->>Modal: Response: Redirect with Success Message
    deactivate Ctrl
    Modal-->>PK: Tutup Modal, Perbarui Tabel Klien, & Tampilkan Toast Berhasil
    deactivate Modal
```

---

## 5. Sequence Diagram: Penjadwalan Bimbingan Klien (PK)
Menggambarkan alur penetapan jadwal pertemuan bimbingan tatap muka antara PK dan klien bapas, penentuan materi bimbingan, serta pembaruan otomatis pada metrik *Dashboard PK* (Jadwal Hari Ini & Jadwal Minggu Ini).

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant Modal as Modal Jadwal Bimbingan
    participant Ctrl as JadwalController
    participant DB as Database (jadwal_bimbingan)

    PK->>Modal: Pilih Klien, Tanggal Bimbingan, Jam Mulai, & Materi
    activate Modal
    Modal->>Ctrl: storeJadwal(Request $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Tanggal & Jam Operasional Ketersediaan PK

    Ctrl->>DB: INSERT INTO jadwal_bimbingan (id_klien, id_user, tgl, jam, status="Dijadwalkan")
    activate DB
    DB-->>Ctrl: Return Status: Saved
    deactivate DB

    Ctrl->>DB: Hitung Ulang Metrik (Jadwal Hari Ini & Minggu Ini)
    activate DB
    DB-->>Ctrl: Return Data Metrik Terbaru
    deactivate DB

    Ctrl-->>Modal: Response: Update View & Refresh Table
    deactivate Ctrl
    Modal-->>PK: Tampilkan Jadwal Baru di Kalender/Tabel & Update Metrik Dashboard
    deactivate Modal
```

---

## 6. Sequence Diagram: Pencatatan Bukti Kehadiran Klien / Wajib Lapor (PK)
Menggambarkan proses presensi tatap muka wajib lapor klien di Bapas: pengunggahan foto fisik kehadiran, perekaman paraf verifikasi, pencatatan evaluasi perkembangan kepribadian/kemandirian, hingga otomatisasi pengubahan status jadwal menjadi **"Selesai"**.

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant Modal as Modal Presensi Kehadiran
    participant Ctrl as KehadiranController
    participant Store as Storage Disk (/storage/presensi)
    participant DB as Database (kehadiran & jadwal)

    PK->>Modal: Upload Foto Kehadiran, Konfirmasi Waktu, Paraf, & Catatan Evaluasi
    activate Modal
    Modal->>Ctrl: storeKehadiran(Request $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Format Foto (JPG/PNG) & Kelengkapan Evaluasi

    Ctrl->>Store: storeFoto(foto_bukti, "storage/presensi")
    activate Store
    Store-->>Ctrl: Return $pathFoto
    deactivate Store

    Ctrl->>DB: INSERT INTO kehadiran_klien (id_jadwal, foto, tgl, paraf, evaluasi)
    activate DB
    DB-->>Ctrl: Return ID Presensi
    
    Ctrl->>DB: UPDATE jadwal_bimbingan SET status = "Selesai" WHERE id = $id_jadwal
    DB-->>Ctrl: Return Status: Success
    deactivate DB

    Ctrl-->>Modal: Response: Tutup Modal & Refresh Status Pertemuan
    deactivate Ctrl
    Modal-->>PK: Status Jadwal Berubah "Selesai" & Riwayat Kehadiran Klien Diperbarui
    deactivate Modal
```
