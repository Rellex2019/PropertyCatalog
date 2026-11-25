import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: 'rgb(var(--brand-default) / <alpha-value>)',
                    ink: 'rgb(var(--brand-ink) / <alpha-value>)',
                    forest: 'rgb(var(--brand-forest) / <alpha-value>)',
                    teal: 'rgb(var(--brand-teal) / <alpha-value>)',
                    gold: 'rgb(var(--brand-gold) / <alpha-value>)',
                    'gold-light': 'rgb(var(--brand-gold-light) / <alpha-value>)',
                    'gold-dark': 'rgb(var(--brand-gold-dark) / <alpha-value>)',
                    cream: 'rgb(var(--brand-cream) / <alpha-value>)',
                    sand: 'rgb(var(--brand-sand) / <alpha-value>)',
                    line: 'rgb(var(--brand-line) / <alpha-value>)',
                    muted: 'rgb(var(--brand-muted) / <alpha-value>)',
                    soft: 'rgb(var(--brand-soft) / <alpha-value>)',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
