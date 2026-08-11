<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/socialite (SOCIALITE) - v5
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>

# 📋 **PRODUCT REQUIREMENTS DOCUMENT (PRD)**
## Yalia Beauty - Salon Booking System
### Full Laravel Monolith Stack (Blade + Alpine.js + Tailwind CSS)

---

## **1. PROJECT OVERVIEW**

### **1.1 Deskripsi Proyek**
Sistem booking online untuk salon kecantikan **Yalia Beauty** berbasis web yang memungkinkan pelanggan melakukan reservasi treatment, memilih layanan datang ke salon (*Visit Salon*) atau panggilan ke rumah (*Home Service*), mengelola jadwal reservasi dengan *Booking Wizard*, serta pembayaran melalui **Midtrans Payment Gateway (Sandbox)** atau QRIS/Manual. Aplikasi ini dibangun dengan arsitektur **Laravel Monolith Stack** menggunakan Blade template engine, Alpine.js untuk interaktivitas frontend, dan Tailwind CSS dengan desain **Modern Maximalist Luxury** (Google Stitch).

### **1.2 Tujuan Utama**
- Memudahkan pelanggan melakukan booking treatment salon secara online dengan ketersediaan slot waktu *real-time*.
- Mencegah *double booking* pada beautician, tanggal, dan jam layanan menggunakan *race condition prevention* & *custom JavaScript time picker*.
- Menyediakan integrasi pembayaran cepat menggunakan **Midtrans Sandbox (Snap API)** & opsi manual (QRIS/Transfer).
- Menyajikan antarmuka mewah berbasis standar Google Stitch (Modern Maximalist Luxury: `#f45472`, `#ff8fa4`, `#5b3a29`, Playfair Display & Work Sans).
- Menyediakan sistem membership & reward berbasis poin pengeluaran pelanggan.

---

## **2. TECH STACK & ACTUAL PROJECT ARCHITECTURE**

### **2.1 Technology Stack**
```yaml
Backend Framework: Laravel 11.x / 13.x (PHP 8.3+)
Frontend Architecture: Blade Templates + Alpine.js 3.x + Tailwind CSS 3.x/4.x (Vite)
Design System: Modern Maximalist Luxury (Google Stitch MCP)
Authentication: Laravel Breeze (Standard Auth & Google Socialite OAuth2)
Payment Gateway: Midtrans PHP SDK (v2.6) - Sandbox Mode
Database: MySQL / MariaDB
Utilities: 
  - intervention/image (3.0)
  - simplesoftwareio/simple-qrcode (4.2)
  - barryvdh/laravel-dompdf (3.1)
  - maatwebsite/excel (3.1)
  - laravel/socialite (5.29)
```

