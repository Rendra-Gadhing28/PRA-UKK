<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

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
### Full Laravel Stack (Blade + Alpine.js)

---

## **1. PROJECT OVERVIEW**

### **1.1 Deskripsi Proyek**
Sistem booking online untuk salon kecantikan Yalia Beauty berbasis web yang memungkinkan pelanggan melakukan reservasi treatment, memilih datang ke salon atau home service, serta menyediakan dashboard admin untuk manajemen booking, keuangan, dan operasional salon. Dibangun menggunakan **Full Laravel Stack (Monolith)** dengan Blade template engine dan Alpine.js untuk interaktivitas frontend.

### **1.2 Tujuan**
- Memudahkan pelanggan melakukan booking treatment salon secara online
- Mengelola jadwal beautician dan mencegah double booking
- Mencatat pemasukan dan pengeluaran salon secara terstruktur
- Memberikan notifikasi toast real-time untuk setiap aksi user
- Meningkatkan customer experience dengan sistem membership dan reward

### **1.3 Target User**
- **Customer:** Wanita usia 18-50 tahun yang membutuhkan jasa perawatan kecantikan
- **Admin/Owner:** Pemilik salon yang mengelola operasional
- **Beautician:** Staff salon yang memberikan layanan treatment

### **1.4 Development Constraints (UKK)**
- **Waktu Pengerjaan:** 2 minggu
- **Server:** Hosting sederhana (shared hosting/VPS kecil)
- **Teknologi:** Full Laravel (no SPA framework)
- **Pembayaran:** QRIS manual (tidak perlu webhook/WebSocket)
- **Notifikasi:** Toast notification via session flash + Alpine.js

---

## **2. TECH STACK & ARCHITECTURE**

### **2.1 Technology Stack**
```yaml
Backend:
  Framework: Laravel 11.x
  Language: PHP 8.2+
  Architecture: MVC (Monolith)
  Authentication: Laravel Breeze (Blade + Alpine)
  API: Optional REST endpoints (untuk AJAX/Alpine)

Frontend:
  Template Engine: Blade
  CSS Framework: Tailwind CSS 3.x (via Vite)
  JavaScript: Alpine.js 3.x (CDN)
  Icons: Lucide Icons (CDN)
  Build Tool: Vite (built-in Laravel)

Database & Cache:
  Database: MySQL 8.0 / MariaDB 10.6+
  Cache Driver: File / Database
  Queue Driver: Database (sync for dev)

Package yang Digunakan:
  - laravel/breeze (Authentication scaffolding)
  - simplesoftwareio/simple-qrcode (QRIS generator)
  - barryvdh/laravel-dompdf (PDF export)
  - maatwebsite/excel (Excel export)
  - intervention/image (Image optimization - optional)

Development Tools:
  Local Server: Laravel Sail (Docker) / Laragon / XAMPP
  Package Manager: Composer + NPM
  Code Editor: VS Code
  Database GUI: TablePlus / phpMyAdmin
  Testing: PHPUnit / Pest PHP
  Version Control: Git + GitHub

TIDAK DIPAKAI (Kesederhanaan UKK):
  ❌ React / Vue.js / Inertia.js (SPA framework)
  ❌ Redis (cache driver)
  ❌ WebSocket / Pusher / Laravel Reverb
  ❌ Payment Gateway Webhook (Midtrans/Xendit)
  ❌ Queue Jobs (async processing)
  ❌ Docker (optional, bisa pakai Laragon)
```

### **2.2 Architecture Pattern**
```
┌─────────────────────────────────────────────────────────┐
│                    BROWSER (Client)                      │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Blade Templates + Alpine.js          │  │
│  │  ┌─────────┐ ┌──────────┐ ┌──────────────────┐  │  │
│  │  │  Blade  │ │ Alpine.js│ │  Tailwind CSS    │  │  │
│  │  │  Views  │ │ (State)  │ │  (Styling)       │  │  │
│  │  └─────────┘ └──────────┘ └──────────────────┘  │  │
│  │  ┌─────────┐ ┌──────────┐ ┌──────────────────┐  │  │
│  │  │Lucide   │ │  Toast   │ │  Axios/Fetch    │  │  │
│  │  │Icons    │ │Component │ │  (AJAX)          │  │  │
│  │  └─────────┘ └──────────┘ └──────────────────┘  │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                           │
                    HTTP Request/Response
                    (Full Page Reload + AJAX)
                           │
┌─────────────────────────────────────────────────────────┐
│                    LARAVEL BACKEND                        │
│  ┌───────────────────────────────────────────────────┐  │
│  │              HTTP Layer (web.php routes)          │  │
│  │  ┌────────────────┐  ┌──────────────────────┐    │  │
│  │  │  Controllers   │  │  Form Requests       │    │  │
│  │  │  (CRUD Logic)  │  │  (Validation)        │    │  │
│  │  └────────────────┘  └──────────────────────┘    │  │
│  └───────────────────────────────────────────────────┘  │
│                           │                               │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Business Logic Layer                 │  │
│  │  ┌────────────────┐  ┌──────────────────────┐    │  │
│  │  │   Services     │  │     Helpers          │    │  │
│  │  │   (Booking,    │  │     (Toast,          │    │  │
│  │  │    Payment,    │  │      Date, etc)      │    │  │
│  │  │    Finance)    │  │                      │    │  │
│  │  └────────────────┘  └──────────────────────┘    │  │
│  └───────────────────────────────────────────────────┘  │
│                           │                               │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Data Layer                            │  │
│  │  ┌────────────────┐  ┌──────────────────────┐    │  │
│  │  │  Eloquent      │  │   Cache (File/DB)    │    │  │
│  │  │  Models        │  │   (Performance)      │    │  │
│  │  └────────────────┘  └──────────────────────┘    │  │
│  └───────────────────────────────────────────────────┘  │
│                           │                               │
│                    MySQL 8.0 Database                     │
│                    File/Database Cache                    │
└─────────────────────────────────────────────────────────┘
```

