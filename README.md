# 💼 HRIS System (Human Resource Information System)

Sistem HRIS (Human Resource Information System) ini merupakan aplikasi berbasis web yang dirancang untuk membantu perusahaan dalam mengelola data karyawan secara efisien dan terintegrasi.

Aplikasi ini dikembangkan menggunakan framework Laravel dengan struktur MVC yang rapi dan scalable, sehingga memudahkan pengembangan lanjutan di masa depan.

Fitur Utama

* Manajemen data karyawan (CRUD)
* Sistem autentikasi (login & logout)
* Pengelolaan data jabatan dan departemen
* Dashboard informasi karyawan
* Struktur sistem berbasis MVC (Laravel)

Teknologi yang Digunakan

* PHP (Laravel Framework)
* MySQL Database
* Bootstrap (Frontend UI)
* JavaScript

Struktur Project

Project ini menggunakan struktur standar Laravel:

* `app/` → Logic aplikasi
* `routes/` → Routing sistem
* `resources/` → View / tampilan
* `database/` → Migration & seeding
* `public/` → Akses publik

Cara Menjalankan Project

1. Clone repository

```bash
git clone https://github.com/GalihPratamaRahadian/hris-system.git
```

2. Masuk ke folder project

```bash
cd hris-system
```

3. Install dependency

```bash
composer install
```

4. Copy file environment

```bash
cp .env.example .env
```

5. Generate key

```bash
php artisan key:generate
```

6. Setting database di file `.env`

7. Migrasi database

```bash
php artisan migrate
```

8. Jalankan server

```bash
php artisan serve
```


## Catatan

Project ini dibuat sebagai bagian dari pengembangan kemampuan dalam bidang sistem informasi dan pengelolaan data berbasis web.


## Developer

Dikembangkan oleh **Galih Pratama Rahadian**
Lulusan S1 Sistem Informasi (2023) Universitas Catur Insan Cendekia Kota Cirebon

