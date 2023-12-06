/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    important: "#ohdear",
    content: ["./resources/**/*.blade.php"],
    theme: {
        extend: {
            colors: {
                brand: "#FF3900",
            },
        },
    },
    plugins: [],
    corePlugins: {
        preflight: false,
    },
};