### **2.3 Project Structure**
```
yalia-beauty/
├── app/
│   ├── Enums/
│   │   ├── BookingStatus.php
│   │   ├── PaymentMethod.php
│   │   ├── PaymentStatus.php
│   │   ├── BookingType.php
│   │   └── MembershipLevel.php
│   ├── Helpers/
│   │   ├── ToastHelper.php
│   │   └── DateHelper.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/              # Breeze auth controllers
│   │   │   ├── User/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── TreatmentController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   └── ProfileController.php
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── AdminBookingController.php
│   │   │   │   ├── AdminTreatmentController.php
│   │   │   │   ├── AdminBeauticianController.php
│   │   │   │   ├── AdminFinanceController.php
│   │   │   │   ├── AdminVoucherController.php
│   │   │   │   └── AdminUserController.php
│   │   │   └── Api/
│   │   │       ├── SlotController.php
│   │   │       └── BookingController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/
│   │       ├── CreateBookingRequest.php
│   │       ├── StoreExpenseRequest.php
│   │       └── UpdateProfileRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Booking.php
│   │   ├── Treatment.php
│   │   ├── Category.php
│   │   ├── Beautician.php
│   │   ├── BeauticianSchedule.php
│   │   ├── Transaction.php
│   │   ├── ExpenseCategory.php
│   │   ├── Voucher.php
│   │   ├── UserVoucher.php
│   │   └── Review.php
│   ├── Services/
│   │   ├── BookingService.php
│   │   ├── QrisService.php
│   │   └── CacheService.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── auth.blade.php
│   │   ├── components/
│   │   │   ├── navbar.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   ├── footer.blade.php
│   │   │   ├── toast.blade.php
│   │   │   └── modals/
│   │   │       ├── qris-modal.blade.php
│   │   │       └── confirm-modal.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── reset-password.blade.php
│   │   ├── user/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── treatments/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── bookings/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   └── show.blade.php
│   │   │   └── profile/
│   │   │       └── edit.blade.php
│   │   └── admin/
│   │       ├── dashboard.blade.php
│   │       ├── bookings/
│   │       │   ├── index.blade.php
│   │       │   └── show.blade.php
│   │       ├── treatments/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── beauticians/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       ├── finances/
│   │       │   ├── index.blade.php
│   │       │   ├── expenses.blade.php
│   │       │   ├── expense-form.blade.php
│   │       │   ├── reports.blade.php
│   │       │   └── pdf-report.blade.php
│   │       ├── vouchers/
│   │       │   ├── index.blade.php
│   │       │   └── form.blade.php
│   │       └── users/
│   │           └── index.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── app.js
│       ├── toast.js
│       └── bootstrap.js
│
├── public/
│   └── storage/         # Symlink untuk uploaded files
│
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── routes/
│   ├── web.php           # Main routes
│   ├── auth.php          # Breeze auth routes
│   └── api.php           # Optional API routes
│
├── config/
│   └── cache.php         # Cache configuration
│
├── storage/
│   └── app/
│       └── public/
│           ├── treatments/    # Treatment images
│           ├── beauticians/   # Beautician photos
│           ├── receipts/      # Expense receipts
│           ├── qris/          # Generated QR codes
│           └── payment-proofs/ # User payment proof uploads
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── .env
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## **3. DATABASE DESIGN**

### **3.1 Entity Relationship Diagram (ERD)**

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : "membuat"
    USERS ||--o{ REVIEWS : "menulis"
    USERS ||--o{ USER_VOUCHERS : "memiliki"
    USERS {
        bigint id PK
        string name
        string email UK
        string phone UK
        timestamp email_verified_at
        string password
        string avatar nullable
        text address nullable
        decimal latitude nullable
        decimal longitude nullable
        enum membership_level
        int total_points default 0
        int total_bookings default 0
        decimal total_spending default 0
        boolean is_active default true
        boolean is_admin default false
        remember_token
        timestamps
    }
    
    CATEGORIES ||--o{ TREATMENTS : "mengelompokkan"
    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        string icon nullable
        text description nullable
        boolean is_active default true
        int sort_order default 0
        timestamps
    }
    
    TREATMENTS ||--o{ BOOKING_TREATMENTS : "dibooking"
    TREATMENTS {
        bigint id PK
        bigint category_id FK
        string name
        string slug UK
        text description
        text benefits nullable
        decimal price
        int duration_minutes
        string image nullable
        enum badge nullable
        boolean is_active default true
        int sort_order default 0
        decimal rating_avg default 0
        int rating_count default 0
        timestamps
    }
    
    BEAUTICIANS ||--o{ BOOKINGS : "menangani"
    BEAUTICIANS ||--o{ BEAUTICIAN_SCHEDULES : "memiliki"
    BEAUTICIANS {
        bigint id PK
        string name
        string phone
        string email nullable
        string photo nullable
        text bio nullable
        json specialties nullable
        decimal rating_avg default 0
        int total_bookings default 0
        json service_area nullable
        boolean is_active default true
        timestamps
    }
    
    BEAUTICIAN_SCHEDULES {
        bigint id PK
        bigint beautician_id FK
        tinyint day_of_week
        time start_time
        time end_time
        boolean is_working default true
        timestamps
    }
    
    BOOKINGS ||--|| TRANSACTIONS : "menghasilkan"
    BOOKINGS ||--o{ BOOKING_TREATMENTS : "berisi"
    BOOKINGS ||--o{ REVIEWS : "direview"
    BOOKINGS {
        bigint id PK
        string booking_code UK
        bigint user_id FK
        bigint beautician_id FK
        enum booking_type
        enum status
        date booking_date
        time time_start
        time time_end
        decimal subtotal
        decimal discount_amount default 0
        decimal transport_fee default 0
        decimal total_amount
        string payment_method
        enum payment_status default 'unpaid'
        string qris_code nullable
        string qris_image_url nullable
        string payment_proof nullable
        timestamp payment_verified_at nullable
        bigint payment_verified_by nullable
        text notes nullable
        text cancel_reason nullable
        timestamp canceled_at nullable
        integer version default 1
        timestamps
    }
    
    BOOKING_TREATMENTS {
        bigint id PK
        bigint booking_id FK
        bigint treatment_id FK
        int quantity default 1
        decimal price_per_unit
        decimal subtotal
        timestamps
    }
    
    TRANSACTIONS {
        bigint id PK
        enum type
        bigint booking_id FK nullable
        string category
        string icon nullable
        string title
        text description nullable
        decimal amount
        string receipt_image nullable
        date transaction_date
        json metadata nullable
        bigint created_by FK nullable
        timestamps
    }
    
    EXPENSE_CATEGORIES {
        bigint id PK
        string name
        string icon
        text description nullable
        boolean is_active default true
        timestamps
    }
    
    REVIEWS {
        bigint id PK
        bigint booking_id FK
        bigint user_id FK
        bigint beautician_id FK nullable
        int rating
        text comment nullable
        string photo nullable
        boolean is_approved default false
        text admin_reply nullable
        timestamps
    }
    
    VOUCHERS ||--o{ USER_VOUCHERS : "digunakan"
    VOUCHERS {
        bigint id PK
        string code UK
        string name
        text description nullable
        enum type
        decimal value
        decimal min_purchase default 0
        decimal max_discount nullable
        date valid_from
        date valid_until
        int quota
        int used_count default 0
        boolean is_active default true
        timestamps
    }
    
    USER_VOUCHERS {
        bigint id PK
        bigint user_id FK
        bigint voucher_id FK
        bigint booking_id FK nullable
        boolean is_used default false
        timestamp used_at nullable
        timestamps
    }
```

