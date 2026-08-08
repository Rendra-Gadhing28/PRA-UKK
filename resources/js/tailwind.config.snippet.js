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
module.exports = {
    theme: {
        extend: {
            borderRadius: {
                // Tailwind default 2xl = 1rem, mockup minta 1.5rem — override
                // scoped di sini saja (bukan DEFAULT) supaya tidak mengubah
                // `rounded` polos di halaman lain yang sudah ada.
                '2xl': '1.5rem',
            },
            colors: {
                primary: '#b01f44',
                'on-primary': '#ffffff',
                'primary-container': '#d23b5b',
                'on-primary-container': '#fffbff',
                'on-primary-fixed': '#400010',
                'on-primary-fixed-variant': '#910030',
                'primary-fixed': '#ffd9dc',
                'primary-fixed-dim': '#ffb2ba',
                'inverse-primary': '#ffb2ba',
                secondary: '#9b4054',
                'on-secondary': '#ffffff',
                'secondary-container': '#ff8fa4',
                'on-secondary-container': '#79253a',
                'secondary-fixed': '#ffd9de',
                'secondary-fixed-dim': '#ffb2be',
                'on-secondary-fixed': '#400014',
                'on-secondary-fixed-variant': '#7d283d',
                tertiary: '#785341',
                'on-tertiary': '#ffffff',
                'tertiary-container': '#946c58',
                'on-tertiary-container': '#fffbff',
                'tertiary-fixed': '#ffdbcb',
                'tertiary-fixed-dim': '#edbca5',
                'on-tertiary-fixed': '#2e1507',
                'on-tertiary-fixed-variant': '#613f2d',
                error: '#ba1a1a',
                'on-error': '#ffffff',
                'error-container': '#ffdad6',
                'on-error-container': '#93000a',
                background: '#fff8f8',
                'background-main': '#fdf5f6',
                surface: '#fff8f8',
                'surface-tint': '#b42246',
                'surface-light': '#fff8f9',
                'surface-bright': '#fff8f8',
                'surface-dim': '#ebd4d9',
                'surface-variant': '#f4dde1',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#fff0f2',
                'surface-container': '#ffe8ed',
                'surface-container-high': '#fae2e7',
                'surface-container-highest': '#f4dde1',
                'inverse-surface': '#3b2d30',
                'inverse-on-surface': '#ffecef',
                'on-surface': '#25181c',
                'on-surface-variant': '#594043',
                'on-background': '#25181c',
                outline: '#8d7072',
                'outline-variant': '#e0bec1',
                'text-heading': '#2b1a1f',
                'accent-clear': '#ffd2e1',
                'border-subtle': 'rgba(91, 58, 41, 0.12)',
            },
            fontFamily: {
                'headline-lg': ['"Playfair Display"', 'serif'],
                'headline-md': ['"Playfair Display"', 'serif'],
                'headline-sm': ['"Playfair Display"', 'serif'],
                'body-md': ['"Work Sans"', 'sans-serif'],
                'body-sm': ['"Work Sans"', 'sans-serif'],
                'label-lg': ['"Work Sans"', 'sans-serif'],
                'label-md': ['"Work Sans"', 'sans-serif'],
                button: ['"Work Sans"', 'sans-serif'],
            },
            fontSize: {
                'headline-lg-mobile': ['36px', { lineHeight: '1.2', fontWeight: '600' }],
                'headline-lg': ['48px', { lineHeight: '1.2', fontWeight: '600' }],
                'headline-md': ['32px', { lineHeight: '1.3', fontWeight: '600' }],
                'headline-sm': ['24px', { lineHeight: '1.4', fontWeight: '600' }],
                'body-lg': ['18px', { lineHeight: '1.6', fontWeight: '400' }],
                'body-md': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
                'body-sm': ['14px', { lineHeight: '1.5', fontWeight: '400' }],
                'label-lg': ['14px', { lineHeight: '1', letterSpacing: '0.05em', fontWeight: '600' }],
                'label-md': ['12px', { lineHeight: '1', fontWeight: '500' }],
                button: ['16px', { lineHeight: '1', fontWeight: '600' }],
            },
            spacing: {
                'margin-desktop': '4rem',
                'margin-mobile': '1rem',
                gutter: '1.5rem',
            },
        },
    },
};
