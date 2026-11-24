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
                    DEFAULT: '#0B3B36',
                    ink: '#1C2B29',
                    forest: '#15594F',
                    teal: '#2F8F7C',
                    gold: '#C98A3C',
                    'gold-light': '#E2B36B',
                    'gold-dark': '#B37B2F',
                    cream: '#F7F4EF',
                    sand: '#F1EDE6',
                    line: '#E5DED2',
                    muted: '#6B7B77',
                    soft: '#7C8B87',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
