# Aplikasi Peminjaman Alat

Aplikasi ini dibuat menggunakan PHP Native, MySQL, dan Bootstrap 5.

## Fitur
1. **Admin**: Manajemen User, Alat, Kategori, dan melihat log peminjaman.
2. **Petugas**: Menyetujui peminjaman, memproses pengembalian, dan cetak laporan.
3. **Peminjam**: Melihat katalog alat, mengajukan peminjaman, dan melihat riwayat.

## Instalasi
1. Pastikan XAMPP terinstall.
2. Copy folder `peminjaman_alat` ke dalam direktori `htdocs` (biasanya `C:\xampp\htdocs\`).
3. Buka phpMyAdmin (`http://localhost/phpmyadmin`).
4. Buat database baru dengan nama `db_peminjaman`.
5. Import file `db_peminjaman.sql` ke dalam database `db_peminjaman`.
6. Atur konfigurasi database jika perlu di `config/database.php`.

## Login Default (Username / Password)
- **Admin**: `admin` / `123456`
- **Petugas**: `petugas` / `123456`
- **Peminjam**: `peminjam` / `123456`

## Catatan
- Password default di database adalah hash dari `123456`.
- Folder Upload gambar: `assets/img/`. Pastikan folder ini memiliki hak akses write jika di Linux/Mac.