### **2.2 Actual Directory Structure**
```
salon-yalia-beauty/
├── app/
│   ├── Enums/
│   │   ├── BookingStatus.php
│   │   └── PaymentStatus.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── SlotController.php          # Endpoint AJAX slot ketersediaan jam
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── GoogleAuthController.php
│   │   │   │   └── SocialiteController.php
│   │   │   ├── user/
│   │   │   │   ├── BookingController.php        # List, create, store, cancel booking
│   │   │   │   ├── DashboardController.php      # User dashboard & membership stats
│   │   │   │   └── TreatmentController.php      # Treatments listing & filtering
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   │       └── SearchTreatmentRequest.php
│   ├── Models/                                  # Catatan: Nama Model Plural di Codebase
│   │   ├── User.php
│   │   ├── Bookings.php                         # Relasi ke User, Beauticians, BookingTreatments
│   │   ├── Treatments.php                       # Attributes, rating_avg, duration, price
│   │   ├── Categories.php                       # Treatment Categories
│   │   ├── Beauticians.php                      # Staff Beauticians
│   │   ├── BeauticiansSchedules.php             # Beautician Work Schedule
│   │   ├── BookingTreatments.php                # Pivot data treatment yang dibooking
│   │   ├── Transactions.php                     # Income/Expense Transactions
│   │   ├── ExpenseCategories.php                # Kategori Pengeluaran
│   │   ├── Reviews.php                          # Customer Reviews & Rating
│   │   ├── Vouchers.php                         # Promo & Discount Vouchers
│   │   ├── UserVouchers.php                     # Voucher per user
│   │   └── Notifications.php                    # System notifications
│   └── Services/
│       └── TreatmentQueryService.php            # LengthAware Pagination & Query caching
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── navigation.blade.php
│   │   │   └── footer.blade.php
│   │   ├── user/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── bookings/
│   │   │   │   ├── BookingList.blade.php
│   │   │   │   ├── create.blade.php             # Booking Wizard (Stitch UI + Custom Time Picker)
│   │   │   │   └── show.blade.php
│   │   │   ├── treatments/
│   │   │   │   └── index.blade.php              # Treatments Grid + Filter + Pagination
│   │   │   └── profile/
│   │   │       └── edit.blade.php
│   │   └── auth/
│   │       ├── login.blade.php
│   │       └── register.blade.php
├── routes/
│   ├── web.php                                  # Authenticated & User Dashboard Routes
│   └── auth.php                                 # Breeze & Socialite Routes
```

---

## **3. DATABASE SCHEMAS & MODELS**

### **3.1 Model Naming Convention (Pluralized)**
Model di codebase ini menggunakan konvensi penamaan jamak (*plural*):
- `User` (`users`)
- `Bookings` (`bookings`)
- `Treatments` (`treatments`)
- `Categories` (`categories`)
- `Beauticians` (`beauticians`)
- `BeauticiansSchedules` (`beautician_schedules`)
- `BookingTreatments` (`booking_treatments`)
- `Transactions` (`transactions`)
- `ExpenseCategories` (`expense_categories`)
- `Reviews` (`reviews`)
- `Vouchers` (`vouchers`)
- `UserVouchers` (`user_vouchers`)
- `Notifications` (`notifications`)

### **3.2 Database Constraints & Race Condition Prevention**
1. **Unique Constraint**: Tabel `bookings` memiliki constraint `unique_booking_slot` pada tuple `['beautician_id', 'booking_date', 'time_start']` untuk mencegah *double booking* di tingkat database.
2. **Pessimistic Locking / DB Transaction**: Proses pembuatan booking dibungkus dalam DB Transaction.
3. **AJAX Slot Check**: Endpoint `/dashboard/slots/check?date=YYYY-MM-DD` mengembalikan daftar slot yang sudah terisi sehingga antarmuka *time-picker* menyembunyikan/memudarkan opsi jam yang bentrok secara otomatis berdasarkan durasi *treatment*.

---

## **4. CORE FEATURES & IMPLEMENTATION DETAILS**

### **4.1 Treatment Browsing & Filtering**
- **Paginated Listing**: Menggunakan `LengthAwarePaginator` di `TreatmentQueryService.php` untuk menampilkan nomor halaman link secara penuh (`1, 2, 3...`).
- **Rating Display**: Jika `rating_avg` pada treatment bernilai `0.0` (belum ada ulasan), antarmuka secara otomatis menampilkan *badge* berlabel **"New"** daripada mencetak `0.0`.
- **Primary CTA**: Tombol **"BOOKING"** berwarna Yalia Pink (`#f45472`) pada setiap kartu *treatment* untuk langsung mengarahkan pelanggan ke Booking Wizard.