### **3.2 Complete Migration Schema**

```php
// USERS
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone', 20)->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->text('address')->nullable();
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->enum('membership_level', ['regular', 'silver', 'gold', 'platinum'])->default('regular');
    $table->integer('total_points')->default(0);
    $table->integer('total_bookings')->default(0);
    $table->decimal('total_spending', 15, 2)->default(0);
    $table->boolean('is_active')->default(true);
    $table->boolean('is_admin')->default(false);
    $table->rememberToken();
    $table->timestamps();
});

// CATEGORIES
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('icon')->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// TREATMENTS
Schema::create('treatments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description');
    $table->text('benefits')->nullable();
    $table->decimal('price', 15, 2);
    $table->integer('duration_minutes');
    $table->string('image')->nullable();
    $table->enum('badge', ['none', 'best_seller', 'new', 'promo'])->default('none');
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->decimal('rating_avg', 3, 2)->default(0);
    $table->integer('rating_count')->default(0);
    $table->timestamps();
});

// BEAUTICIANS
Schema::create('beauticians', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone', 20);
    $table->string('email')->nullable();
    $table->string('photo')->nullable();
    $table->text('bio')->nullable();
    $table->json('specialties')->nullable();
    $table->decimal('rating_avg', 3, 2)->default(0);
    $table->integer('total_bookings')->default(0);
    $table->json('service_area')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// BEAUTICIAN_SCHEDULES
Schema::create('beautician_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('beautician_id')->constrained()->onDelete('cascade');
    $table->tinyInteger('day_of_week'); // 0=Sunday, 6=Saturday
    $table->time('start_time');
    $table->time('end_time');
    $table->boolean('is_working')->default(true);
    $table->timestamps();
});

// BOOKINGS (DENGAN UNIQUE CONSTRAINT)
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->string('booking_code', 20)->unique();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('beautician_id')->nullable()->constrained()->onDelete('set null');
    $table->enum('booking_type', ['salon', 'home']);
    $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'canceled'])->default('pending');
    $table->date('booking_date');
    $table->time('time_start');
    $table->time('time_end');
    $table->decimal('subtotal', 15, 2);
    $table->decimal('discount_amount', 15, 2)->default(0);
    $table->decimal('transport_fee', 15, 2)->default(0);
    $table->decimal('total_amount', 15, 2);
    $table->string('payment_method', 50); // cash, qris, transfer
    $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'refunded'])->default('unpaid');
    $table->string('qris_code')->nullable();
    $table->string('qris_image_url')->nullable();
    $table->string('payment_proof')->nullable();
    $table->timestamp('payment_verified_at')->nullable();
    $table->foreignId('payment_verified_by')->nullable()->constrained('users')->onDelete('set null');
    $table->text('notes')->nullable();
    $table->text('cancel_reason')->nullable();
    $table->timestamp('canceled_at')->nullable();
    $table->integer('version')->default(1);
    $table->timestamps();
    
    // UNIQUE CONSTRAINT untuk cegah double booking di database level
    $table->unique(
        ['beautician_id', 'booking_date', 'time_start'],
        'unique_booking_slot'
    );
    
    // Index untuk query performance
    $table->index(['booking_date', 'status']);
    $table->index('payment_status');
});

// BOOKING_TREATMENTS
Schema::create('booking_treatments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');
    $table->foreignId('treatment_id')->constrained()->onDelete('restrict');
    $table->integer('quantity')->default(1);
    $table->decimal('price_per_unit', 15, 2);
    $table->decimal('subtotal', 15, 2);
    $table->timestamps();
});

// TRANSACTIONS
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->enum('type', ['income', 'expense']);
    $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
    $table->string('category', 100);
    $table->string('icon', 10)->nullable();
    $table->string('title');
    $table->text('description')->nullable();
    $table->decimal('amount', 15, 2);
    $table->string('receipt_image')->nullable();
    $table->date('transaction_date');
    $table->json('metadata')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index(['type', 'transaction_date']);
});

// EXPENSE_CATEGORIES
Schema::create('expense_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('icon', 10);
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// REVIEWS
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('beautician_id')->nullable()->constrained()->onDelete('set null');
    $table->integer('rating');
    $table->text('comment')->nullable();
    $table->string('photo')->nullable();
    $table->boolean('is_approved')->default(false);
    $table->text('admin_reply')->nullable();
    $table->timestamps();
    
    $table->unique('booking_id'); // One review per booking
});

// VOUCHERS
Schema::create('vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('code', 50)->unique();
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('type', ['percentage', 'fixed']);
    $table->decimal('value', 15, 2);
    $table->decimal('min_purchase', 15, 2)->default(0);
    $table->decimal('max_discount', 15, 2)->nullable();
    $table->date('valid_from');
    $table->date('valid_until');
    $table->integer('quota');
    $table->integer('used_count')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// USER_VOUCHERS
Schema::create('user_vouchers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
    $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
    $table->boolean('is_used')->default(false);
    $table->timestamp('used_at')->nullable();
    $table->timestamps();
});

// CACHE TABLE (Optional, jika pakai database cache)
Schema::create('cache', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->mediumText('value');
    $table->integer('expiration');
});

Schema::create('cache_locks', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->string('owner');
    $table->integer('expiration');
});
```

