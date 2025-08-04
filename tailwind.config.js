/** @type {import('tailwindcss').Config} */
export default {
	content: ["./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}"],
	darkMode: "class",
	theme: {
		extend: {
			colors: {
				primary: "#F22B02", // Amarillo cálido
				secondary: "#FABD2F", // Rojo cálido
				accent: "#B8BB26", // Verde oliva
				background: "#282828", // Marrón grisáceo oscuro
				surface: "#3C3836", // Marrón medio
				text: "#EBDBB2", // Beige cálido
				muted: "#928374", // Gris cálido
			},
		},
	},
};