### **4.2 Booking Wizard (Google Stitch UI + Custom JS Picker)**
- **Visual Design**: Desain "Modern Maximalist Luxury" dengan kartu membulat (28px radius), efek *soft-luxury shadow*, background blob organik, dan tipografi Playfair Display & Work Sans.
- **Custom Date Picker**: Elemen *scrollable* horizontal yang menampilkan daftar tanggal 14 hari ke depan. Tidak menggunakan `<input type="date">`.
- **Custom Time Picker**: Elemen grid interval 30 menit (pukul 09:00 - 17:00). Opsi jam yang bentrok dengan durasi booking yang sudah ada atau jam yang telah berlalu pada hari yang sama akan berwarna abu-abu (*disabled/faded*). Tidak menggunakan `<input type="time">`.
- **Overlapping Logic**: Panggilan AJAX ke `route('user.slots.check')` setiap kali tanggal dipilih. JS menghitung rentang `time_start` dan `time_end` (`time_start + duration_minutes`) untuk memastikan ketersediaan waktu secara presisi.

### **4.3 Payment Gateway (Midtrans Sandbox & Midtrans Snap)**
- **Integrasi Midtrans Sandbox**: Menggunakan SDK `midtrans/midtrans-php` (v2.6) pada lingkungan *sandbox/testing*.
- **Midtrans Snap Token**: Setelah booking berhasil dibuat, server meng-generate Snap Token Midtrans dan menampilkan modal pembayaran Snap.
- **Callback & Status Update**: Mendukung update status pembayaran otomatis (*paid*, *pending*, *expire*, *cancel*) melalui webhook callback atau verifikasi status transaksi Midtrans Sandbox.
- **Opsi Pembayaran Manual**: Tetap menyediakan QRIS manual atau bukti transfer sebagai opsi cadangan/alternatif.

---

## **5. ROUTES & ENDPOINTS SUMMARY**

```php
// Authentication & OAuth Routes (routes/auth.php)
GET  /login                     -> AuthenticatedSessionController@showLogin
POST /login                     -> AuthenticatedSessionController@login
GET  /register                  -> AuthenticatedSessionController@showRegister
POST /register                  -> AuthenticatedSessionController@register
GET  /auth/google               -> SocialiteController@redirectToGoogle
GET  /auth/google/callback      -> SocialiteController@handleGoogleCallback

// User Dashboard & Booking Routes (routes/web.php)
Route::middleware(['auth'])->prefix('dashboard')->name('user.')->group(function () {
    GET  /                      -> DashboardController@index          # Dashboard & Stats
    GET  /bookings/list         -> BookingController@list             # Dynamic AJAX tab list
    GET  /treatments            -> TreatmentController@index          # Treatments listing
    GET  /slots/check           -> Api\SlotController@check           # AJAX Check booked slots
    GET  /booking               -> BookingController@index            # Booking History
    GET  /booking/buat          -> BookingController@create           # Booking Wizard
    POST /booking               -> BookingController@store            # Process Booking & Midtrans Token
    GET  /booking/{booking}     -> BookingController@show             # Booking Detail
    PATCH /booking/{booking}/batalkan -> BookingController@cancel      # Cancel Booking
});

// Profile Routes (routes/web.php)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    GET    /                    -> ProfileController@edit
    PATCH  /                    -> ProfileController@update
    DELETE /                    -> ProfileController@destroy
});
```

---

## **6. DESIGN SYSTEM TOKENS (Google Stitch MCP)**

```css
Primary Color: #f45472 (Yalia Pink - CTA, Active State)
Secondary Color: #ff8fa4 (Soft Pink - Hover, Highlights)
Tertiary Color: #5b3a29 (Luxury Deep Brown - Text, Hairlines, Shadows)
Background Main: #fdf5f6
Surface Light: #fff8f9
Typography: 
  - Headlines: 'Playfair Display', serif
  - Body & Labels: 'Work Sans', sans-serif
Border Radius: rounded-2xl (16px), rounded-3xl (24px), rounded-full (Pill buttons)
```

---

## **7. DEVELOPMENT VERIFICATION & BUILD**

```bash
# Jalankan kompilasi produksi
npm run build

# Atau jalankan dev server
npm run dev

# Bersihkan cache view Laravel jika Blade tidak ter-update
php artisan view:clear
```
