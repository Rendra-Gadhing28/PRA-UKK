import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx}',
    ],

    safelist: [
        'translate-x-0',
        'translate-x-5',
        'translate-x-4',
        'bg-[#f45472]',
        'bg-gray-200',
    ],

    theme: {
        extend: {
            borderRadius: {
                // Tailwind default 2xl = 1rem, mockup minta 1.5rem — override
                // scoped di sini saja (bukan DEFAULT) supaya tidak mengubah
                // `rounded` polos di halaman lain yang sudah ada.
                '2xl': '1.5rem',
            },
            colors: {
                'primary': '#b01f44',
                'on-primary': '#ffffff',
                'primary-container': '#d23b5b',
                'on-primary-container': '#fffbff',
                'on-primary-fixed': '#400010',
                'on-primary-fixed-variant': '#910030',
                'primary-fixed': '#ffd9dc',
                'primary-fixed-dim': '#ffb2ba',
                'inverse-primary': '#ffb2ba',
                'secondary': '#9b4054',
                'on-secondary': '#ffffff',
                'secondary-container': '#ff8fa4',
                'on-secondary-container': '#79253a',
                'secondary-fixed': '#ffd9de',
                'secondary-fixed-dim': '#ffb2be',
                'on-secondary-fixed': '#400014',
                'on-secondary-fixed-variant': '#7d283d',
                'tertiary': '#785341',
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

                // ==== Palet tambahan (custom palette) ====
                // Catatan: `secondary` (#9b4054) di atas adalah warna yang sama
                // dengan "primary" di palet ini, dan `accent-clear` (#ffd2e1)
                // adalah base dari skala `rose` & `blush` di bawah. Dipisah pakai
                // nama baru (bukan menimpa `secondary`/`primary`) supaya token
                // M3 yang sudah dipakai di blade/komponen lain tidak berubah.

                // Skala burgundy — versi bertingkat dari `secondary` (#9b4054)
                burgundy: {
                    50: '#f5ecee',
                    100: '#ebd9dd',
                    200: '#d7b3bb',
                    300: '#c38c98',
                    400: '#af6676',
                    500: '#9b4054', // = secondary
                    600: '#7c3343',
                    700: '#5d2632',
                    800: '#3e1a22',
                    900: '#1f0d11',
                    950: '#100608',
                },

                // Warna komplementer — mint, dipasangkan dengan burgundy
                mint: {
                    50: '#fbfffe',
                    100: '#f6fffc',
                    200: '#edfff9',
                    300: '#e4fff6',
                    400: '#dbfff3',
                    500: '#d2fff0',
                    600: '#a8ccc0',
                    700: '#7e9990',
                    800: '#546660',
                    900: '#2a3330',
                    950: '#151a18',
                },

                // Monokromatik vibrant dari `accent-clear` (#ffd2e1) — untuk CTA/aksen hidup
                rose: {
                    100: '#ffecf2',
                    200: '#ffd2e1', // = accent-clear
                    300: '#ffb9d0',
                    400: '#ff9fbf',
                    500: '#ff86ae',
                },

                // Skala pastel lembut (tint & shade) dari `accent-clear` — untuk background/surface
                blush: {
                    50: '#fffafc',
                    100: '#fff6f9',
                    200: '#ffedf3',
                    300: '#ffe4ed',
                    400: '#ffdbe7',
                    500: '#ffd2e1', // = accent-clear
                    600: '#cca8b4',
                    700: '#997e87',
                    800: '#66545a',
                    900: '#332a2d',
                    950: '#191516',
                },

                // Warna rekomendasi — variasi kartu, badge, ilustrasi
                recommended: {
                    rose: '#eecadc',
                    lilac: '#f1e3f1',
                    peach: '#f6d4cd',
                },

                // Warna terkait — detail kecil, hover state, dekorasi
                related: {
                    blush: '#f4c7db',
                    candy: '#fdd4dd',
                    orchid: '#fccfed',
                    cream: '#fae2e3',
                    mauve: '#f4d3e8',
                },

                // VIP Membership Colorful Palette
                'vip-purple': '#6b21a8',
                'vip-purple-dark': '#2e1065',
                'vip-purple-light': '#d8b4fe',
                'vip-gold': '#d97706',
                'vip-gold-dark': '#451a03',
                'vip-silver': '#0284c7',
                'vip-silver-dark': '#0f172a',
                'vip-rose': '#e11d48',
            },
            backgroundImage: {
                'vip-gradient-purple': 'linear-gradient(135deg, #1e1b4b 0%, #4c1d95 40%, #6b21a8 70%, #2e1065 100%)',
                'vip-gradient-gold': 'linear-gradient(135deg, #451a03 0%, #78350f 40%, #b45309 70%, #78350f 100%)',
                'vip-gradient-silver': 'linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0369a1 70%, #0c4a6e 100%)',
                'vip-gradient-regular': 'linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #334155 70%, #1e293b 100%)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
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

    plugins: [forms],
};