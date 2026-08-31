# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Laravel 13, Octane v2, Breeze v2, Tailwind CSS v3, Alpine.js v3, React v19, Blade

## Users

Women aged 18–50 in Indonesia seeking beauty & wellness treatments (hair, facial, nail, makeup, body care) at the salon or via home service, as well as salon administrators and beauticians managing daily schedules, bookings, and finances.

## Product Purpose

Provide an elegant, seamless booking experience for salon and home beauty services with real-time slot availability, beautician assignment, loyalty rewards, and operational management tools for salon staff.

## Positioning

Integrated beauty salon & home service platform tailored for the Indonesian market, combining online scheduling, home service dispatch, quarterly loyalty tier points, Midtrans payment gateway, and admin financial/expense tracking.

## Operating Context

Customers browse treatments on mobile/desktop browsers, select salon vs home service, pick time slots and preferred beauticians, apply vouchers, and pay via Midtrans (QRIS, Transfer, e-wallet) or Cash. Admin staff monitor daily schedules, assign beauticians, track daily revenue/expenses, and manage vouchers from the admin portal.

## Capabilities and Constraints

- **Capabilities**: Treatment catalog & detail views, home service radius validation, slot availability calculation, beautician assignment, booking status lifecycle (pending, confirmed, completed, cancelled), quarterly tier points & badge levels, vouchers, customer reviews, Midtrans payment webhook handling, admin management portal.
- **Constraints**: Indonesian Rupiah currency (IDR), Midtrans payment gateway integration, localized home service service radius.

## Brand Commitments

- **Name**: Yalia Beauty
- **Tagline**: "Glow Up Your Beauty"
- **Personality**: Elegant, Feminine, Modern, Warm, Trustworthy
- **Aesthetic**: Soft pink tones (#f472b6, #db2777), warm brown accents, off-white backgrounds (#fffefb), refined typography (Playfair Display / Plus Jakarta Sans).

## Evidence on Hand

- Core Laravel 13 codebase with Eloquent models, controllers, enums, Blade views, and Tailwind CSS configuration (`app/`, `resources/`, `routes/`).
- Design system documentation in [`DESIGN.md`](file:///d:/laragon/www/salon-yalia-beauty/DESIGN.md).
- Performance & feature optimization logs in [`Optimalisasi.md`](file:///d:/laragon/www/salon-yalia-beauty/Optimalisasi.md).

## Product Principles

1. **Elegance First**: Soft, sophisticated color palette, clean typography hierarchy, and refined proportions.
2. **Warmth & Comfort**: Soft off-white backgrounds and rounded shapes for an inviting user experience.
3. **Frictionless Booking**: Intuitive step-by-step scheduling, transparent slot availability, and instant confirmation.
4. **Operational Clarity**: Real-time administrative oversight for schedules, beautician workloads, and financial tracking.

## Accessibility & Inclusion

Touch-friendly targets (min 44px), high-contrast readable text over soft backgrounds, accessible form labels, and responsive layout adaptations across mobile and desktop viewports.

---

## Core Entities & Database Architecture

The product model relies on **15 Core Database Entities**:
- **User**: Customer profile, role (`admin` / `user`), tier level (`regular`, `silver`, `gold`, `platinum`), points balance, tier points, and Google OAuth credentials.
- **Beautician**: Beauty specialist profile, working status, assigned bookings count, and service area boundaries.
- **BeauticianSchedule**: Weekly operational schedule per beautician (`day_of_week`, `start_time`, `end_time`).
- **Category**: Treatment categorizations (Facial, Hair, Nail, Makeup, Body Care) with custom icons and display order.
- **Treatment**: Service offerings with pricing, duration in minutes, PTS reward points, ratings, and badges.
- **Booking**: Core transaction holding booking codes, service type (`salon` vs `home`), GPS coordinates, beautician assignment, costs breakdown, Midtrans QRIS order IDs, payment status (`unpaid`, `pending`, `paid`), and reminder flags.
- **BookingTreatment**: M:N pivot entity linking bookings and treatments with quantity & unit price snapshots.
- **Voucher**: Promo discount codes and reward point exchange options with expiry and quota constraints.
- **UserVoucher**: Tracked voucher redemptions per user.
- **Review**: Customer ratings (1-5 stars), photo documentation, and admin response entries per booking.
- **Transaction**: Financial income/expense journal records tied to bookings or manual entries.
- **Expense & ExpenseItem**: Personal/operational expense tracking with AI receipt image extraction.

---

## End-to-End User Flow & Feature Walkthrough

1. **Authentication (`/login`, `/register`, `/auth/google`)**:
   - Customer logs in via email credentials or 1-click Google OAuth.
2. **Dashboard & Daily Bonus (`/dashboard`)**:
   - Customer collects daily check-in reward (+10 PTS) and views recommendations.
3. **Treatment Discovery (`/dashboard/treatments`)**:
   - Filters treatments by category, price, and ratings.
4. **Interactive Booking Wizard (`/dashboard/booking/buat`)**:
   - Step 1: Select services & quantity.
   - Step 2: Choose Salon Visit or Home Service (GPS geolocation & transport fee calculation).
   - Step 3: Pick date, time slot, and preferred beautician.
   - Step 4: Apply vouchers, choose Full Payment or Down Payment (DP), select QRIS or Cash.
5. **Instant QRIS Payment (`/dashboard/booking/{id}/pembayaran`)**:
   - Scans dynamic QRIS with 15-minute expiration window. Realtime polling updates page upon Midtrans webhook trigger.
6. **Service & Completion (`/dashboard/booking/{id}`)**:
   - Automated cron reminders (H-24, H-1, M-30). Beautician uploads treatment outcome photo (`photo_assign`). Admin marks completed. PTS points & membership tier automatically awarded.
7. **Reviews & Rewards (`/dashboard/vouchers`, review page)**:
   - Customer submits 5-star review and exchanges accumulated PTS points for exclusive vouchers.
8. **AI Receipt Tracker (`/expenses`)**:
   - Uploads beauty product receipts for automatic AI itemization.
9. **Logout (`/logout`)**:
   - Terminates session securely.

