import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import viteCompression from "vite-plugin-compression";

export default defineConfig({
    plugins: [
        viteCompression({
            algorithm: "brotliCompress",
            ext: ".br",
            threshold: 10240,
        }),
        viteCompression({
            algorithm: "gzip",
            ext: ".gz",
        }),
        laravel({
            input: "resources/js/app.js",
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
    ],
    build: {
        manifest: "manifest.json",
        outDir: "public/build",
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
