# 🌿 HABITIFY

> Platform Digital Cerdas untuk Pembinaan Adaptif dan Dukungan Mental Santri Berbasis Web

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-yellow.svg)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Deskripsi

Habitify adalah aplikasi Expert System berbasis web yang menggunakan metode **Forward Chaining** untuk membantu Guru BK (Bimbingan Konseling) dalam:

- 📝 Mengelola laporan pelanggaran & apresiasi santri
- 🧠 Menganalisis kondisi mental santri secara otomatis
- 📊 Memberikan diagnosis dan rekomendasi penanganan
- 📱 Notifikasi real-time ke Wali Santri via WhatsApp

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 11 |
| **Admin Panel** | Filament 3 |
| **Database** | MySQL 8 |
| **Authentication** | Laravel Sanctum + Google OAuth |
| **NLP** | Sastrawi PHP (Indonesian Stemmer) |
| **Real-time** | Laravel Echo + Pusher |
| **Notification** | WhatsApp API (Fonnte) |

---

## 📁 Clean Architecture Structure

```
app/
├── Domain/                     # Business Logic (Pure PHP)
│   ├── Entities/               # Business Entities
│   ├── Enums/                  # Enumerations
│   ├── Repositories/           # Repository Interfaces
│   ├── Services/               # Domain Services (Use Cases)
│   └── ValueObjects/           # Value Objects
│
├── Infrastructure/             # Framework Implementation
│   ├── Persistence/
│   │   ├── Models/             # Eloquent Models
│   │   └── Repositories/       # Repository Implementations
│   ├── Services/               # External Service Implementations
│   └── Providers/              # Service Providers
│
├── Application/                # Application Layer
│   ├── Actions/                # Single Action Classes
│   ├── DTOs/                   # Data Transfer Objects
│   ├── Jobs/                   # Queue Jobs
│   └── Listeners/              # Event Listeners
│
└── Presentation/               # UI Layer
    └── Filament/
        ├── SuperAdmin/         # Super Admin Panel
        ├── Bk/                 # Guru BK Panel
        ├── Pengajar/           # Pengajar Panel
        ├── Wali/               # Wali Santri Panel
        └── Santri/             # Santri Panel
```

---

## 🚀 Installation

### Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18

### Steps

```bash
# 1. Clone repository
git clone https://github.com/rizzdev31/habitify.git
cd habitify

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=habitify
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations & seeders
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Start server
php artisan serve
```

---

## 👥 User Roles & Panels

| Role | Panel URL | Access |
|------|-----------|--------|
| Super Admin | `/superadmin` | Full system access |
| Guru BK | `/bk` | Reports, Expert System, Counseling |
| Pengajar | `/pengajar` | Submit reports |
| Wali Santri | `/wali` | Submit reports, Monitor child |
| Santri | `/santri` | View profile & points |

---

## 🎨 Design System

### Colors
- **Primary (CTA):** `#FF9B51` - Warm Orange
- **Secondary (Text):** `#25343F` - Deep Navy
- **Background:** `#EAEFEF` - Soft Cloud
- **Border:** `#BFC9D1` - Muted Silver

### Typography
- **Font:** Plus Jakarta Sans
- **Weights:** Regular (400), Bold (700), ExtraBold (800)

---

## 📖 Documentation

- [Architecture Guide](docs/ARCHITECTURE.md)
- [API Documentation](docs/API.md)
- [Expert System Rules](docs/EXPERT_SYSTEM.md)
- [Deployment Guide](docs/DEPLOYMENT.md)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Rizz Dev** - [GitHub](https://github.com/rizzdev31)