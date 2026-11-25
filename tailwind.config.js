import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                    DEFAULT: 'rgb(var(--brand-surface) / <alpha-value>)',
                    ink: 'rgb(var(--brand-ink) / <alpha-value>)',
                    forest: 'rgb(var(--brand-surface-hover) / <alpha-value>)',
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
                surface: {
                    page: 'rgb(var(--surface-page) / <alpha-value>)',
                    card: 'rgb(var(--surface-card) / <alpha-value>)',
                    elevated: 'rgb(var(--surface-elevated) / <alpha-value>)',
                    footer: 'rgb(var(--surface-footer) / <alpha-value>)',
                    subtle: 'rgb(var(--surface-subtle) / <alpha-value>)',
                    overlay: 'rgb(var(--surface-overlay) / <alpha-value>)',
                },
                content: {
                    DEFAULT: 'rgb(var(--content) / <alpha-value>)',
                    muted: 'rgb(var(--content-muted) / <alpha-value>)',
                    soft: 'rgb(var(--content-soft) / <alpha-value>)',
                },
                line: 'rgb(var(--line) / <alpha-value>)',
                accent: {
                    DEFAULT: 'rgb(var(--accent) / <alpha-value>)',
                    light: 'rgb(var(--accent-light) / <alpha-value>)',
                    dark: 'rgb(var(--accent-dark) / <alpha-value>)',
                },
                on: {
                    accent: 'rgb(var(--on-accent) / <alpha-value>)',
                    brand: 'rgb(var(--on-brand) / <alpha-value>)',
                },
                danger: {
                    bg: 'rgb(var(--danger-bg) / <alpha-value>)',
                    line: 'rgb(var(--danger-line) / <alpha-value>)',
                    text: 'rgb(var(--danger-text) / <alpha-value>)',
                    solid: 'rgb(var(--danger-solid) / <alpha-value>)',
                },
                success: {
                    bg: 'rgb(var(--success-bg) / <alpha-value>)',
                    line: 'rgb(var(--success-line) / <alpha-value>)',
                    text: 'rgb(var(--success-text) / <alpha-value>)',
                    solid: 'rgb(var(--success-solid) / <alpha-value>)',
                },
                nav: {
                    bg: 'rgb(var(--nav-bg) / <alpha-value>)',
                    line: 'rgb(var(--nav-line) / <alpha-value>)',
                    text: 'rgb(var(--nav-text) / <alpha-value>)',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
