import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import rtlcss from "rtlcss";
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/adminlte.css",
                "resources/css/adminlte-rtl.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        {
            name: "rtlcss",
            transform(code, id) {
                if (id.includes("adminlte-rtl.css")) {
                    return rtlcss.process(code);
                }
            },
        },
    ],
      build: {
        manifest: true, 
        outDir: 'public/build', 
    },
});