---

## **4. CORE FEATURES & FUNCTIONAL REQUIREMENTS**

### **4.1 Authentication System**
```yaml
FR-1.1: Registrasi dengan email + nomor HP
FR-1.2: Verifikasi email (built-in Breeze)
FR-1.3: Login dengan email/phone + password
FR-1.4: Remember me functionality
FR-1.5: Forgot & reset password
FR-1.6: Role-based access (User vs Admin)
FR-1.7: Middleware admin untuk protect admin routes
FR-1.8: Profile management (update nama, avatar, alamat, password)
```

### **4.2 Treatment Browsing**
```yaml
FR-2.1: Landing page menampilkan treatment populer
FR-2.2: Halaman daftar treatment dengan filter kategori
FR-2.3: Search treatment by name
FR-2.4: Detail treatment (deskripsi, harga, durasi, rating)
FR-2.5: Badge visual (Best Seller, New, Promo)
FR-2.6: Cache treatment list untuk performa (1 jam)
```

### **4.3 Booking System (Core Feature)**
```yaml
FR-3.1: Form booking 3-step wizard:
       Step 1: Pilih treatment
       Step 2: Pilih tipe kunjungan, beautician, tanggal, jam
       Step 3: Pilih pembayaran & konfirmasi
FR-3.2: Booking type: "Ke Salon" atau "Home Service"
FR-3.3: Home service tambah ongkir berdasarkan jarak
FR-3.4: Pilih beautician atau auto-assign
FR-3.5: Slot time 30 menit interval
FR-3.6: Real-time slot checking via AJAX
FR-3.7: Double booking prevention:
       - Cache Lock (file/database)
       - Database Transaction + Pessimistic Locking
       - Unique constraint di database
FR-3.8: Generate booking code: YLB-YYYYMMDD-XXXX
FR-3.9: Booking confirmation notification (toast)
FR-3.10: Booking history di user dashboard
FR-3.11: Booking detail dengan status tracking
FR-3.12: Cancel & reschedule booking
```

### **4.4 Payment System (QRIS Manual)**
```yaml
FR-4.1: Metode pembayaran: Cash, QRIS, Transfer Bank
FR-4.2: QRIS: Generate QR code dengan nominal booking
FR-4.3: QR code ditampilkan setelah booking dibuat
FR-4.4: User upload bukti transfer (untuk transfer bank)
FR-4.5: Admin verifikasi pembayaran manual
FR-4.6: Status pembayaran: unpaid → pending → paid
FR-4.7: Auto-create income transaction setelah verifikasi
FR-4.8: Toast notification saat verifikasi berhasil
FR-4.9: Tidak perlu webhook/WebSocket (manual verification)
```

### **4.5 Admin Dashboard**
```yaml
FR-5.1: Summary cards real-time:
       - Total booking hari ini
       - Pending / Confirmed / In Progress / Completed
       - Total pemasukan (Cash + Cashless)
FR-5.2: Grafik booking & revenue (Chart.js)
FR-5.3: List booking terbaru dengan status
FR-5.4: Filter by date range
FR-5.5: Cache dashboard stats (5 menit) untuk performa
FR-5.6: Quick actions (verifikasi pembayaran, tambah pengeluaran)
```

### **4.6 Booking Management (Admin)**
```yaml
FR-6.1: List semua booking dengan filter (status, tanggal)
FR-6.2: Detail booking lengkap
FR-6.3: Actions:
       - Confirm booking → assign beautician
       - Reject booking → kirim alasan
       - Mark as completed
       - Cancel booking
       - Reschedule booking
FR-6.4: Verifikasi pembayaran (klik tombol verify)
FR-6.5: Lihat bukti transfer yang diupload user
FR-6.6: QR code display untuk booking QRIS
```

### **4.7 Financial Management**
```yaml
FR-7.1: Pemasukan auto-record dari booking completed/verified
FR-7.2: Pemasukan dikategorikan: Cash, QRIS, Transfer
FR-7.3: CRUD pengeluaran dengan form:
       - Pilih kategori (dari expense_categories)
       - Icon (emoji)
       - Judul pengeluaran
       - Deskripsi detail
       - Jumlah (Rp)
       - Upload nota/bukti (opsional)
       - Tanggal transaksi
FR-7.4: Ringkasan keuangan:
       - Total pemasukan
       - Total pengeluaran
       - Laba/Rugi
FR-7.5: Grafik pemasukan vs pengeluaran
FR-7.6: Filter by periode (hari/minggu/bulan/tahun/custom)
FR-7.7: Export laporan PDF (Dompdf)
FR-7.8: Export laporan Excel (Maatwebsite)
FR-7.9: Toast notification setelah CRUD pengeluaran
```

### **4.8 Beautician Management**
```yaml
FR-8.1: Admin CRUD beautician
FR-8.2: Set spesialisasi & service area
FR-8.3: Set jadwal kerja (per hari, jam)
FR-8.4: Block time (libur/cuti)
FR-8.5: View performance (rating, total booking, revenue)
```

### **4.9 Membership & Reward**
```yaml
FR-9.1: Auto membership tier berdasarkan total booking:
       - Regular (0-5)
       - Silver (6-15) → diskon 5%
       - Gold (16-30) → diskon 10%
       - Platinum (>30) → diskon 15%
FR-9.2: Poin reward (1 poin per Rp 10.000 spending)
FR-9.3: Poin bisa ditukar voucher/discount
FR-9.4: Auto-upgrade membership setelah booking completed
```

### **4.10 Voucher System**
```yaml
FR-10.1: Admin CRUD voucher
FR-10.2: Voucher types: Persentase atau Nominal
FR-10.3: Set min purchase & max discount
FR-10.4: Periode berlaku & kuota
FR-10.5: User klaim & gunakan voucher
FR-10.6: Auto-validate voucher saat booking
```

