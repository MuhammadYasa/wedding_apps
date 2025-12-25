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
```Environment

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
1. B5. Run Migrations

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
- Ro6. Create Upload Directory

```bash
mkdir -p web/uploads/invitations
chmod 777 web/uploads/invitations  # Linux/Mac
```

Windows (PowerShell):

```powershell
New-Item -Path "web\uploads\invitations" -ItemType Directory -Force
```

### 7. Run Migrations

```bash
php yii migrate
```

Ini akan membuat:

- Table `user` (admin users)
- Table `invitation` (undangan)
- Table `gallery` (foto gallery)
- Table `guest` (daftar tamu)
- Table `rsvp` (konfirmasi kehadiran)
- Sample data (admin user & contoh undangan)

**Default Admin Credentials:**

- Username: `nearhxh`
- Password: `yasak123`
- **⚠️ PENTING: Ganti password setelah first login!**

### 5. Create Upload Directory

```bash
mkdir -p web/uploads/invitations
chmod 777 web/uploads/invitations  # Linux/Mac
```

Windows (PowerShell):

```powershell
New-Item -Path "web\uploads\invitations" -ItemType Directory -Force
```

### 6. Run Application

#### Development Server (Built-in PHP):

```bash
php yii serve
```

Akses: http://localhost:8080
│   ├── HelloController.php
│   └── UserController.php       # User management CLI
├── config/              # Application configuration
│   ├── db.php                   # Database config
│   ├── web.php                  # Web + OAuth config
│   └── console.php              # Console config
├── controllers/         # Controllers
│   ├── SiteController.php       # Homepage, login, logout
│   ├── AuthController.php       # Google OAuth callback
│   ├── InvitationController.php # Public invitation view
│   ├── AdminInvitationController.php  # Invitation CRUD (RBAC)
│   ├── AdminUserController.php        # User management (Super User only)
│   ├── AdminGuestController.php       # Guest management (RBAC)
│   ├── AdminGalleryController.php     # Gallery management (RBAC)
│   └── AdminRsvpController.php        # RSVP management (RBAC)
├── migrations/          # Database migrations
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
├── models/              # Data models
│   ├── User.php                 # User model with RBAC & OAuth
│   ├── Invitation.php           # Invitation model with owner
│   ├── Gallery.php
│   ├── Guest.php
│   ├── Rsvp.php
│   └── LoginForm.php            # Login with dual auth support
├── views/               # View templatesdevi-and-wawan`

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

**Dibuat dengan ❤️ menggunakan Yii2 Framework + Google OAuthr

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
├── views/               # View templates
│   ├── layouts/
│   ├── site/
│   ├── invitation/
│   └── admin-guest/
├── web/                 # Web root (public)
│   ├── index.php       # Entry script
│   ├── css/
│   ├── js/
│   └── uploads/        # Uploaded files
└── tests/              # Automated tests
```

## 🎯 Usage

### Access Public Invitation

Buka: `http://localhost:8080/invitation/{slug}`
Contoh: `http://localhost:8080/invitation/yasa-and-devi`

### Access Admin Dashboard

1. Login: `http://localhost:8080/site/login`
2. Gunakan credentials admin default
3. Manage invitations, guests, RSVP, gallery

### Send Invitations via WhatsApp (Manual)

1. Login sebagai admin
2. Buka Guest Management
3. Untuk setiap tamu, system generate link WhatsApp dengan message
4. Click "Copy Link" atau "Open WhatsApp"
5. Paste link di WhatsApp Web atau aplikasi WhatsApp
6. Kirim ke nomor tamu yang bersangkutan

**Catatan**: Pengiriman dilakukan secara manual satu per satu untuk kontrol lebih baik dan menghindari spam. System hanya menyediakan link yang sudah di-format dengan pesan yang tepat.

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

✅ **Database Design**

- Proper foreign keys & indexes
- Data integrity & constraints
- Optimized queries

✅ **Security Best Practices**

- Password hashing
- CSRF protection
- Input validation
- File upload security

✅ **Real-world Application**

- WhatsApp integration
- Google Maps embed
- Image gallery
- CSV export

---

**Dibuat dengan ❤️ menggunakan Yii2 Framework**

      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources

## REQUIREMENTS

The minimum requirement by this project template that your Web server supports PHP 7.4.

## INSTALLATION

### Install via Composer

If you do not have [Composer](https://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

```
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
```

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

```
http://localhost/basic/web/
```

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](https://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

```
http://localhost/basic/web/
```

### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist

Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install

Start the container

    docker-compose up -d

You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:**

- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches

## CONFIGURATION

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**

- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.

## TESTING

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](https://codeception.com/).
By default, there are 3 test suites:

- `unit`
- `functional`
- `acceptance`

Tests can be executed by running

```
vendor/bin/codecept run
```

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser.

### Running acceptance tests

To execute acceptance tests do the following:

1. Rename `tests/acceptance.suite.yml.example` to `tests/acceptance.suite.yml` to enable suite configuration

2. Replace `codeception/base` package in `composer.json` with `codeception/codeception` to install full-featured
   version of Codeception

3. Update dependencies with Composer

   ```
   composer update
   ```

4. Download [Selenium Server](https://www.seleniumhq.org/download/) and launch it:

   ```
   java -jar ~/selenium-server-standalone-x.xx.x.jar
   ```

   In case of using Selenium Server 3.0 with Firefox browser since v48 or Google Chrome since v53 you must download [GeckoDriver](https://github.com/mozilla/geckodriver/releases) or [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) and launch Selenium with it:

   ```
   # for Firefox
   java -jar -Dwebdriver.gecko.driver=~/geckodriver ~/selenium-server-standalone-3.xx.x.jar

   # for Google Chrome
   java -jar -Dwebdriver.chrome.driver=~/chromedriver ~/selenium-server-standalone-3.xx.x.jar
   ```

   As an alternative way you can use already configured Docker container with older versions of Selenium and Firefox:

   ```
   docker run --net=host selenium/standalone-firefox:2.53.0
   ```

5. (Optional) Create `yii2basic_test` database and update it by applying migrations if you have them.

   ```
   tests/bin/yii migrate
   ```

   The database configuration can be found at `config/test_db.php`.

6. Start web server:

   ```
   tests/bin/yii serve
   ```

7. Now you can run all available tests

   ```
   # run all available tests
   vendor/bin/codecept run

   # run acceptance tests
   vendor/bin/codecept run acceptance

   # run only unit and functional tests
   vendor/bin/codecept run unit,functional
   ```

### Code coverage support

By default, code coverage is disabled in `codeception.yml` configuration file, you should uncomment needed rows to be able
to collect code coverage. You can run your tests and collect coverage with the following command:

```
#collect coverage for all tests
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

#collect coverage only for unit tests
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml

#collect coverage for unit and functional tests
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml
```

You can see code coverage output under the `tests/_output` directory.
