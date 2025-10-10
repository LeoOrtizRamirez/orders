import { defineConfig } from "vite";
import { fileURLToPath, URL } from "node:url";
import path from "node:path";
import vue from "@vitejs/plugin-vue";
import VueI18nPlugin from "@intlify/unplugin-vue-i18n/vite";
import laravel from "laravel-vite-plugin";

// Configuración de Vite
export default defineConfig({
    // Plugins
    plugins: [
        // Plugin de Laravel Vite para integración con Laravel
        laravel({
            input: ["resources/js/src/main.ts"],
            refresh: true,
        }),
        
        // Plugin de Vue.js
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                },
            },
        }),
        
        // Plugin de internacionalización (i18n)
        VueI18nPlugin({
            include: path.resolve("resources/js/src/locales/**"),
        }),
    ],
    
    // Resolución de módulos
    resolve: {
        alias: {
            // Alias para imports absolutos
            "@": fileURLToPath(new URL("./resources/js/src", import.meta.url)),
            // Alias adicionales si son necesarios
            "~bootstrap": path.resolve(__dirname, "node_modules/bootstrap"),
        },
        extensions: [".js", ".ts", ".vue", ".json"],
    },
    
    // Optimización de dependencias
    optimizeDeps: {
        include: ["quill", "lodash", "axios"],
        exclude: [],
    },
    
    // Configuración del servidor de desarrollo
    server: {
        host: "localhost",
        port: 3000,
        strictPort: true,
        open: false,
        cors: true,
        proxy: {
            // Proxy para API de Laravel
            "/api": {
                target: "http://localhost:8000",
                changeOrigin: true,
                secure: false,
                ws: true,
            },
            // Proxy para assets de Laravel
            "/storage": {
                target: "http://localhost:8000",
                changeOrigin: true,
                secure: false,
            },
        },
        hmr: {
            host: "localhost",
        },
    },
    
    // Configuración del build
    build: {
        outDir: "public/build",
        emptyOutDir: true,
        manifest: true,
        sourcemap: process.env.NODE_ENV !== "production",
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["vue", "vue-router", "pinia"],
                    quill: ["quill"],
                    chartjs: ["chart.js"],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    
    // Previsualización
    preview: {
        port: 3001,
        strictPort: true,
    },
    
    // Variables de entorno
    envDir: "./",
    envPrefix: "VITE_",
    
    // CSS
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `
                    @import "resources/js/src/assets/scss/variables";
                    @import "resources/js/src/assets/scss/mixins";
                `,
            },
        },
    },
});