### **4.11 Review & Rating**
```yaml
FR-11.1: User beri rating 1-5 + komentar setelah treatment selesai
FR-11.2: Upload foto hasil (opsional)
FR-11.3: Admin moderate review (approve/reject/balas)
FR-11.4: Rating average di treatment & beautician
```

### **4.12 Toast Notification System**
```yaml
FR-12.1: Toast muncul di POJOK KANAN BAWAH
FR-12.2: Tipe: Success (hijau), Error (merah), Warning (kuning), Info (biru)
FR-12.3: Auto-close setelah 4 detik dengan progress bar
FR-12.4: Bisa ditutup manual (klik X)
FR-12.5: Maksimal 3 toast sekaligus
FR-12.6: Animasi slide-in dari kanan
FR-12.7: Trigger via:
       - Session flash (setelah redirect)
       - JavaScript event (setelah AJAX)
       - Alpine.js component (setelah form submit)
FR-12.8: Toast scenarios:
       - Login/Register berhasil
       - Booking created
       - Payment method selected
       - Payment proof uploaded
       - Payment verified by admin
       - Profile updated
       - Review submitted
       - Expense added/updated/deleted
       - Data saved/deleted
       - Validation errors
       - Server errors
```

---

## **5. RACE CONDITION PREVENTION (BOOKING SYSTEM)**

### **5.1 Three-Layer Defense Strategy**

```yaml
Layer 1: APPLICATION LEVEL - Cache Lock (File/Database)
  - Menggunakan Laravel Cache Lock
  - Atomic lock per slot (beautician + date + time)
  - Mencegah concurrent request di aplikasi level
  - Timeout 10 detik, tunggu max 5 detik

Layer 2: DATABASE LEVEL - Pessimistic Locking
  - SELECT ... FOR UPDATE dalam transaction
  - Mengunci row booking yang overlap
  - Mencegah double booking di database level

Layer 3: DATABASE LEVEL - Unique Constraint
  - UNIQUE (beautician_id, booking_date, time_start)
  - Last defense jika 2 layer di atas gagal
  - Auto throw error jika duplicate
```

### **5.2 Implementation Code (BookingService)**

```php
// app/Services/BookingService.php
class BookingService
{
    public function createBooking(array $data): Booking
    {
        // Layer 1: Cache Lock
        $lockKey = sprintf('booking_lock:%d:%s:%s',
            $data['beautician_id'],
            $data['booking_date'],
            $data['time_start']
        );
        
        $lock = Cache::lock($lockKey, 10);
        
        try {
            if (!$lock->block(5)) {
                throw new SlotNotAvailableException('Slot sedang diproses');
            }
            
            // Layer 2: Database Transaction + Pessimistic Lock
            return DB::transaction(function () use ($data) {
                $existingBooking = Booking::where('beautician_id', $data['beautician_id'])
                    ->where('booking_date', $data['booking_date'])
                    ->where('time_start', $data['time_start'])
                    ->whereNotIn('status', ['canceled'])
                    ->lockForUpdate()
                    ->first();
                
                if ($existingBooking) {
                    throw new SlotNotAvailableException('Slot sudah dibooking');
                }
                
                // Create booking
                $booking = Booking::create([...]);
                
                // Clear cache
                Cache::forget("slots.{$data['booking_date']}.{$data['beautician_id']}");
                Cache::forget('admin.dashboard');
                
                return $booking;
            });
            
        } catch (QueryException $e) {
            // Layer 3: Unique constraint violation
            if ($e->errorInfo[1] === 1062) {
                throw new DoubleBookingException('Slot sudah dibooking orang lain');
            }
            throw $e;
        } finally {
            optional($lock)->release();
        }
    }
}
```

---

## **6. CACHE STRATEGY**

### **6.1 Cache Configuration**
```env
# .env - Pakai file cache (paling sederhana)
CACHE_DRIVER=file

# Atau database cache (lebih cepat dari file)
CACHE_DRIVER=database
# php artisan cache:table
# php artisan migrate
```

### **6.2 Cache Rules**
```yaml
Treatment List:      Cache 1 jam
Categories:           Cache 2 jam
Popular Treatments:   Cache 1 jam
Available Slots:      Cache 1 menit (cepat berubah)
Dashboard Stats:      Cache 5 menit
User Profile:         Cache 30 menit
```

### **6.3 Cache Invalidation**
```php
// Clear cache saat:
// - Treatment ditambah/update/delete
// - Booking created/canceled
// - Expense ditambah
// - Payment verified

// Contoh:
Cache::forget('treatments.all');
Cache::forget('treatments.popular');
Cache::forget('categories.all');
Cache::forget('admin.dashboard');
Cache::forget("slots.{$date}.{$beauticianId}");
```

---

## **7. TOAST NOTIFICATION SYSTEM**

### **7.1 Toast Component (resources/views/components/toast.blade.php)**
```blade
{{-- Toast Container - Pojok Kanan Bawah --}}
<div x-data="toastManager()" 
     @toast.window="addToast($event.detail)"
     class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="pointer-events-auto bg-white border rounded-lg shadow-lg p-4 cursor-pointer"
             :class="{
                'border-green-200 bg-green-50': toast.type === 'success',
                'border-red-200 bg-red-50': toast.type === 'error',
                'border-yellow-200 bg-yellow-50': toast.type === 'warning',
                'border-blue-200 bg-blue-50': toast.type === 'info',
             }"
             @click="removeToast(toast.id)">
            
            <div class="flex items-start gap-3">
                {{-- Dynamic Icon --}}
                <div class="flex-shrink-0" x-html="toast.icon"></div>
                
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900" x-text="toast.message"></p>
                    <p class="text-sm text-gray-500 mt-0.5" 
                       x-show="toast.description" 
                       x-text="toast.description"></p>
                </div>
                
                {{-- Close --}}
                <button @click.stop="removeToast(toast.id)" 
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            {{-- Progress Bar --}}
            <div class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full transition-all duration-300"
                     :class="{
                        'bg-green-500': toast.type === 'success',
                        'bg-red-500': toast.type === 'error',
                        'bg-yellow-500': toast.type === 'warning',
                        'bg-blue-500': toast.type === 'info',
                     }"
                     :style="`width: ${toast.progress}%`"></div>
            </div>
        </div>
    </template>
</div>
```

