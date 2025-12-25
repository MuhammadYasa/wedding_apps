<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">💒 Wedding Invitation App - Yii2</h1>
    <br>
</p>

Aplikasi undangan pernikahan digital berbasis Yii2 Framework dengan Role-Based Access Control (RBAC). Aplikasi ini memungkinkan Anda membuat dan mengelola undangan pernikahan online dengan fitur RSVP, gallery, personalized links untuk WhatsApp, Google OAuth authentication, dan multi-tenant admin dashboard.

## ✨ Fitur Utama

### Public Features

- 🎨 **Halaman Undangan Publik** - Tampilan undangan dengan slug unik
- ⏱️ **Countdown Timer** - Hitung mundur hari ke acara pernikahan
- 📸 **Photo Gallery** - Galeri foto dengan fullscreen lightbox dan keyboard navigation
- 📝 **RSVP Form** - Form konfirmasi kehadiran tamu
- 📍 **Google Maps Integration** - Lokasi acara dengan peta
- 💌 **Personalized Guest Links** - Link khusus untuk setiap tamu via token
- 📱 **WhatsApp Distribution** - Link WhatsApp siap copy-paste untuk manual sharing
- 🎭 **Multiple Themes** - Template undangan yang dapat diganti

### Authentication & Authorization

- 🔐 **Dual Authentication** - Login via username/password atau Google OAuth
- 🌐 **Google OAuth Integration** - Login dengan akun Google (email harus terdaftar)
- 👥 **Role-Based Access Control** - 2 role: Super User & Client User
- ✅ **Email Validation** - Hanya email yang sudah didaftarkan yang bisa login via Google
- 🔒 **Status Management** - Kontrol akses user dengan status aktif/non-aktif

### Admin Features - Super User

- 👔 **Full System Access** - Akses penuh ke semua fitur
- 📋 **Invitation Management** - Kelola semua undangan dari semua client
- 👥 **User Management** - Create, read, update user accounts dengan role assignment
- 🎯 **Client Assignment** - Assign undangan ke client users
- 🔓 **No Status Restriction** - Super user tidak terpengaruh status management
- 📊 **System-wide Dashboard** - Lihat semua data RSVP dan guest dari semua client

### Admin Features - Client User

- 📋 **Own Invitation Management** - Kelola undangan milik sendiri
- 👥 **Guest Management** - Kelola daftar tamu untuk undangan sendiri
- 💬 **RSVP Management** - Lihat konfirmasi kehadiran untuk undangan sendiri
- 📸 **Gallery Upload** - Upload dan kelola foto untuk undangan sendiri
- 📊 **Export RSVP to CSV** - Export data RSVP untuk analisis
- 📲 **WhatsApp Link Generator** - Generate pre-filled WhatsApp links (manual copy-paste)
- ⚙️ **Invitation Customization** - Edit detail pernikahan, nama pasangan, tanggal acara

### Status Management

- 🔄 **Bidirectional Sync** - Status user ↔ invitation tersinkronisasi otomatis
- 🚫 **Access Control** - User non-aktif tidak bisa login dan undangannya tidak bisa diakses
- 🛡️ **Super User Exemption** - Status tidak berlaku untuk super user
- 🎚️ **Toggle Control** - Toggle status dengan satu klik di admin dashboard

## 🛠️ Technology Stack

- **Backend**: Yii2 Basic Application Template
- **Database**: MySQL / MariaDB
- **Frontend**: Bootstrap 5 + Bootstrap Icons + Vanilla JavaScript
- **Authentication**: yii2-authclient (Google OAuth 2.0)
- **Email**: SwiftMailer (Yii2 integrated)
- **Server**: PHP 7.4+ / 8.0+
- **RBAC**: Custom role-based access control implementation

## 📋 Requirements

- PHP >= 7.4 (recommended PHP 8.0+)
- MySQL/MariaDB >= 5.7
- Composer
- Web server (Apache/Nginx) atau PHP built-in server untuk development

## 🚀 Installation

### 1. Clone Repository

```bash
git clone <your-repo-url> wedding_apps
cd wedding_apps
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

Copy `.env.example` to `.env` dan isi dengan credentials Anda:

```bash
cp .env.example .env
```

Edit file `.env` untuk Google OAuth (optional, jika ingin menggunakan Google Login):

```env
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

**Cara mendapatkan Google OAuth Credentials:**
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih existing project
3. Enable Google+ API
4. Buat OAuth 2.0 Client ID (Web Application)
5. Tambahkan Authorized Redirect URIs: `http://localhost:8080/auth/callback`
6. Copy Client ID dan Client Secret ke file `.env`

### 4. Configure Database

