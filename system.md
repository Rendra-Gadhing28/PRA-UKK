# DOKUMENTASI SISTEM & FLOWCHART SISTEM END-TO-END SALON YALIA BEAUTY

Dokumen ini menjelaskan alur kerja teknis sistem informasi website **Salon Yalia Beauty** berbasis Laravel secara end-to-end, mencakup logika sistem, pengolahan database, integrasi payment gateway Midtrans, manajemen poin & VIP, serta modul dashboard Admin/Kapster.

---

## 1. FLOWCHART SISTEM END-TO-END (MERMAID SYSTEM FLOWCHART)

Berikut adalah **Flowchart Sistem** yang menggambarkan aliran data otomatis antara pengguna, server Laravel Octane, Database, Midtrans Payment Gateway, dan Dashboard Pengguna.

```mermaid
flowchart TD
    classDef client fill:#f9f9f9,stroke:#333,stroke-width:1.5px;
    classDef system fill:#e3f2fd,stroke:#1565c0,stroke-width:1.5px;
    classDef db fill:#fff3e0,stroke:#e65100,stroke-width:1.5px,shape:cylinder;
    classDef gateway fill:#e8f5e9,stroke:#2e7d32,stroke-width:1.5px;

    subgraph SYSTEM_FLOW ["FLOWCHART SISTEM WEBSITE YALIA BEAUTY"]
        direction TB

        subgraph Pelanggan ["💻 INTERFACE PELANGGAN"]
            direction TB
            U1([Mulai Access Website]):::client
            U2[Registrasi / Login Akun]:::client
            U3[Pilih Treatment, Tipe Service & Slot Kapster]:::client
            U4[Input Alamat Home Service / Konfirmasi Salon]:::client
            U5[Gunakan Point Reward / Voucher VIP]:::client
            U6[Redirect ke Midtrans Snap Payment]:::client
            U7[Menerima Notifikasi Booking Confirmed]:::client
            U8[Memberikan Ulasan & Rating]:::client
        end

        subgraph LaravelApp ["⚙️ LARAVEL BACKEND SYSTEM"]
            direction TB
            S1[Verifikasi Sesi & Auth Middleware]:::system
            S2[Cek Ketersediaan Slot Kapster & Jadwal]:::system
            S3[Kalkulasi Subtotal + Diskon VIP Tier]:::system
            S4[Generate Booking ID & Midtrans Snap Token]:::system
            S5[Receive Midtrans Webhook / Callback]:::system
            S6[Update Booking Status -> PAID]:::system
            S7[Trigger Event: Notifikasi Kapster & User]:::system
            S8[Kalkulasi Reward Points & Daily Check-in]:::system
            S9[Simpan Review & Update Rating Kapster]:::system
        end

        subgraph Database ["🗄️ DATABASE (MySQL / Cache Engine)"]
            direction TB
            DB1[(users & memberships)]:::db
            DB2[(treatments & beauticians)]:::db
            DB3[(bookings & transactions)]:::db
            DB4[(reviews & reward_points)]:::db
        end

        subgraph PaymentGateway ["💳 PAYMENT GATEWAY (MIDTRANS)"]
            direction TB
            M1[Terima Request Snap Token]:::gateway
            M2[Proses Pembayaran User]:::gateway
            M3[Kirim Notification Callback HTTP POST]:::gateway
        end

        subgraph DashboardKapsterAdmin ["💆‍♀️ KAPSTER & 👑 ADMIN DASHBOARD"]
            direction TB
            A1[Kapster: Terima Jadwal & Update In-Progress]:::system
            A2[Kapster: Selesaikan Treatment -> COMPLETED]:::system
            A3[Admin: Monitor Transaksi & Export Laporan]:::system
        end
    end

    %% ALUR RELASI SISTEM
    U1 --> U2
    U2 --> S1
    S1 <--> DB1
    
    U3 --> S2
    S2 <--> DB2
    
    U4 --> U5
    U5 --> S3
    S3 <--> DB1
    
    S3 --> S4
    S4 --> DB3
    S4 --> M1
    M1 --> U6
    
    U6 --> M2
    M2 --> M3
    M3 --> S5
    
    S5 --> S6
    S6 --> DB3
    S6 --> S7
    S7 --> U7
    S7 --> A1
    
    A1 --> A2
    A2 --> S8
    S8 --> DB1
    S8 --> DB4
    
    U7 --> U8
    U8 --> S9
    S9 --> DB4
    
    S6 --> A3
    A3 <--> DB3
```

