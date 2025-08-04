import sitemap from "@astrojs/sitemap";

import tailwind from "@astrojs/tailwind";
import { defineConfig } from "astro/config";
import compress from "astro-compress";

export default defineConfig({
	output: "static",
	trailingSlash: "always",
	site: "https://www.notakrista.com",
	build: {
		assetsPrefix: "./"
	},

	// Single page, no prefetch needed
	prefetch: false,

	integrations: [
		tailwind(),
		sitemap(),
		compress({
			CSS: true,
			SVG: false,
			Image: false,
			HTML: {
				"html-minifier-terser": {
					collapseWhitespace: true,
					// collapseInlineTagWhitespace: true, // It breaks display-inline / flex-inline text
					minifyCSS: true,
					minifyJS: true,
					removeComments: true,
					removeEmptyAttributes: true,
					// removeEmptyElements: true, // It removes sometimes SVGs
					removeRedundantAttributes: true,
				},
			},
			JavaScript: {
				terser: {
					compress: {
						drop_console: true,
						drop_debugger: true,
					},
				},
			},
		}),
	],
});
