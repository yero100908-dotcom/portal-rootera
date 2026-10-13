import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        screens: {
            'xs': '375px',
            ...defaultTheme.screens,
        },
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            minHeight: {
                'touch': '44px',
                'touch-lg': '48px',
            },
            minWidth: {
                'touch': '44px',
                'touch-lg': '48px',
            },
            fontSize: {
                'fluid-hero': 'clamp(1.875rem, 4vw + 1rem, 3.75rem)',
                'fluid-title': 'clamp(1.35rem, 2.5vw + 0.5rem, 2.25rem)',
                'fluid-body': 'clamp(0.875rem, 0.8vw + 0.5rem, 1.125rem)',
            },
            spacing: {
                'safe-top': 'env(safe-area-inset-top)',
                'safe-bottom': 'env(safe-area-inset-bottom)',
                'safe-left': 'env(safe-area-inset-left)',
                'safe-right': 'env(safe-area-inset-right)',
            },
        },
    },

    plugins: [forms],
};
