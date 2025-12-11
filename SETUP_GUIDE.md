# Quick Setup Guide

## Setup Cepat (5 Menit)

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Database
```bash
# Buat database di phpMyAdmin: mood_playlist
# Edit .env file dengan konfigurasi database
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### 3. Akses Aplikasi
```
http://localhost/Mood-Based-Playlist-Generator/public
```

## Struktur URL

- **Home**: `/` - Kuesioner mood
- **Admin Dashboard**: `/admin` - Manajemen playlist & lagu
- **Playlist Detail**: `/playlist/{id}` - Detail playlist
- **Share Playlist**: `/share/{id}` - Halaman share

## Fitur Utama

1. **Kuesioner Mood**: Pilih mood, tingkat energi, dan platform
2. **Rekomendasi**: Sistem rule-based memberikan rekomendasi
3. **Admin CRUD**: Kelola playlist dan lagu
4. **Share**: Bagikan playlist yang dibuat

## Mood yang Tersedia

- Energi
- Tenang
- Galau
- Bahagia
- Romantis
- Semangat

## Menambah Data

### Via Admin Panel
1. Akses `/admin`
2. Klik "Tambah Lagu" atau "Tambah Playlist"
3. Isi form dan simpan

### Via Seeder (Sample Data)
Data sample sudah tersedia setelah menjalankan `php artisan db:seed`

## Tips

- Gunakan link Spotify/YouTube yang valid
- Setiap lagu harus memiliki mood label
- Playlist bisa memiliki banyak lagu
- Sistem rekomendasi menggunakan rule-based algorithm