### **7.2 Toast Helper (PHP)**
```php
// app/Helpers/ToastHelper.php
namespace App\Helpers;

class ToastHelper
{
    public static function success(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'success',
            'message' => $message,
            'description' => $description,
        ]);
    }
    
    public static function error(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'error',
            'message' => $message,
            'description' => $description,
        ]);
    }
    
    public static function warning(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'warning',
            'message' => $message,
            'description' => $description,
        ]);
    }
    
    public static function info(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'info',
            'message' => $message,
            'description' => $description,
        ]);
    }
}
```

### **7.3 Toast Usage Examples**
```php
// Di Controller - via session flash
use App\Helpers\ToastHelper;

public function store(Request $request)
{
    // ... booking logic
    
    ToastHelper::success(
        'Booking Berhasil! 📅', 
        "Kode: {$booking->booking_code}. Silakan lakukan pembayaran."
    );
    
    return redirect()->route('user.bookings.show', $booking->booking_code);
}

public function verifyPayment(Request $request, string $bookingCode)
{
    // ... verification logic
    
    ToastHelper::success(
        'Pembayaran Terverifikasi! ✅',
        "Booking #{$bookingCode} telah dibayar dan dikonfirmasi."
    );
    
    return redirect()->back();
}
```

```javascript
// Di Blade View - via JavaScript untuk AJAX calls
<script>
// Trigger toast dari JavaScript
window.dispatchEvent(new CustomEvent('toast', {
    detail: {
        message: 'Data Tersimpan! 💾',
        description: 'Perubahan berhasil disimpan',
        type: 'success',
        duration: 4000
    }
}));

// Atau pakai helper
window.toastSuccess('Berhasil!', 'Data telah disimpan');
window.toastError('Gagal!', 'Terjadi kesalahan');
window.toastWarning('Perhatian!', 'Slot hampir penuh');
window.toastInfo('Info', 'Silakan lengkapi data');
</script>
```

```blade
{{-- Display session flash toast --}}
@if(session('toast'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.toast{{ ucfirst(session('toast.type')) }}(
            '{{ session('toast.message') }}',
            '{{ session('toast.description') }}'
        );
    });
</script>
@endif
```

---

## **8. USER INTERFACE DESIGN (KEY PAGES)**

### **8.1 Landing Page**
```
┌──────────────────────────────────────────────────────────┐
│  🏠 YALIA BEAUTY          📞 0812-3456-7890              │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌──────────┐     │
│  │ Home    │ │Treatment│ │ Booking │ │ 💰Harga  │     │
│  └─────────┘ └─────────┘ └─────────┘ └──────────┘     │
├──────────────────────────────────────────────────────────┤
│  🌸 Banner Hero: "Glow Up Your Beauty!"                 │
│  [✨ Booking Sekarang]                                   │
├──────────────────────────────────────────────────────────┤
│  🔍 Cari Treatment: [________________] 🔍                │
├──────────────────────────────────────────────────────────┤
│  Kategori:                                               │
│  [💇 Hair] [💆 Facial] [💅 Nail Art]                    │
│  [💄 Makeup] [🧖 Body Treatment]                        │
├──────────────────────────────────────────────────────────┤
│  ⭐ Treatment Populer:                                   │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐          │
│  │Creambath│ │Facial  │ │Manicure│ │Hair Spa│          │
│  │Rp 150K │ │Rp 200K │ │Rp 100K │ │Rp 250K │          │
│  │⭐4.8    │ │⭐4.9   │ │⭐4.7   │ │⭐4.6   │          │
│  │[Book]   │ │[Book]  │ │[Book]  │ │[Book]  │          │
│  └────────┘ └────────┘ └────────┘ └────────┘          │
└──────────────────────────────────────────────────────────┘
```

### **8.2 Booking Form (3-Step Wizard)**
```
┌──────────────────────────────────────────────────────────┐
│  📅 BUAT BOOKING                                         │
│  [1. Treatment] → [2. Detail] → [3. Pembayaran]        │
├──────────────────────────────────────────────────────────┤
│  STEP 1: Pilih Treatment                                │
│  ┌────────────────────────────────────────────┐         │
│  │ 🌿 Creambath                        ⭐4.8  │         │
│  │ Perawatan rambut dengan krim...            │         │
│  │ ⏱️ 60 menit                                │         │
│  │ 💰 Rp 150.000                              │         │
│  │ [Pilih Treatment]                          │         │
│  └────────────────────────────────────────────┘         │
│                                                          │
│  [❌ Batal]                    [Selanjutnya →]           │
└──────────────────────────────────────────────────────────┘
```

### **8.3 Admin Dashboard**
```
┌──────────────────────────────────────────────────────────┐
│  📊 DASHBOARD | 📅 Booking | 💆 Treatment | 💰 Keuangan │
├──────────────────────────────────────────────────────────┤
│  Summary Cards:                                          │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │
│  │📅 Booking│ │🟡Pending │ │🔄Proses  │ │✅Selesai │  │
│  │   25     │ │    5     │ │    3     │ │   17     │  │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘  │
│                                                          │
│  💰 Total Pemasukan Bulan Ini:                          │
│  ┌──────────────────────────────────────────┐           │
│  │ 💵 Cash: Rp 5.000.000                    │           │
│  │ 📱 Cashless: Rp 15.000.000               │           │
│  │ 🏦 Total: Rp 20.000.000                  │           │
│  └──────────────────────────────────────────┘           │
│                                                          │
│  📈 Grafik Booking Bulanan:                             │
│  [Chart.js Bar Chart]                                   │
│                                                          │
│  🔔 Booking Terbaru:                                    │
│  ┌──────────────────────────────────────────┐           │
│  │ 09:30 | Sari | Creambath | 🟡 Pending   │           │
│  │ 10:00 | Dewi | Facial   | 🔵 Confirmed │           │
│  │ 11:00 | Rina | Nail Art | 🟠 Progress  │           │
│  └──────────────────────────────────────────┘           │
└──────────────────────────────────────────────────────────┘
```

