/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                'sans': ['Onest', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                'onest': ['Onest', 'sans-serif'],
            },
            fontSize: {
                'sf-nav': ['18px', {
                    lineHeight: '20px',
                }],
            },
            colors: {
                'sf-blue': '#2055dd',
                'nav-text': '#63637c',
                'nav-text-hover': '#2055d5',
                'nav-hover': '#3677B3',
            },
        },
    },
    plugins: [],
};
