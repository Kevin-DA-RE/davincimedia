import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { quasar, transformAssetUrls } from "@quasar/vite-plugin";

export default defineConfig({
    base: "/", // <-- ajoute cette ligne ici
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        quasar(),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
            '@img': '/src/img',
        },
    },
    build: {
        outDir: "public/build", // dossier de sortie
        emptyOutDir: true, // nettoie le dossier avant chaque build
        sourcemap: false, // désactive les sourcemaps en production
    },
});
