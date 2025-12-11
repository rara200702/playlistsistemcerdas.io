# 🚀 Cara Menjalankan Aplikasi Mood-Based Playlist Generator

## Langkah 1: Pastikan XAMPP Running

1. Buka **XAMPP Control Panel**
2. Start **Apache** (klik tombol Start, harus hijau)
3. Start **MySQL** (klik tombol Start, harus hijau)

✅ Jika sudah hijau, lanjut ke langkah berikutnya!

---

## Langkah 2: Import Database

1. Buka browser, ketik: `http://localhost/phpmyadmin`
2. Klik tab **"Import"** di bagian atas
3. Klik tombol **"Choose File"** atau **"Pilih File"**
4. Pilih file: `database/mood_playlist.sql` dari folder project Anda
5. Scroll ke bawah, klik tombol **"Go"** atau **"Kirim"**
6. Tunggu sampai muncul pesan sukses ✅

**Catatan**: Jika ada error, pastikan MySQL sudah running di XAMPP!

---

## Langkah 3: Konfigurasi Database (Opsional)

Edit file `config.php` jika database Anda berbeda:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Sesuaikan jika ada password
define('DB_NAME', 'mood_playlist');
```

**Default sudah untuk XAMPP**, jadi biasanya tidak perlu diubah.

---

## Langkah 4: Akses Aplikasi di Browser

Buka browser dan ketik:

```
http://localhost/Mood-Based-Playlist-Generator/index.php
```

**Atau jika folder di htdocs:**
```
http://localhost/Mood-Based-Playlist-Generator/
```

---

## ✅ Selesai! Aplikasi Sudah Berjalan

### Halaman yang Tersedia:

1. **Home Page** (Kuesioner Mood):
   ```
   http://localhost/Mood-Based-Playlist-Generator/index.php
   ```

2. **Admin Dashboard**:
   ```
   http://localhost/Mood-Based-Playlist-Generator/admin.php
   ```

3. **Manage Playlists**:
   - Tambah: `admin_playlist.php?action=create`
   - Edit: `admin_playlist.php?action=edit&id=1`

4. **Manage Songs**:
   - Tambah: `admin_song.php?action=create`
   - Edit: `admin_song.php?action=edit&id=1`

---

## 🎯 Cara Menggunakan Aplikasi

### Untuk User (Home Page):

1. Pilih **mood** Anda (Energi, Tenang, Galau, dll)
2. Atur **tingkat energi** dengan slider (1-5)
3. Pilih **platform** (Spotify, YouTube, atau Campuran)
4. Klik **"Generate Playlist"**
5. Lihat hasil rekomendasi dan klik link Spotify/YouTube untuk mendengarkan!
6. Bagikan playlist dengan tombol "Bagikan Playlist"

### Untuk Admin:

1. Akses `admin.php`
2. Lihat dashboard dengan statistik
3. Klik **"Tambah Playlist"** atau **"Tambah Lagu"** untuk menambah data
4. Edit atau hapus data yang sudah ada

---

## ⚠️ Troubleshooting

### Error: "Connection failed"
- ✅ Pastikan MySQL sudah running di XAMPP
- ✅ Pastikan database `mood_playlist` sudah diimport
- ✅ Cek konfigurasi database di `config.php`

### Error: "404 Not Found"
- ✅ Pastikan file ada di folder yang benar
- ✅ Pastikan Apache sudah running
- ✅ Cek URL yang digunakan (harus ada `/index.php`)

### Halaman Kosong/Putih
- ✅ Cek error log di XAMPP
- ✅ Pastikan PHP error reporting aktif
- ✅ Cek konfigurasi database
- ✅ Pastikan file `config.php` ada dan benar

### Error: "Call to undefined function"
- ✅ Pastikan PHP versi 7.4 atau lebih tinggi
- ✅ Pastikan extension mysqli sudah aktif di php.ini

---

## 📝 Checklist Sebelum Menjalankan

- [ ] XAMPP sudah terinstall
- [ ] Apache dan MySQL sudah running (hijau)
- [ ] Database sudah diimport via phpMyAdmin
- [ ] File `config.php` sudah ada
- [ ] Browser sudah dibuka dengan URL yang benar

---

## 🎉 Selamat! Aplikasi Siap Digunakan!

Jika semua langkah sudah dilakukan dan tidak ada error, aplikasi Anda sudah berjalan dengan sempurna! 🎵

**Keuntungan PHP Native:**
- ✅ Tidak perlu install Composer
- ✅ Tidak perlu install Laravel
- ✅ Tidak perlu install dependency apapun
- ✅ Langsung jalan setelah import database
- ✅ Sederhana dan mudah dipahami

---

**Tips**: 
- Simpan XAMPP tetap running saat menggunakan aplikasi
- Jika ada perubahan di database, refresh browser
- Untuk development, gunakan browser dengan Developer Tools (F12)
