# Spesifikasi & Sequence Diagram Lengkap - Sistem Informasi SIMBA
**Balai Pemasyarakatan (Bapas) Kelas I Banjarmasin**

> [!TIP]
> File diagram siap buka & edit untuk **Draw.io / diagrams.net** tersedia di folder `diagram/`:
> - [`c:\laragon\www\SIMBA\diagram\sequence_diagram_simba.drawio`](file:///c:/laragon/www/SIMBA/diagram/sequence_diagram_simba.drawio)
> 
> *Struktur 12 Tab Sheet Lengkap di Draw.io:*
> 1. `1. Sequence Login Sistem`: Alur autentikasi login pengguna (NIP & Password), pengecekan hash password, penanganan salah, hingga pengalihan ke dashboard sesuai role.
> 2. `2. Sequence Tambah Surat Masuk`: Alur input data surat masuk, validasi file scan PDF, penyimpanan ke *storage*, dan perekaman ke database MySQL.
> 3. `3. Sequence Detail & Unduh Surat`: Alur pengambilan data spesifik surat dari database, pratinjau PDF di browser, dan pengunduhan berkas PDF.
> 4. `4. Sequence Tambah Data Klien`: Alur registrasi klien bapas baru, validasi keunikan nomor register, relasi ke PK pendamping, dan pembaruan tabel.
> 5. `5. Sequence Penjadwalan Bimbingan`: Alur pembuatan jadwal pertemuan tatap muka, validasi waktu operasional, dan pembaruan otomatis kartu metrik dashboard PK.
> 6. `6. Sequence Bukti Kehadiran Klien`: Alur pengunggahan foto bukti kehadiran fisik, paraf verifikasi, catatan evaluasi, dan otomasi update status jadwal bimbingan menjadi "Selesai".
> 7. `7. Sequence Tambah Surat Keluar (Create)`: Alur pencatatan surat dinas keluar baru, validasi berkas PDF, upload ke server storage `/storage/surat_keluar/`, dan simpan ke database.
> 8. `8. Sequence Detail & Unduh Surat Keluar (Read)`: Alur pengambilan detail surat keluar, penayangan pratinjau PDF di modal, serta streaming unduh file PDF.
> 9. `9. Sequence Edit Surat Keluar (Update)`: Alur pengubahan rincian surat keluar, penggantian berkas PDF baru (opsional dengan auto-delete berkas lama di server), dan pembaruan record di DB.
> 10. `10. Sequence Hapus Surat Keluar (Delete)`: Alur konfirmasi penghapusan surat keluar, pembersihan berkas fisik PDF dari storage disk, serta penghapusan record di DB.
> 11. `11. Sequence Hapus Surat Masuk (Delete)`: Alur dialog konfirmasi, penghapusan file scan PDF dari storage `/storage/surat/`, dan penghapusan data surat masuk di DB.
> 12. `12. Sequence Hapus Data Klien (Delete)`: Alur penghapusan data klien beserta riwayat relasi bimbingan di DB, serta pengurangan otomatis kartu metrik Total Klien pada Dashboard PK.

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

## 2. Sequence Diagram: Tambah Surat Masuk (Admin / Bagian Umum)
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

## 3. Sequence Diagram: Lihat Detail & Unduh Berkas Surat Masuk (Admin)
Menggambarkan proses melihat rincian surat masuk, pratinjau berkas digital secara *inline*, serta proses pengunduhan (*download*) dokumen fisik dari penyimpanan server.

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

## 4. Sequence Diagram: Tambah Surat Keluar & Upload PDF / Create (Admin)
Menggambarkan proses pencatatan naskah dinas keluar Bapas ke instansi luar, validasi berkas scan digital PDF, penyimpanan berkas ke storage `/storage/surat_keluar/`, dan perekaman record ke database.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant Modal as Modal Form Surat Keluar
    participant Ctrl as SuratKeluarController
    participant Store as Storage Disk (/storage/surat_keluar)
    participant DB as Database (surat_keluar)

    Admin->>Modal: Isi Form (No Surat, Tujuan, Tgl, Perihal) & Upload PDF
    activate Modal
    Modal->>Ctrl: store(Request $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Kelengkapan Field & Ekstensi PDF (Max 2MB)
    
    Ctrl->>Store: storeFile(file_pdf, "storage/surat_keluar")
    activate Store
    Store-->>Ctrl: Return $filePath (storage/surat_keluar/sk_01.pdf)
    deactivate Store

    Ctrl->>DB: INSERT INTO surat_keluar (no_surat, tujuan_surat, tgl_keluar, perihal, upload_dokumen, id_pegawai)
    activate DB
    DB-->>Ctrl: Return Status: Success (ID Surat Keluar Baru)
    deactivate DB

    Ctrl-->>Modal: Response: JSON Success / Redirect with Flash Message
    deactivate Ctrl
    Modal-->>Admin: Tutup Modal, Perbarui Tabel Surat Keluar, & Tampilkan Toast Sukses
    deactivate Modal
```

---

## 5. Sequence Diagram: Lihat Detail & Unduh Surat Keluar / Read (Admin)
Menggambarkan proses peninjauan arsip surat keluar Bapas, pratinjau dokumen PDF langsung di browser, serta proses pengunduhan (*download*) salinan berkas fisik.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant View as Tabel Surat Keluar
    participant Ctrl as SuratKeluarController
    participant DB as Database (surat_keluar)
    participant Store as Storage Disk (File PDF)

    Admin->>View: Klik Tombol "Lihat Detail" pada baris surat keluar
    activate View
    View->>Ctrl: show($id)
    activate Ctrl
    Ctrl->>DB: SELECT * FROM surat_keluar WHERE id = $id
    activate DB
    DB-->>Ctrl: Return Data Rincian Surat Keluar & Path Dokumen
    deactivate DB
    Ctrl-->>View: Render Modal Detail & Pratinjau (Preview) PDF di Layar
    View-->>Admin: Tampilkan Modal Rincian & Preview Dokumen PDF

    Admin->>View: Klik Tombol "Unduh Berkas PDF"
    View->>Ctrl: download($id)
    Ctrl->>Store: Storage::download($filePath)
    activate Store
    Store-->>Ctrl: Stream File Binary PDF Surat Keluar
    deactivate Store
    Ctrl-->>View: Header Content-Disposition: attachment
    deactivate Ctrl
    View-->>Admin: Berkas PDF Surat Keluar Berhasil Terunduh ke Komputer
    deactivate View
```

---

## 6. Sequence Diagram: Edit / Update Surat Keluar (Admin)
Menggambarkan proses pembaruan data naskah dinas keluar, penanganan opsional penggantian file PDF baru (menghapus file lama di storage dan menyimpan file baru), serta pembaruan record di basis data.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant Modal as Modal Edit Surat Keluar
    participant Ctrl as SuratKeluarController
    participant Store as Storage Disk (/storage/surat_keluar)
    participant DB as Database (surat_keluar)

    Admin->>Modal: Klik Tombol "Edit" pada baris data
    activate Modal
    Modal->>Ctrl: edit($id)
    activate Ctrl
    Ctrl->>DB: SELECT * FROM surat_keluar WHERE id = $id
    activate DB
    DB-->>Ctrl: Return Data Surat Keluar Eksisting
    deactivate DB
    Ctrl-->>Modal: Tampilkan Modal Edit dengan Data Terisi
    deactivate Ctrl

    Admin->>Modal: Ubah Field & Unggah Berkas PDF Pengganti (Opsional), klik "Simpan"
    Modal->>Ctrl: update(Request $request, $id)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Input Form & Berkas PDF Baru

    alt Ada File PDF Baru yang Diunggah
        Ctrl->>Store: delete(old_file_path) & storeFile(new_pdf, "storage/surat_keluar")
        activate Store
        Store-->>Ctrl: Return $newFilePath
        deactivate Store
    else Tidak Ada Perubahan File PDF
        Ctrl->>Ctrl: Pertahankan Path File PDF Lama
    end

    Ctrl->>DB: UPDATE surat_keluar SET no_surat=?, tujuan=?, tgl=?, perihal=?, upload_dokumen=? WHERE id=?
    activate DB
    DB-->>Ctrl: Return Status: Update Success
    deactivate DB

    Ctrl-->>Modal: Response: Success (JSON / Flash Message)
    deactivate Ctrl
    Modal-->>Admin: Tutup Modal, Perbarui Baris di Tabel, & Tampilkan Toast Sukses
    deactivate Modal
```

---

## 7. Sequence Diagram: Hapus Surat Keluar / Delete (Admin)
Menggambarkan alur penghapusan arsip surat dinas keluar: dialog konfirmasi pengamanan data, pembersihan berkas scan digital PDF dari server storage, dan penghapusan record dari database.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant View as Tabel & Modal Konfirmasi
    participant Ctrl as SuratKeluarController
    participant DB as Database (surat_keluar)
    participant Store as Storage Disk (/storage/surat_keluar)

    Admin->>View: Klik Tombol "Hapus" pada baris surat keluar
    activate View
    View-->>Admin: Tampilkan Modal Dialog Konfirmasi: "Yakin Hapus Surat Keluar Ini?"
    Admin->>View: Klik Tombol "Ya, Hapus"
    View->>Ctrl: destroy($id)
    activate Ctrl

    Ctrl->>DB: SELECT upload_dokumen FROM surat_keluar WHERE id = $id
    activate DB
    DB-->>Ctrl: Return $filePath berkas scan PDF
    deactivate DB

    Ctrl->>Store: Storage::delete($filePath) - Hapus Berkas Fisik PDF dari Server
    activate Store
    Store-->>Ctrl: Return Status: File Deleted Successfully
    deactivate Store

    Ctrl->>DB: DELETE FROM surat_keluar WHERE id = $id
    activate DB
    DB-->>Ctrl: Return Status: Record Deleted
    deactivate DB

    Ctrl-->>View: Response: Success (JSON / Redirect Flash Message)
    deactivate Ctrl
    View-->>Admin: Hapus Baris dari Tabel & Tampilkan Toast "Surat Keluar Berhasil Dihapus"
    deactivate View
```

---

## 8. Sequence Diagram: Hapus Surat Masuk / Delete (Admin)
Menggambarkan alur penghapusan surat masuk kedinasan, penghapusan file scan PDF dari folder `/storage/surat/`, dan penghapusan data di basis data MySQL.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Bagian Umum
    participant View as Tabel & Modal Konfirmasi
    participant Ctrl as SuratMasukController
    participant DB as Database (surat_masuk)
    participant Store as Storage Disk (/storage/surat)

    Admin->>View: Klik Tombol "Hapus" pada baris surat masuk
    activate View
    View-->>Admin: Tampilkan Modal Dialog Konfirmasi: "Yakin Hapus Surat Masuk Ini?"
    Admin->>View: Klik Tombol "Ya, Hapus"
    View->>Ctrl: destroy($id)
    activate Ctrl

    Ctrl->>DB: SELECT upload_dokumen FROM surat_masuk WHERE id = $id
    activate DB
    DB-->>Ctrl: Return $filePath berkas scan PDF
    deactivate DB

    Ctrl->>Store: Storage::delete($filePath) - Hapus Berkas Fisik PDF dari Server
    activate Store
    Store-->>Ctrl: Return Status: File Deleted Successfully
    deactivate Store

    Ctrl->>DB: DELETE FROM surat_masuk WHERE id = $id
    activate DB
    DB-->>Ctrl: Return Status: Record Deleted
    deactivate DB

    Ctrl-->>View: Response: Success (JSON / Redirect Flash Message)
    deactivate Ctrl
    View-->>Admin: Hapus Baris dari Tabel & Tampilkan Toast "Surat Masuk Berhasil Dihapus"
    deactivate View
```

---

## 9. Sequence Diagram: Pendaftaran Master Klien Pemasyarakatan (PK)
Menggambarkan alur pendaftaran klien pemasyarakatan baru di Bapas, validasi keunikan nomor registrasi perkara, relasi langsung ke akun PK pembimbing, dan pembaruan kartu metrik *Total Klien* (+1).

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant Modal as Modal Form Klien
    participant Ctrl as KlienController
    participant DB as Database (klien)
    participant Dash as Dashboard PK (Metrik)

    PK->>Modal: Isi Form (No Register, Nama, Tindak Pidana, Tgl Lahir, Jenis Kelamin)
    activate Modal
    Modal->>Ctrl: store(KlienRequest $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Keunikan No. Register & Kelengkapan Identitas

    Ctrl->>DB: SELECT id FROM klien WHERE no_register = ?
    activate DB
    DB-->>Ctrl: Return: Unique / Not Found
    deactivate DB

    Ctrl->>DB: INSERT INTO klien (no_register, nama_klien, tindak_pidana, jenis_kelamin, tgl_lahir, id_pegawai)
    activate DB
    DB-->>Ctrl: Return $klien (ID Baru Klien)
    deactivate DB

    Ctrl->>Dash: incrementTotalKlien() - Tambah Metrik Total Klien (+1)
    activate Dash
    Dash-->>Ctrl: Return Metrik Terkini
    deactivate Dash

    Ctrl-->>Modal: Response: Redirect with Success Message
    deactivate Ctrl
    Modal-->>PK: Tutup Modal, Perbarui Tabel Klien, & Tampilkan Toast Berhasil
    deactivate Modal
```

---

## 10. Sequence Diagram: Hapus Data Klien Pemasyarakatan / Delete (PK)
Menggambarkan alur penghapusan master klien pemasyarakatan: dialog proteksi konfirmasi, pembersihan riwayat relasi bimbingan dan presensi di database, serta pembaruan otomatis pengurangan nilai metrik *Total Klien* (-1) pada Dashboard PK.

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant View as Tabel & Modal Konfirmasi
    participant Ctrl as KlienController
    participant DB as Database (klien & riwayat)
    participant Dash as Dashboard PK (Card Total Klien)

    PK->>View: Klik Tombol "Hapus" pada baris klien terpilih
    activate View
    View-->>PK: Tampilkan Modal: "Yakin Hapus Klien Beserta Seluruh Riwayat Bimbingannya?"
    PK->>View: Klik Tombol "Ya, Hapus Klien"
    View->>Ctrl: destroy($id)
    activate Ctrl

    Ctrl->>DB: DELETE FROM jadwal_bimbingan & kehadiran_klien WHERE id_klien = $id
    activate DB
    DB-->>Ctrl: Return Status: Riwayat Relasi Berhasil Dihapus
    deactivate DB

    Ctrl->>DB: DELETE FROM klien WHERE id = $id
    activate DB
    DB-->>Ctrl: Return Status: Record Klien Deleted
    deactivate DB

    Ctrl->>Dash: decrementTotalKlien() - Kurangi Hitungan Kartu Metrik Dashboard (-1)
    activate Dash
    Dash-->>Ctrl: Return Metrik Terbaru (Total Klien Updated)
    deactivate Dash

    Ctrl-->>View: Response: Success (JSON / Redirect with Flash Message)
    deactivate Ctrl
    View-->>PK: Hapus Baris dari Tabel & Tampilkan Notifikasi Sukses "Klien Berhasil Dihapus"
    deactivate View
```

---

## 11. Sequence Diagram: Penjadwalan Bimbingan Klien (PK)
Menggambarkan alur penetapan jadwal pertemuan bimbingan tatap muka antara PK dan klien bapas, penentuan materi bimbingan, pengecekan bentrok jadwal, serta pembaruan otomatis pada metrik *Dashboard PK* (Jadwal Hari Ini & Jadwal Minggu Ini).

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant Modal as Modal Jadwal Bimbingan
    participant Ctrl as JadwalController
    participant DB as Database (jadwal_bimbingan)

    PK->>Modal: Pilih Klien, Tanggal Bimbingan, Jam Mulai, & Keterangan Materi
    activate Modal
    Modal->>Ctrl: storeJadwal(Request $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Tanggal & Cek Bentrok Waktu Jadwal PK

    Ctrl->>DB: INSERT INTO jadwal_bimbingan (id_klien, id_pegawai, tgl_bimbingan, jam_mulai, keterangan, id_status_bimbingan=1)
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

## 12. Sequence Diagram: Pencatatan Bukti Kehadiran Klien / Wajib Lapor (PK)
Menggambarkan proses presensi tatap muka wajib lapor klien di Bapas: pengunggahan foto fisik kehadiran, pencatatan evaluasi pembimbingan, penyimpanan foto ke `/storage/presensi/`, hingga otomatisasi pengubahan status jadwal menjadi **"Selesai"**.

```mermaid
sequenceDiagram
    autonumber
    actor PK as Pembimbing Kemasyarakatan
    participant Modal as Modal Presensi Kehadiran
    participant Ctrl as KehadiranController
    participant Store as Storage Disk (/storage/presensi)
    participant DB as Database (kehadiran & jadwal)

    PK->>Modal: Upload Foto Kehadiran, Konfirmasi Jam Hadir, & Catatan Evaluasi
    activate Modal
    Modal->>Ctrl: storeKehadiran(Request $request)
    activate Ctrl
    Ctrl->>Ctrl: Validasi Format Foto (JPG/PNG) & Kelengkapan Evaluasi

    Ctrl->>Store: storeFoto(foto_kehadiran, "storage/presensi")
    activate Store
    Store-->>Ctrl: Return $pathFoto
    deactivate Store

    Ctrl->>DB: INSERT INTO kehadiran_klien (id_jadwal_bimbingan, id_klien, foto_kehadiran, tgl_hadir, jam_hadir, catatan)
    activate DB
    DB-->>Ctrl: Return ID Presensi
    
    Ctrl->>DB: UPDATE jadwal_bimbingan SET id_status_bimbingan = 2 WHERE id_jadwal_bimbingan = $id_jadwal
    DB-->>Ctrl: Return Status: Success (Status Selesai)
    deactivate DB

    Ctrl-->>Modal: Response: Tutup Modal & Refresh Status Pertemuan
    deactivate Ctrl
    Modal-->>PK: Status Jadwal Berubah "Selesai" & Riwayat Kehadiran Klien Diperbarui
    deactivate Modal
```
