# ShadAlkane Tools

Portal web tools serbaguna yang dibangun menggunakan **Laravel 12** dan **PHP 8.2+**. Dilengkapi dengan tema glassmorphism bergradasi Merah–Ungu–Orange yang modern dan responsif.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Prasyarat](#prasyarat)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Autentikasi](#autentikasi)
- [Tools](#tools)
  - [YouTube Downloader](#1-youtube-downloader)
  - [QR Code Generator](#2-qr-code-generator)
  - [Image Editor](#3-image-editor)
- [Struktur Project](#struktur-project)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)

---

## Fitur Utama

- **Autentikasi** — Login dengan username & password, dilindungi session berbasis database
- **Session 10 Hari** — Session disimpan di database dengan masa aktif 10 hari (14.400 menit)
- **YouTube Downloader** — Ambil info video YouTube dan pilih resolusi download
- **QR Code Generator** — Buat QR code dari URL/teks dengan kustomisasi warna dan ukuran, download sebagai PNG
- **Image Editor** — Editor gambar lengkap dengan crop, resize, rotate, flip, filter, dan remove background
- **Tema Glassmorphism** — Desain modern dengan efek blur kaca, gradasi Merah:Ungu:Orange (60%:20%:20%)
- **Responsif** — Tampilan optimal di desktop maupun perangkat mobile

---

## Prasyarat

Pastikan perangkat Anda sudah terinstal:

| Software | Versi Minimum | Keterangan |
|----------|--------------|------------|
| PHP | 8.2 | Dengan ekstensi yang diperlukan |
| Composer | 2.x | Dependency manager PHP |
| MySQL | 5.7 / 8.x | Database server |
| Python | 3.10+ | Diperlukan untuk yt-dlp |
| yt-dlp | latest | YouTube video downloader |
| FFmpeg | 5.x+ | Merge video+audio streams |
| Node.js | 18+ | Opsional, untuk asset Vite |

Ekstensi PHP yang diperlukan:
- `gd` (untuk pengolahan gambar)
- `pdo_mysql`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`

### Install yt-dlp & FFmpeg

**yt-dlp** (via pip):
```bash
pip install yt-dlp
```

**FFmpeg:**
- **Windows:** `winget install Gyan.FFmpeg` atau download dari [ffmpeg.org](https://ffmpeg.org/download.html)
- **Linux:** `sudo apt install ffmpeg`
- **macOS:** `brew install ffmpeg`

Setelah install, pastikan path ke binary dicatat untuk konfigurasi `.env`.

---

## Instalasi

1. **Clone atau download** project ke direktori web server Anda:

   ```bash
   cd /path/to/htdocs
   git clone <repository-url> shadalkane
   cd shadalkane
   ```

2. **Install dependensi PHP:**

   ```bash
   composer install
   ```

3. **Salin file environment:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Buat database MySQL:**

   ```sql
   CREATE DATABASE shadalkane;
   ```

5. **Konfigurasi `.env`** (lihat bagian [Konfigurasi](#konfigurasi))

6. **Jalankan migrasi dan seeder:**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

   > ⚠️ **Penting:** Catat password admin yang ditampilkan di terminal setelah seeder berjalan. Password ini di-hash dengan bcrypt dan tidak bisa dilihat lagi.

7. **Buat symbolic link untuk storage:**

   ```bash
   php artisan storage:link
   ```

---

## Konfigurasi

Edit file `.env` sesuai lingkungan Anda:

```env
APP_NAME=ShadAlkane

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shadalkane
DB_USERNAME=root
DB_PASSWORD=

# Session (database-backed, 10 hari)
SESSION_DRIVER=database
SESSION_LIFETIME=14400

# YouTube Downloader Dependencies
YTDLP_PATH=C:\path\to\yt-dlp.exe
FFMPEG_PATH=C:\path\to\ffmpeg.exe
```

> **Catatan:** Sesuaikan `YTDLP_PATH` dan `FFMPEG_PATH` dengan lokasi binary di sistem Anda. Di Linux/macOS, jika sudah ada di PATH, cukup isi `yt-dlp` dan `ffmpeg`.

### Pengaturan Session

| Parameter | Nilai | Keterangan |
|-----------|-------|------------|
| `SESSION_DRIVER` | `database` | Session disimpan di tabel `sessions` di MySQL |
| `SESSION_LIFETIME` | `14400` | Masa aktif session: 14.400 menit = 10 hari |

---

## Menjalankan Aplikasi

### Menggunakan PHP built-in server (development):

```bash
php artisan serve
```

Akses di: `http://localhost:8000`

### Menggunakan XAMPP / Apache:

Arahkan DocumentRoot ke folder `public/` atau akses melalui:

```
http://localhost/shadalkane/public
```

---

## Autentikasi

Aplikasi menggunakan sistem login berbasis **username dan password** (bukan email).

### Akun Default

Setelah menjalankan `php artisan db:seed`, akun admin akan dibuat secara otomatis:

| Field | Nilai |
|-------|-------|
| Username | `admin` |
| Password | *(ditampilkan saat seeder berjalan — catat segera!)* |

### Keamanan

- Password di-hash menggunakan **Bcrypt** dengan **12 rounds**
- Password admin di-generate secara kriptografis menggunakan `random_bytes(16)` — menghasilkan 32 karakter hex
- Session disimpan di database untuk keamanan lebih baik dibanding file
- Proteksi CSRF pada semua form
- Session di-regenerate setelah login untuk mencegah session fixation

### Alur Login

1. Buka aplikasi → otomatis redirect ke halaman login
2. Masukkan username dan password
3. Centang "Ingat saya" jika ingin session lebih lama
4. Setelah login, masuk ke Dashboard

---

## Tools

### 1. YouTube Downloader

**URL:** `/youtube`

Fitur untuk mendapatkan informasi dan download video YouTube menggunakan **yt-dlp** dan **FFmpeg**.

#### Cara Penggunaan:
1. Salin URL video dari YouTube (contoh: `https://www.youtube.com/watch?v=xxxxx`)
2. Paste di kolom input (mendukung juga format Shorts: `youtube.com/shorts/...`)
3. Klik **"Cari"** atau tekan Enter
4. Informasi video ditampilkan (thumbnail, judul, channel, durasi)
5. Pilih resolusi yang diinginkan — format HD akan otomatis merge video+audio di server

#### Format URL yang Didukung:
- `https://www.youtube.com/watch?v=xxxxx`
- `https://youtu.be/xxxxx`
- `https://youtube.com/shorts/xxxxx`
- `https://youtube.com/embed/xxxxx`

#### Resolusi yang Tersedia:
Resolusi ditampilkan secara dinamis berdasarkan video. Umumnya:

| Resolusi | Tipe | Keterangan |
|----------|------|------------|
| 1080p | Merge | Video+audio di-merge di server via FFmpeg |
| 720p | Merge | Video+audio di-merge di server via FFmpeg |
| 480p | Merge | Video+audio di-merge di server via FFmpeg |
| 360p | Combined | Stream langsung (sudah ada audio) |
| 240p | Merge | Video+audio di-merge di server |
| 144p | Merge | Video+audio di-merge di server |
| Audio Only | Audio | Download audio M4A |

#### Fitur Teknis:
- **yt-dlp** untuk mengambil metadata video dan direct download URL
- **FFmpeg** untuk merge video-only + audio-only streams menjadi MP4
- Format merge di-download dan diproses di server, lalu dikirim ke browser
- Format combined langsung redirect ke Google CDN URL
- Temp files otomatis dibersihkan (>1 jam)
- Validasi format URL YouTube (youtube.com, youtu.be, shorts, embed)
- Menampilkan thumbnail, judul, channel, dan durasi video
- Custom `proc_open` environment untuk kompatibilitas Windows

---

### 2. QR Code Generator

**URL:** `/qrcode`

Fitur untuk membuat QR code dari URL atau teks apapun.

#### Cara Penggunaan:
1. Ketik atau paste URL/teks di kolom input
2. (Opsional) Atur ukuran QR code: 200px, 300px, 400px, atau 500px
3. (Opsional) Pilih warna QR code menggunakan color picker
4. Klik **"Generate QR Code"** atau tekan Enter
5. QR code akan ditampilkan di bawah
6. Klik **"Download PNG"** untuk menyimpan sebagai file gambar

#### Opsi Kustomisasi:
| Opsi | Rentang | Default |
|------|---------|---------|
| Ukuran | 200–500 px | 300 px |
| Warna | Semua warna (color picker) | Hitam (#000000) |

#### Fitur Teknis:
- Menggunakan library **SimpleSoftwareIO/SimpleQRCode**
- QR code di-generate sebagai SVG di server
- Konversi otomatis ke PNG saat download (via HTML Canvas)
- Error correction level **H** (High) — tahan terhadap kerusakan ~30%
- Maksimum 2048 karakter input

---

### 3. Image Editor

**URL:** `/image-editor`

Editor gambar berbasis browser dengan berbagai fitur editing.

#### Cara Upload:
- **Klik** area upload untuk memilih file, atau
- **Drag & drop** gambar langsung ke area upload
- Format: JPEG, PNG, GIF, WebP
- Ukuran maksimum: 10 MB

#### Fitur Editing:

##### Transformasi
| Tool | Fungsi |
|------|--------|
| **Crop** | Seleksi area dengan mouse lalu konfirmasi untuk memotong |
| **Resize** | Ubah ukuran gambar dengan opsi lock aspect ratio |
| **Rotate R** | Putar 90° ke kanan |
| **Rotate L** | Putar 90° ke kiri |
| **Flip H** | Cermin horizontal |
| **Flip V** | Cermin vertikal |

##### Adjustments (Slider)
| Adjustment | Rentang | Default |
|------------|---------|---------|
| Brightness | 0–200% | 100% |
| Contrast | 0–200% | 100% |
| Saturation | 0–200% | 100% |
| Blur | 0–20 px | 0 px |
| Grayscale | 0–100% | 0% |
| Sepia | 0–100% | 0% |
| Hue Rotate | 0–360° | 0° |

##### Quick Filters (Preset)
| Filter | Efek |
|--------|------|
| **Vintage** | Warna hangat klasik dengan sedikit desaturasi |
| **Cool** | Nada biru dingin |
| **Warm** | Nada jingga hangat |
| **B&W** | Hitam putih dengan kontras tinggi |
| **Dramatic** | Kontras dan saturasi ekstrem |
| **Reset** | Kembalikan ke pengaturan asli |

##### Remove Background
- Klik tombol **"Remove Background"**
- Klik pada area background yang ingin dihapus
- Menggunakan algoritma **flood fill** berbasis tolerance
- Area yang warnanya mirip dengan titik klik akan dibuat transparan
- Klik lagi tombol untuk menonaktifkan mode ini

##### Lainnya
- **Undo** — Batalkan aksi terakhir (hingga 20 langkah)
- **Buka Lain** — Upload gambar baru tanpa reload halaman
- **Download** — Simpan hasil edit sebagai file PNG (termasuk filter yang diterapkan)

---

## Struktur Project

```
shadalkane/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php        # Login & logout
│   │   ├── DashboardController.php   # Halaman utama
│   │   ├── YoutubeController.php     # YouTube downloader
│   │   ├── QrCodeController.php      # QR code generator
│   │   └── ImageEditorController.php # Image editor (upload & save)
│   └── Models/
│       └── User.php                  # Model user (dengan username)
├── database/
│   ├── migrations/                   # Migrasi tabel (users, sessions, dll)
│   └── seeders/
│       └── DatabaseSeeder.php        # Seeder akun admin
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php            # Layout utama (navbar, glassmorphism)
│   ├── auth/
│   │   └── login.blade.php          # Halaman login
│   ├── dashboard.blade.php          # Dashboard dengan card tools
│   └── tools/
│       ├── youtube.blade.php        # YouTube downloader UI
│       ├── qrcode.blade.php         # QR code generator UI
│       └── image-editor.blade.php   # Image editor UI (canvas-based)
├── routes/
│   └── web.php                      # Definisi semua route
├── .env                             # Konfigurasi environment
└── composer.json                    # Dependensi PHP
```

---

## Teknologi yang Digunakan

| Kategori | Teknologi |
|----------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL |
| Session | Database-backed (10 hari) |
| YouTube DL | yt-dlp + FFmpeg (server-side merge) |
| QR Code | SimpleSoftwareIO/SimpleQRCode |
| Image Editor | HTML5 Canvas API (vanilla JavaScript) |
| Auth | Laravel built-in Auth (bcrypt) |
| Icons | Font Awesome 6.5 |
| Fonts | Google Fonts (Inter) |
| UI | Custom CSS Glassmorphism (tanpa framework CSS) |

---

## Rute Aplikasi

| Method | URL | Fungsi | Middleware |
|--------|-----|--------|------------|
| GET | `/login` | Halaman login | guest |
| POST | `/login` | Proses login | guest |
| GET | `/` | Redirect ke dashboard | — |
| GET | `/dashboard` | Halaman utama | auth |
| POST | `/logout` | Proses logout | auth |
| GET | `/youtube` | YouTube Downloader | auth |
| POST | `/youtube/info` | Ambil info video | auth |
| POST | `/youtube/download` | Download video | auth |
| GET | `/youtube/serve` | Serve file merge | auth |
| GET | `/qrcode` | QR Code Generator | auth |
| POST | `/qrcode/generate` | Generate QR code | auth |
| GET | `/image-editor` | Image Editor | auth |
| POST | `/image-editor/upload` | Upload gambar | auth |
| POST | `/image-editor/save` | Simpan hasil edit | auth |

---

## Lisensi

Hak cipta © 2026 ShadAlkane. All rights reserved.
