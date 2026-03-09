<div align="center">

<br/>

```
███████╗██╗███╗   ██╗████████╗██████╗  █████╗  ██████╗██╗  ██╗
██╔════╝██║████╗  ██║╚══██╔══╝██╔══██╗██╔══██╗██╔════╝██║ ██╔╝
█████╗  ██║██╔██╗ ██║   ██║   ██████╔╝███████║██║     █████╔╝
██╔══╝  ██║██║╚██╗██║   ██║   ██╔══██╗██╔══██║██║     ██╔═██╗
██║     ██║██║ ╚████║   ██║   ██║  ██║██║  ██║╚██████╗██║  ██╗
╚═╝     ╚═╝╚═╝  ╚═══╝   ╚═╝   ╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝
```

**Aplikasi manajemen keuangan pribadi berbasis web, mobile-first.**  
Catat pemasukan, pengeluaran, transfer, dan pantau saldo semua rekening dalam satu tempat.

<br/>

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-CDN-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

<br/>

</div>

---

## 📋 Daftar Isi

- [Tentang Aplikasi](#-tentang-aplikasi)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Struktur Database](#-struktur-database)
- [Cara Instalasi](#-cara-instalasi)
- [Logika Bisnis](#-logika-bisnis-transaksi)
- [Struktur Folder](#-struktur-folder)
- [Roadmap](#-roadmap)
- [Lisensi](#-lisensi)

---

## 💡 Tentang Aplikasi

**FinTrack** adalah aplikasi pencatatan keuangan pribadi yang dirancang dengan tampilan modern ala mobile banking Indonesia (seperti BeyondBSI / Livin by Mandiri). Dibangun di atas **Laravel 12** dengan antarmuka **mobile-first** menggunakan Tailwind CSS dan Alpine.js.

Cocok digunakan oleh:

- Individu yang ingin mencatat keuangan harian secara terstruktur
- Agen transfer / jasa keuangan yang perlu mencatat fee & komisi
- Siapa saja yang punya banyak rekening (bank, e-wallet, cash) dan ingin melihatnya dalam satu dashboard

---

## ✨ Fitur Utama

| Fitur                     | Deskripsi                                                                |
| ------------------------- | ------------------------------------------------------------------------ |
| 🔐 **Autentikasi**        | Register, Login, Logout dengan session aman                              |
| 🏦 **Manajemen Rekening** | Tambah rekening Bank, E-Wallet, Tunai, Investasi, Tabungan, Kartu Kredit |
| ➕ **Pemasukan**          | Catat saldo masuk dengan kategori                                        |
| ➖ **Pengeluaran**        | Catat saldo keluar dengan info penerima, bank tujuan, biaya admin        |
| 🔄 **Transfer**           | Pindah saldo antar rekening sendiri, biaya admin tercatat otomatis       |
| ⚖️ **Penyesuaian Saldo**  | Set ulang saldo rekening ke angka tertentu                               |
| 📊 **Dashboard**          | Total saldo, chart arus keuangan 6 bulan, ringkasan bulanan              |
| ✏️ **Edit Transaksi**     | Edit keterangan, nominal, kategori, tanggal (selain transfer)            |
| 🗑️ **Hapus Transaksi**    | Hapus dengan rollback saldo otomatis                                     |
| 🔍 **Filter & Pencarian** | Filter per jenis, rekening, bulan; pencarian teks bebas                  |
| 💳 **Preset Bank**        | BRI, BCA, Mandiri, BNI, BSI, DANA, GoPay, OVO, ShopeePay, Cash           |
| 🎨 **Kustomisasi**        | Pilih warna & icon untuk setiap rekening                                 |
| 💸 **Manajemen Hutang**   | Catat hutang, cicilan, penyesuaian nominal, riwayat pembayaran           |
| 📱 **Mobile-First UI**    | Tampilan responsif seperti aplikasi mobile banking                       |

---

## 🛠 Tech Stack

```
Backend    : Laravel 12 (PHP 8.2+)
Database   : MySQL 8.0+
Frontend   : Blade Templates
CSS        : Tailwind CSS (via CDN)
JavaScript : Alpine.js (via CDN)
Font       : Plus Jakarta Sans (Google Fonts)
Auth       : Laravel built-in session auth
```

---

## 🗄 Struktur Database

```
users
├── id, name, email, password
└── phone, currency, avatar

accounts
├── id, user_id
├── name, account_number, account_holder, bank_name
├── type: cash | bank | e_wallet | investment | savings | credit
├── icon, color
├── balance, initial_balance, currency
└── is_active, include_in_total, sort_order

categories
├── id, user_id (nullable = kategori global)
├── name, icon, color
└── type: income | expense

transactions
├── id, user_id, account_id, category_id
├── destination_account_id   (untuk transfer)
├── type: income | expense | transfer | adjustment
├── transfer_type: debit | credit
├── amount, admin_fee
├── balance_before, balance_after
├── recipient_name, recipient_account, reference_code
├── description, notes, transaction_date
└── transfer_pair_id         (menghubungkan debit ↔ credit transfer)

debts
├── id, user_id, transaction_id
├── debtor_name, account, bank
├── original_amount, adjusted_amount, paid_amount
├── status: unpaid | partial | paid
└── due_date, notes

debt_payments
├── id, debt_id, account_id
├── type: payment | adjustment
├── amount, notes
└── payment_date
```

---

## 🚀 Cara Instalasi

### Prasyarat

- PHP >= 8.2
- Composer
- MySQL 8.0+

### Langkah Instalasi

**1. Clone repository**

```bash
git clone https://github.com/username/fintrack.git
cd fintrack
```

**2. Install dependencies**

```bash
composer install
```

**3. Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi database di `.env`**

```env
APP_NAME=FinTrack
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fintrack_db
DB_USERNAME=root
DB_PASSWORD=your_password

SESSION_DRIVER=database
```

**5. Buat database & jalankan migrasi**

```bash
mysql -u root -p -e "CREATE DATABASE fintrack_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate
php artisan db:seed
```

**6. Jalankan aplikasi**

```bash
php artisan serve
```

Buka browser di **http://localhost:8000**, lalu daftar akun baru.

---

## 💼 Logika Bisnis Transaksi

### Pemasukan

```
saldo_rekening += amount
```

### Pengeluaran

```
saldo_rekening -= (amount + admin_fee)

[Opsional] jika ada fee_income_account_id:
  → admin_fee dicatat sebagai pemasukan di rekening tersebut
```

### Transfer Antar Rekening

```
Dibuat 2 transaksi berpasangan (transfer_pair_id):
  Debit  → rekening asal   : -= (amount + admin_fee)
  Credit → rekening tujuan : += amount

[Opsional] jika ada fee_income_account_id:
  → admin_fee dicatat sebagai komisi/jasa transfer
```

### Penyesuaian Saldo

```
saldo_rekening = amount  (di-set langsung, tanpa kalkulasi)
```

### Hapus Transaksi (rollback otomatis)

```
income     → saldo -= amount
expense    → saldo += (amount + admin_fee)
transfer   → rollback kedua sisi (debit & credit)
```

---

## 📁 Struktur Folder

```
fintrack/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── AccountController.php
│   │   ├── TransactionController.php
│   │   └── DebtController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Account.php
│   │   ├── Category.php
│   │   ├── Transaction.php
│   │   ├── Debt.php
│   │   └── DebtPayment.php
│   ├── Policies/
│   │   ├── AccountPolicy.php
│   │   ├── TransactionPolicy.php
│   │   └── DebtPolicy.php
│   └── Services/
│       └── TransactionService.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/
│   ├── dashboard/
│   ├── accounts/
│   ├── transactions/
│   └── debts/
│
└── routes/web.php
```

---

## 📌 Tips Penggunaan

> **Pertama kali setup?**  
> Tambah rekening terlebih dahulu → isi saldo awal di field _"Saldo Awal"_ saat membuat rekening.

> **Transfer ke orang lain (bukan rekening sendiri)?**  
> Gunakan menu **Pengeluaran**, bukan Transfer. Transfer hanya untuk antar rekening milik sendiri.

> **Agen / jasa transfer dan dapat komisi?**  
> Centang opsi _"Catat jasa sebagai pemasukan"_ di form Transfer/Pengeluaran, lalu pilih rekening tujuan komisi.

> **Saldo tidak sesuai kenyataan?**  
> Gunakan **Penyesuaian Saldo** untuk menyamakan saldo tanpa menghapus riwayat transaksi.

---

## 🛣 Roadmap

- [ ] Laporan & statistik per kategori (pie chart)
- [ ] Export laporan ke PDF / Excel
- [ ] Notifikasi jatuh tempo hutang
- [ ] Halaman profil pengguna (nama, avatar, ganti password)
- [ ] Multi-currency support
- [ ] Dark mode

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<div align="center">

Dibuat dengan ❤️ menggunakan **Laravel 12**

</div>
