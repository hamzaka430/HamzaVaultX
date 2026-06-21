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
                primary: {
                    DEFAULT: '#3ecf8e',
                    deep: '#24b47e',
                    soft: '#4ade80',
                },
                ink: {
                    DEFAULT: '#171717',
                    secondary: '#212121',
                    mute: '#707070',
                    'mute-2': '#9a9a9a',
                    faint: '#b2b2b2',
                },
                on: {
                    primary: '#171717',
                    dark: '#ffffff',
                },
                canvas: {
                    DEFAULT: '#ffffff',
                    soft: '#fafafa',
                    night: '#1c1c1c',
                    'night-soft': '#202020',
                },
                hairline: {
                    DEFAULT: '#dfdfdf',
                    strong: '#c7c7c7',
                    cool: '#ededed',
                    'cool-2': '#efefef',
                    'cool-3': '#d4d4d4',
                },
                accent: {
                    purple: '#6b01c2',
                    violet: '#644fc1',
                    'purple-soft': '#eddbf9',
                    yellow: '#ffdb13',
                    tomato: '#ff2201',
                    pink: '#c7007e',
                    indigo: '#054cff',
                    crimson: '#e2005a',
                }
            },
            fontFamily: {
                sans: ['Inter', 'Circular', "'Helvetica Neue'", 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                mono: ['ui-monospace', 'Menlo', 'Monaco', 'Consolas', "'Liberation Mono'", ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                'display-xxl': ['64px', { lineHeight: '1.1', letterSpacing: '-1.92px', fontWeight: '500' }],
                'display-xl': ['48px', { lineHeight: '1.1', letterSpacing: '-1.44px', fontWeight: '500' }],
                'display-lg': ['36px', { lineHeight: '1.15', letterSpacing: '-0.72px', fontWeight: '500' }],
                'display-md': ['28px', { lineHeight: '1.2', letterSpacing: '-0.42px', fontWeight: '500' }],
                'heading-lg': ['22px', { lineHeight: '1.2', letterSpacing: '0px', fontWeight: '500' }],
                'heading-md': ['18px', { lineHeight: '1.4', letterSpacing: '0px', fontWeight: '500' }],
                'body-lg': ['18px', { lineHeight: '1.55', letterSpacing: '0px', fontWeight: '400' }],
                'body-md': ['16px', { lineHeight: '1.5', letterSpacing: '0px', fontWeight: '400' }],
                'button-md': ['14px', { lineHeight: '1.0', letterSpacing: '0px', fontWeight: '500' }],
                'caption': ['13px', { lineHeight: '1.45', letterSpacing: '0px', fontWeight: '400' }],
                'micro': ['12px', { lineHeight: '1.45', letterSpacing: '0px', fontWeight: '400' }],
                'code': ['14px', { lineHeight: '1.5', letterSpacing: '0px', fontWeight: '400' }],
            },
            borderRadius: {
                xs: '4px',
                sm: '6px',
                md: '8px',
                lg: '12px',
                xl: '16px',
                full: '9999px',
            },
            spacing: {
                xxs: '2px',
                xs: '4px',
                sm: '8px',
                md: '12px',
                lg: '16px',
                xl: '24px',
                xxl: '32px',
                huge: '64px',
            },
            boxShadow: {
                'elevation-1': '0 1px 3px rgba(0,0,0,0.06)',
                'elevation-2': '0 8px 24px rgba(0,0,0,0.08)',
                'elevation-3': '0 16px 48px rgba(0,0,0,0.12)',
            },
            screens: {
                'xs': '475px',
                'wide': '1440px',
            },
        },
    },

    plugins: [forms],
};
