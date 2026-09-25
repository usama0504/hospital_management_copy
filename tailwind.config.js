import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue", // 👈 Yeh line zaroori thi jo add kar di hai
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                heading: [
                    '"Plus Jakarta Sans"',
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            colors: {
                brand: {
                    50: "#eefbfa",
                    100: "#d4f3f0",
                    200: "#aae6e0",
                    300: "#75d2c9",
                    400: "#3fb6ab",
                    500: "#1f9a8f",
                    600: "#167c74",
                    700: "#15645f",
                    800: "#154f4c",
                    900: "#0e2f3a",
                    950: "#0a2530",
                },
            },
        },
    },

    plugins: [forms],
};
