/**
 * GABUNGKAN object di bawah ini ke dalam `theme.extend` pada
 * tailwind.config.js kamu yang sudah ada. Jangan overwrite file itu —
 * cukup merge key-key ini supaya utility class di view (bg-primary,
 * text-headline-lg, font-headline-md, dst) ter-generate saat build.
 *
 * Kenapa tidak pakai <script src="cdn.tailwindcss.com">? Karena CDN build
 * mengompilasi seluruh utility class di browser tiap kali halaman dibuka
 * (blocking JS, tidak ada purge/minify) — buruk untuk LCP/TBT. Dengan
 * config ini + Vite (`@vite(['resources/css/app.css'])`), CSS sudah
 * di-purge & di-minify saat build, dikirim sebagai file .css statis biasa.
 */