Edit file `config/db.php`:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=wedding_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
```

### 5. Run Migrations

```bash
php yii migrate
```

Ini akan membuat:

- Table `user` (users dengan role: super_user/client)
  - Fields: id, username, email, password_hash, auth_key, role, google_id, is_active
- Table `invitation` (undangan dengan owner)
  - Fields: id, user_id, title, slug, bride_name, groom_name, bride_nickname, groom_nickname, event_date, event_time, location, is_active
- Table `gallery` (foto gallery)
- Table `guest` (daftar tamu per undangan)
- Table `rsvp` (konfirmasi kehadiran)
- Sample data (super user & contoh undangan)

**Default Super User Credentials:**

- Username: `nearhxh`
- Password: `yasak123`
- Role: `super_user`
- Email: `nearhxh@example.com`
- **⚠️ PENTING: Ganti password dan email setelah first login!**

**Default Client User (Example):**

- Username: `wawanr`
- Email: `wawanriyanto@gmail.com`
- Role: `client`
- Invitation: "Devi Maylinda & Wawan Riyanto"

### 6. Create Upload Directory

```bash
mkdir -p web/uploads/invitations
chmod 777 web/uploads/invitations  # Linux/Mac
```

Windows (PowerShell):

```powershell
New-Item -Path "web\uploads\invitations" -ItemType Directory -Force
```

### 7. Run Application

#### Development Server (Built-in PHP):

```bash
php yii serve
```

Akses: http://localhost:8080

#### XAMPP/WAMP:

Pindahkan folder ke `htdocs/` dan akses via http://localhost/wedding_apps/web

## 📁 Project Structure

```
wedding_apps/
├── assets/                          # Asset bundles
├── commands/                        # Console commands
│   ├── HelloController.php
│   └── UserController.php           # User management CLI
├── config/                          # Application configuration
│   ├── db.php                       # Database config
│   ├── web.php                      # Web + OAuth config
│   └── console.php                  # Console config
├── controllers/                     # Controllers
│   ├── SiteController.php           # Homepage, login, logout
│   ├── AuthController.php           # Google OAuth callback
│   ├── InvitationController.php     # Public invitation view
│   ├── AdminInvitationController.php  # Invitation CRUD (RBAC)
│   ├── AdminUserController.php      # User management (Super User only)
│   ├── AdminGuestController.php     # Guest management (RBAC)
│   ├── AdminGalleryController.php   # Gallery management (RBAC)
│   └── AdminRsvpController.php      # RSVP management (RBAC)
├── migrations/                      # Database migrations
│   ├── m251222_161632_create_user_table.php
│   ├── m251222_161706_create_invitation_table.php
│   ├── m251222_161722_create_gallery_table.php
│   ├── m251222_161739_create_rsvp_table.php
│   ├── m251222_161749_create_guest_table.php
│   ├── m251222_171147_insert_seed_data.php
│   ├── m251224_071652_add_role_to_user_table.php
│   ├── m251224_071828_add_user_id_to_invitation_table.php
│   ├── m251224_095614_add_nickname_to_invitation_table.php
│   ├── m251224_100834_update_title_length_in_invitation_table.php
│   ├── m251225_024655_add_google_id_to_user_table.php
│   └── m251225_033456_add_is_active_to_user_table.php
├── models/                          # Data models
│   ├── User.php                     # User model with RBAC & OAuth
│   ├── Invitation.php               # Invitation model with owner
│   ├── Gallery.php
│   ├── Guest.php
│   ├── Rsvp.php
│   └── LoginForm.php                # Login with dual auth support
├── views/                           # View templates
│   ├── layouts/
│   │   └── main.php                 # Horizontal navbar (role-based)
│   ├── site/
│   ├── auth/
│   │   └── login.php                # Login with Google OAuth button
│   ├── invitation/
│   ├── admin-invitation/            # Invitation CRUD views
│   ├── admin-user/                  # User management views
│   ├── admin-guest/
│   ├── admin-gallery/               # Gallery with lightbox
│   └── admin-rsvp/
├── web/                             # Web root (public)
│   ├── index.php                    # Entry script
│   ├── css/
│   ├── js/
│   └── uploads/                     # Uploaded files
└── tests/                           # Automated tests
```

## 🎯 Usage

### Access Public Invitation

Buka: `http://localhost:8080/invitation/{slug}`

Contoh: `http://localhost:8080/invitation/devi-and-wawan`

Setiap client user memiliki undangan dengan slug unik yang bisa diakses publik.

### Login Options

**1. Login via Username & Password:**
- URL: `http://localhost:8080/site/login`
- Gunakan credentials yang sudah terdaftar
- Support untuk super_user dan client role

**2. Login via Google OAuth:**
- Click tombol "Continue with Google" di halaman login
- Login dengan akun Google Anda
- **⚠️ Requirement**: Email Google harus sudah didaftarkan oleh Super User
- Jika email tidak terdaftar, login akan ditolak

### Admin Dashboard - Super User