---

## **9. ROUTES STRUCTURE**

```php
// routes/web.php

// ============ PUBLIC ============
Route::get('/', [TreatmentController::class, 'home'])->name('home');
Route::get('/treatments', [TreatmentController::class, 'index'])->name('treatments.index');
Route::get('/treatments/{slug}', [TreatmentController::class, 'show'])->name('treatments.show');

// ============ AUTH (BREEZE) ============
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::get('/register', fn() => view('auth.register'))->name('register');
    // ... Breeze auth routes
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ============ USER (AUTH) ============
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{code}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{code}/payment-proof', [BookingController::class, 'uploadPaymentProof'])->name('bookings.upload-payment');
    Route::delete('/bookings/{code}', [BookingController::class, 'cancel'])->name('bookings.cancel');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Reviews
    Route::post('/bookings/{code}/review', [ReviewController::class, 'store'])->name('reviews.store');
});

// ============ ADMIN (AUTH + ADMIN) ============
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{code}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{code}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::put('/bookings/{code}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
    Route::put('/bookings/{code}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    Route::put('/bookings/{code}/verify-payment', [AdminBookingController::class, 'verifyPayment'])->name('bookings.verify-payment');
    
    // CRUD Management
    Route::resources([
        'treatments' => AdminTreatmentController::class,
        'beauticians' => AdminBeauticianController::class,
        'vouchers' => AdminVoucherController::class,
    ]);
    
    // Finance Management
    Route::prefix('finances')->name('finances.')->group(function () {
        Route::get('/', [AdminFinanceController::class, 'index'])->name('index');
        Route::get('/expenses', [AdminFinanceController::class, 'expenses'])->name('expenses');
        Route::post('/expenses', [AdminFinanceController::class, 'storeExpense'])->name('store-expense');
        Route::put('/expenses/{id}', [AdminFinanceController::class, 'updateExpense'])->name('update-expense');
        Route::delete('/expenses/{id}', [AdminFinanceController::class, 'deleteExpense'])->name('delete-expense');
        Route::get('/reports', [AdminFinanceController::class, 'reports'])->name('reports');
        Route::get('/export-pdf', [AdminFinanceController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [AdminFinanceController::class, 'exportExcel'])->name('export-excel');
    });
    
    // Users Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{id}/membership', [AdminUserController::class, 'updateMembership'])->name('users.membership');
});

// ============ API (AJAX) ============
Route::prefix('api/v1')->middleware(['auth'])->group(function () {
    Route::get('/slots', [Api\SlotController::class, 'index']);
    Route::post('/bookings', [Api\BookingController::class, 'store']);
    Route::get('/bookings/{code}', [Api\BookingController::class, 'show']);
});
```

---

## **10. DEVELOPMENT SPRINTS (2 WEEKS)**

### **Week 1: Foundation + Core Features**

#### **Day 1-2: Setup & Authentication**
```
✅ Install Laravel + Breeze (Blade)
✅ Database design & migrations
✅ Authentication system (login/register)
✅ Layout (navbar, footer, sidebar)
✅ User roles (admin middleware)
✅ Toast notification component
✅ Cache configuration
```

#### **Day 3-4: Core Booking**
```
✅ Treatment CRUD (admin)
✅ Treatment display (public)
✅ Category management
✅ Beautician management
✅ Booking form (3-step wizard)
✅ Slot checking via AJAX
✅ Race condition prevention (3-layer)
✅ QRIS generation
```

#### **Day 5: User Features**
```
✅ User dashboard
✅ Booking history
✅ Booking detail
✅ Payment proof upload
✅ Profile management
```

### **Week 2: Admin + Polish**

#### **Day 6-7: Admin Dashboard**
```
✅ Dashboard statistics (cached)
✅ Charts & graphs
✅ Booking management (confirm/reject/complete)
✅ Payment verification
✅ Finance module (income auto + expense CRUD)
```

#### **Day 8-9: Advanced Features**
```
✅ Voucher system
✅ Membership auto-upgrade
✅ Points calculation
✅ Review & rating
✅ Export PDF/Excel
✅ Expense categories (dynamic icons)
```

#### **Day 10: Polish & Testing**
```
✅ Mobile responsive
✅ Form validation
✅ Error handling
✅ Loading states
✅ Toast integration (all scenarios)
✅ Testing (manual + unit)
✅ Bug fixing
✅ Documentation
```

#### **Day 11-12: Finalization**
```
✅ Deployment preparation
✅ Database seeder (demo data)
✅ README documentation
✅ UKK presentation materials
✅ Final testing
```

---

## **11. NON-FUNCTIONAL REQUIREMENTS**

### **11.1 Performance**
```yaml
NFR-1: Page load < 3 detik
NFR-2: Cache frequently accessed data
NFR-3: Optimize images
NFR-4: Database indexing
NFR-5: Lazy loading for images
NFR-6: Minify CSS/JS (Vite build)
NFR-7: paginate the output
```

### **11.2 Security**
```yaml
NFR-8: CSRF protection (built-in Laravel)
NFR-9: XSS prevention (Blade auto-escape)
NFR-10: SQL injection prevention (Eloquent)
NFR-11: Input validation (server + client)
NFR-12: File upload validation (size, type)
NFR-13: Rate limiting (optional)
NFR-14: Authentication required for protected routes
NFR-15: Admin middleware for admin routes
NFR-16: Password hashing (hash)
```

### **11.3 Usability**
```yaml
NFR-17: Mobile-responsive (Tailwind responsive classes)
NFR-18: Intuitive navigation (max 3 clicks to book)
NFR-19: Clear error messages (Bahasa Indonesia)
NFR-20: Loading states (spinner, skeleton)
NFR-21: Visual feedback (toast notifications)
NFR-22: Confirmation dialogs for destructive actions
```

### **11.4 Reliability**
```yaml
NFR-23: Race condition prevention (3-layer defense)
NFR-24: Unique constraints di database
NFR-25: Graceful error handling
NFR-26: Session management
NFR-27: Cache invalidation strategy
```