---

## 2. STATE MACHINE STATUS BOOKING & TRANSAKSI

Sistem mengontrol status pesanan secara ketat dengan status berikut:

```mermaid
stateDiagram-v2
    [*] --> PENDING : User Buat Booking
    PENDING --> CANCELLED : Expire / Batal Bayar
    PENDING --> PAID : Webhook Midtrans Success / Admin Confirm
    PAID --> CONFIRMED : System Assign Kapster
    CONFIRMED --> IN_PROGRESS : Kapster Mulai Perawatan
    IN_PROGRESS --> COMPLETED : Kapster Klik Selesai
    COMPLETED --> REWARDED : Point & Cashback Ditambahkan
    REWARDED --> [*]
```

---

## 3. ALUR LOGIKA MODUL UTAMA WEBSITE

### A. Modul Autentikasi & VIP Membership
1. **Registrasi/Login**: Sistem memeriksa email & password terenkripsi (`Bcrypt`).
2. **Kalkulasi VIP Tier (`app/Support/Membership.php`)**:
   - **Bronze**: Pendaftaran awal.
   - **Silver**: Total transaksi $\ge Rp 500.000$ (Diskon 5%, Metallic Silver UI).
   - **Gold**: Total transaksi $\ge Rp 1.500.000$ (Diskon 10%, Gold Glow UI).
   - **Platinum**: Total transaksi $\ge Rp 3.500.000$ (Diskon 15%, Premium Dark UI).

### B. Modul Pemesanan (Booking Engine)
1. **Validasi Slot**: Sistem mengecek tabel `bookings` untuk memastikan `beautician_id` tidak bentrok di jam dan tanggal yang sama.
2. **Kalkulasi Biaya**:
   $$\text{Total} = \text{Harga Treatment} - \text{Diskon VIP} - \text{Poin Reward Terpakai} + \text{Biaya Transport (jika Home Service)}$$
3. **Penyimpanan Database**: Menyimpan record di tabel `bookings` (status `pending`) dan `transactions`.

### C. Modul Payment Gateway (Midtrans Integration)
1. Backend memanggil Midtrans Snap API menggunakan ServerKey untuk mendapatkan `snap_token`.
2. Setelah pelanggan membayar, Midtrans mengirimkan HTTP POST Notification (Webhook) ke endpoint `/api/midtrans-callback`.
3. Signature Key diverifikasi:
   $$\text{SHA512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey})$$
4. Jika valid, status transaksi diubah dari `pending` menjadi `paid`.

### D. Modul Eksekusi Kapster & Reward System
1. **Pekerjaan Kapster**: Kapster menerima notifikasi di dashboard mereka, mengubah status menjadi `in_progress` saat mulai melayani, dan `completed` setelah selesai.
2. **Poin & Check-in**:
   - Setelah status `completed`, poin otomatis ditambahkan ke `users.points`.
   - Modul Daily Check-in memberikan poin tambahan harian (Streak Poin).

### E. Modul Admin & Caching (`Cache::remember`)
1. Dashboard Admin menggunakan Laravel Cache untuk mempercepat query statistik harian (`topTreatments`, `totalRevenue`, `activeBeauticians`).
2. Admin dapat mengunduh laporan keuangan dan performa kapster.

---

## 4. STRUKTUR DIKTATOR DATABASE (ENTITY RELATIONSHIPS)

- **`users`**: `id`, `name`, `email`, `role` (customer/admin/superadmin), `points`, `total_spent`.
- **`beauticians`**: `id`, `user_id`, `name`, `rating`, `status_active`.
- **`treatments`**: `id`, `category_id`, `name`, `price`, `duration_minutes`, `type` (salon/home_service/both).
- **`bookings`**: `id`, `booking_code`, `user_id`, `beautician_id`, `treatment_id`, `booking_date`, `time_slot`, `service_type`, `status`.
- **`transactions`**: `id`, `booking_id`, `payment_gateway`, `snap_token`, `payment_status`, `gross_amount`.
- **`reviews`**: `id`, `booking_id`, `user_id`, `beautician_id`, `rating`, `comment`.

---

Dokumen ini menjadi acuan teknis resmi alur sistem website **Salon Yalia Beauty**.
