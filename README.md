# Selowa

Selowa adalah aplikasi Laravel untuk operasional perusahaan isi ulang air minum.

## Stack

- Laravel 12
- MySQL database: `selowa`
- Template UI: Inspinia asset lama di `public/css`, `public/js`, dan `resources/views/layouts`
- Role/permission: `spatie/laravel-permission`

## Modul Awal

- Login menggunakan tabel `users`.
- Profile user: `/profile`, termasuk update foto, email, nama, dan password.
- CRUD User: `/users`
- CRUD Role: `/roles`
- CRUD Menu: `/menus`
- Role Menu: `/role-menus`
- Company: `/company`
- Pelanggan: `/customers`
- Update harga pelanggan serentak: `/customers-bulk-price`
- Alamat Pelanggan: `/customer-addresses`
- Transaksi: `/transactions`
- Mobil berjalan: `/delivery-runs`, mendukung lebih dari satu mobil dalam satu hari.
- Pendapatan: `/income`
- Pelanggan Setia: `/loyal-customers`
- Stok Galon: `/gallons`
- Laporan operasional: `/reports`
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
- Data server lama dapat ditarik via web dengan command `php artisan selowa:scrape-legacy --email=... --password=...`; command ini login ke aplikasi lama, scrape halaman customer/transaksi, lalu upsert ke tabel baru berdasarkan ID customer dan kode transaksi.
- Transaksi hasil scrape menyimpan `customer_name_snapshot` agar nama pelanggan lama tetap tampil ketika ID customer tidak bisa dipetakan karena data lama memiliki nama pelanggan yang kembar.
- Layout admin memakai pencarian menu di sidebar, DataTables untuk tabel, dan Select2 untuk pilihan data.
- Menu default: Dashboard, Profile, Data Master, Transaksi, Laporan, Logout, Setting.
- `Setting` berisi Company, Role, Menu, dan Role Menu.
- Diskon member disimpan di tabel `customers` melalui kolom `is_member`, `discount_type`, dan `discount_value`; harga transaksi default memakai harga efektif setelah diskon.
- Pengiriman harian memakai `delivery_vehicles` dan `delivery_runs`; transaksi dapat dihubungkan ke jadwal mobil berjalan.

## Alur Lanjutan

Setiap penambahan modul baru sebaiknya dicatat di README ini:

1. Nama modul dan route.
2. Tabel/migration yang ditambahkan.
3. Role/menu akses default.
4. Catatan proses bisnis.