---

## **12. SUCCESS CRITERIA (UKK)**

```
✅ 1. User dapat register & login
✅ 2. User dapat browse treatments dengan kategori
✅ 3. User dapat membuat booking (3-step wizard)
✅ 4. Slot time real-time checking
✅ 5. Tidak ada double booking (race condition handled)
✅ 6. QRIS generation & display
✅ 7. User upload bukti transfer
✅ 8. Admin verifikasi pembayaran
✅ 9. Admin dashboard dengan statistik real-time
✅ 10. Booking management (confirm/reject/complete)
✅ 11. Finance: auto income + manual expense CRUD
✅ 12. Expense categories dengan icons
✅ 13. Export laporan PDF & Excel
✅ 14. Membership auto-upgrade
✅ 15. Voucher system
✅ 16. Review & rating
✅ 17. Toast notification di pojok kanan bawah
✅ 18. Mobile responsive
✅ 19. Cache system untuk performa
✅ 20. Form validation (client & server)
```

---

## **13. SETUP INSTRUCTIONS**

```bash
# 1. Clone / create project
# composer create-project laravel/laravel yalia-beauty
# cd yalia-beauty

# 2. Install Breeze (Blade version)
# composer require laravel/breeze --dev
# php artisan breeze:install blade

# 3. Install required packages
# composer require simplesoftwareio/simple-qrcode
# composer require barryvdh/laravel-dompdf
# composer require maatwebsite/excel 

# 4. Setup environment
# cp .env.example .env
# Edit .env: database, app URL, etc

# 5. Generate key & storage link
php artisan key:generate
php artisan storage:link

# 6. Setup database
php artisan migrate

# 7. Run seeders (demo data)
php artisan db:seed

# 8. Install npm & build
npm install
npm run build

# 9. Run dev server
php artisan serve
npm run dev  # For hot reload in development

# 10. Setup cache (jika pakai database cache)
php artisan cache:table
php artisan migrate

# 11. Default admin user
# Email: admin@yalia.com
# Password: password
```

---

## **14. DEMO DATA (Database Seeder)**

```php
// database/seeders/DatabaseSeeder.php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::factory()->create([
            'name' => 'Bu Yulia',
            'email' => 'admin@yalia.com',
            'phone' => '081234567890',
            'is_admin' => true,
        ]);
        
        // Create 10 customers
        User::factory(10)->create();
        
        // Create categories
        $categories = [
            ['name' => 'Hair Treatment', 'icon' => '💇', 'slug' => 'hair-treatment'],
            ['name' => 'Facial Treatment', 'icon' => '💆', 'slug' => 'facial-treatment'],
            ['name' => 'Nail Art', 'icon' => '💅', 'slug' => 'nail-art'],
            ['name' => 'Body Treatment', 'icon' => '🧖', 'slug' => 'body-treatment'],
            ['name' => 'Makeup', 'icon' => '💄', 'slug' => 'makeup'],
        ];
        
        foreach ($categories as $cat) {
            Category::create($cat);
        }
        
        // Create treatments
        Treatment::insert([
            [
                'category_id' => 1,
                'name' => 'Creambath',
                'slug' => 'creambath',
                'description' => 'Perawatan rambut...',
                'price' => 150000,
                'duration_minutes' => 60,
                'badge' => 'best_seller',
            ],
            // ... more treatments
        ]);
        
        // Create beauticians
        Beautician::insert([
            [
                'name' => 'Mawar',
                'phone' => '081122334455',
                'specialties' => json_encode([1, 2]),
                'service_area' => json_encode(['Jakarta Selatan', 'Jakarta Pusat']),
            ],
            // ... more beauticians
        ]);
        
        // Create expense categories
        $expenseCategories = [
            ['name' => 'Pembelian Alat', 'icon' => '🛒'],
            ['name' => 'Bahan Kosmetik', 'icon' => '🧴'],
            ['name' => 'Listrik', 'icon' => '⚡'],
            ['name' => 'Air', 'icon' => '💧'],
            ['name' => 'Internet', 'icon' => '📱'],
            ['name' => 'Sewa Tempat', 'icon' => '🏠'],
            ['name' => 'Gaji Karyawan', 'icon' => '👥'],
            ['name' => 'Marketing', 'icon' => '🎯'],
            ['name' => 'Maintenance', 'icon' => '🔧'],
            ['name' => 'Lainnya', 'icon' => '📝'],
        ];
        
        foreach ($expenseCategories as $cat) {
            ExpenseCategory::create($cat);
        }
    }
}
```

---

**Document Version:** 2.0 (Full Laravel Stack)  
**Last Updated:** 2024  
**Status:** UKK Preparation - 2 Weeks Development  
**Tech Stack:** Laravel 11 (Blade + Alpine.js) + MySQL + File/Database Cache  

---

**AI Instructions for Development:**
1. Gunakan Blade template + Alpine.js untuk SEMUA interaktivitas
2. Implementasi toast notification di POJOK KANAN BAWAH untuk setiap aksi
3. Gunakan cache file/database (bukan Redis)
4. Implementasi 3-layer race condition prevention (Cache Lock + DB Lock + Unique Constraint)
5. QRIS manual (generate QR code, admin verifikasi manual)
6. Semua form menggunakan server-side validation + client-side Alpine validation
7. Mobile-responsive dengan Tailwind CSS
8. Export laporan PDF & Excel
9. Toast helper class untuk controller (session flash)
10. JavaScript toast helper untuk AJAX calls
11. Semua teks dalam Bahasa Indonesia
12. Loading states di setiap form submission
13. Confirmation dialog untuk destructive actions (delete, cancel)
14. Buat seeder dengan data Indonesia (nama, alamat, treatment names)
15. Code mengikuti PSR-12 standard
16. Blade components untuk reusable UI (toast, modal, navbar, sidebar)

**Project ini HARUS selesai dalam 2 minggu dengan fitur-fitur yang sudah disebutkan. Prioritaskan core features (booking flow, payment, admin dashboard) terlebih dahulu, lalu tambahkan fitur tambahan jika masih ada waktu.**