Setelah login sebagai super user (`nearhxh`):

1. **Kelola User** - Buat dan kelola user accounts
   - Create client users dengan email Google untuk OAuth
   - Set role: super_user atau client
   - Input nama pasangan dan nickname
   - Toggle status aktif/non-aktif
   - Status tidak berlaku untuk super_user role

2. **Kelola Undangan** - Manage semua undangan
   - Lihat semua undangan dari semua client
   - Assign undangan ke user tertentu
   - Toggle status undangan (sync dengan user status)
   - Edit detail undangan

3. **Kelola Tamu** - Manage semua guest lists
   - Lihat tamu dari semua undangan
   - Export to CSV

4. **Kelola Gallery** - Manage semua foto
   - Upload foto untuk semua undangan
   - Fullscreen lightbox view dengan keyboard navigation

5. **Kelola RSVP** - Lihat semua RSVP responses

### Admin Dashboard - Client User

Setelah login sebagai client user:

1. **Edit Undangan** - Edit detail undangan sendiri
   - Update nama pasangan, tanggal, lokasi
   - Preview undangan publik
   - **Catatan**: Tidak bisa toggle status (diatur oleh super user)

2. **Kelola Tamu** - Manage guest list sendiri
   - Add/edit/delete guests
   - Generate WhatsApp links per tamu
   - Export to CSV

3. **Gallery** - Upload dan kelola foto sendiri
   - Upload foto acara
   - Fullscreen lightbox view

4. **RSVP** - Lihat konfirmasi kehadiran untuk undangan sendiri

### Status Management

**Untuk Super User:**
- Toggle status user/invitation dari dashboard
- Perubahan status user → otomatis sync ke semua undangannya
- Perubahan status invitation → sync ke owner user
- Super user sendiri tidak terpengaruh status (selalu aktif)

**Status Effect:**
- User dengan `is_active = false` tidak bisa login
- Invitation dengan `is_active = false` tidak bisa diakses publik
- Link undangan akan menampilkan halaman error

### Send Invitations via WhatsApp (Manual)

1. Login sebagai admin (super user atau client)
2. Buka **Kelola Tamu**
3. Untuk setiap tamu, system generate link WhatsApp dengan message
4. Click "Copy Link" atau "Open WhatsApp"
5. Paste link di WhatsApp Web atau aplikasi WhatsApp
6. Kirim ke nomor tamu yang bersangkutan

**Catatan**: Pengiriman dilakukan secara manual satu per satu untuk kontrol lebih baik dan menghindari spam. System hanya menyediakan link yang sudah di-format dengan pesan yang tepat.

### Password Reset (CLI)

Jika lupa password, gunakan console command:

```bash
php yii user/reset-password <username>
```

Contoh:
```bash
php yii user/reset-password wawanr
# Enter new password: ********
# Password untuk user 'wawanr' berhasil diubah!
```

## 🧪 Testing

```bash
# Run all tests
vendor/bin/codecept run

# Run specific test suite
vendor/bin/codecept run unit
vendor/bin/codecept run functional
```

## 🎓 Portfolio Context

Project ini dibuat sebagai portfolio showcase untuk mendemonstrasikan:

✅ **Yii2 Framework Mastery**
- MVC architecture
- Active Record ORM
- Migrations & seeding
- Behaviors (Timestamp, Sluggable)
- Authentication & Authorization
- OAuth 2.0 integration (yii2-authclient)
- Console commands
- Transaction-based operations

✅ **Role-Based Access Control (RBAC)**
- Custom role implementation (super_user, client)
- Role-based menu rendering
- Access control filters
- Owner-based data filtering
- Status management with role exemption

✅ **Database Design**
- Proper foreign keys & indexes
- Data integrity & constraints
- Optimized queries
- Bidirectional status synchronization
- Multi-tenant data isolation

✅ **Security Best Practices**
- Password hashing (bcrypt)
- CSRF protection
- Input validation & sanitization
- File upload security
- OAuth 2.0 authentication
- Email whitelist validation
- SQL injection prevention

✅ **Modern UI/UX**
- Bootstrap 5 responsive design
- Bootstrap Icons integration
- Fullscreen lightbox with keyboard navigation
- Horizontal navigation layout
- Status badges with toggle controls
- Google OAuth button styling

✅ **Real-world Application**
- Multi-tenant SaaS architecture
- WhatsApp integration
- Google OAuth 2.0 authentication
- Google Maps embed
- Image gallery with lightbox
- CSV export
- Status management system
- Personalized invitation links

✅ **Code Quality**
- Clean code principles
- Separation of concerns
- DRY (Don't Repeat Yourself)
- Professional git commits
- Comprehensive documentation

---

## 📝 License

This project is open-sourced under the BSD-3-Clause license.

---

**Dibuat dengan ❤️ menggunakan Yii2 Framework + Google OAuth**
