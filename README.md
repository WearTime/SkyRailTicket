# SKYRAILTICKET 🎟️

### 📌 Overview
SKYRAILTICKET adalah sistem web ticketing yang dibuat menggunakan PHP, dirancang untuk mempermudah pemesanan dan pengelolaan tiket. Dengan arsitektur modular, proyek ini mendukung skalabilitas serta kemudahan dalam pengembangan lebih lanjut.

### 📁 Project Structure
Struktur proyek ini terbagi dalam beberapa direktori utama:
```
SKYRAILTICKET/
│── admin/               # Halaman admin untuk mengelola tiket & booking  
│── assets/              # Berisi CSS, JavaScript, dan gambar  
│   ├── css/             # Styling halaman web  
│   ├── image/           # Kumpulan aset gambar  
│   ├── js/              # Script JavaScript untuk interaksi  
│   ├── config/          # Konfigurasi sistem  
│   ├── handler/         # Proses backend dan request handling  
│   ├── layouts/admin/   # Template layout untuk admin  
│   ├── uploads/         # Direktori untuk file yang diunggah  
│── hosts/               # Folder terkait pengelolaan host  
│── tickets/             # Folder utama untuk pemrosesan tiket  
│── index.php            # Beranda aplikasi  
│── login.php            # Halaman login  
│── register.php         # Halaman registrasi  
│── search.php           # Fitur pencarian tiket  
│── logout.php           # Proses logout pengguna  
│── 404.php              # Halaman untuk error 404  
│── .htaccess            # Konfigurasi Apache untuk URL rewriting  
```
### 🚀 Installation
Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek:
- Clone repository:
```bash
git clone https://github.com/yourusername/SKYRAILTICKET.git
cd SKYRAILTICKET
```
- Konfigurasi Database
- Buat database baru dan import file .sql yang tersedia.
- Perbarui kredensial database di assets/config/config.php.
- Menjalankan Server
- Gunakan XAMPP atau WAMP sebagai environment lokal.
- Pastikan .htaccess dikonfigurasi dengan benar untuk URL rewriting.
  
### 🔥 Features
- Admin Panel: Kelola pemesanan dan tiket dengan antarmuka admin.
- User Authentication: Sistem login dan registrasi pengguna.
- Advanced Ticket Search: Pencarian tiket berdasarkan kriteria tertentu.
- Secure Transactions: Perlindungan terhadap akses tidak sah.

### 🛡️ License
Proyek ini berlisensi `APACHE 2.0`, silakan cek `LICENSE` untuk detail lebih lanjut.

