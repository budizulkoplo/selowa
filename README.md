# Selowa

Selowa adalah aplikasi Laravel untuk operasional perusahaan isi ulang air minum.

## Stack

- Laravel 12
- MySQL database: `selowa`
- Template UI: Inspinia asset lama di `public/css`, `public/js`, dan `resources/views/layouts`
- Role/permission: `spatie/laravel-permission`

## Modul Awal

- Login menggunakan tabel `users`.
- CRUD User: `/users`
- CRUD Role: `/roles`
- CRUD Menu: `/menus`
- Role Menu: `/role-menus`
- Company: `/company`
- Pelanggan: `/customers`
- Alamat Pelanggan: `/customer-addresses`
- Transaksi: `/transactions`
- Pendapatan: `/income`
- Pelanggan Setia: `/loyal-customers`
- Stok Galon: `/gallons`
- Dashboard: metrik pelanggan/transaksi/pendapatan, form transaksi cepat, dan tabel pelanggan klik untuk pilih pelanggan.

Role awal:

- `owner`
- `superadmin`
- `admin`
- `operator`

User awal:

- Email: `owner@selowa.local`
- Password: `password`

## Catatan Arsitektur

- Tabel `menus` menyimpan tree menu.
- Pivot `menu_role` menentukan role yang boleh melihat menu.
- Role `owner` dan `superadmin` selalu melihat semua menu aktif.
- Logo aplikasi menggunakan `public/selowa.webp`.
- Data legacy dari `D:\selowa.sql` diimport melalui database sementara `selowa_legacy` dengan command `php artisan selowa:import-legacy selowa_legacy`.
- Tabel legacy yang dimigrasikan: `company`, `cities`, `districts`, `villages`, `customer`, `gallon`, `hd_transaction`.
- Layout admin memakai pencarian menu di sidebar, DataTables untuk tabel, dan Select2 untuk pilihan data.

## Alur Lanjutan

Setiap penambahan modul baru sebaiknya dicatat di README ini:

1. Nama modul dan route.
2. Tabel/migration yang ditambahkan.
3. Role/menu akses default.
4. Catatan proses bisnis.
