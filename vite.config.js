import inject from "@rollup/plugin-inject";
import { defineConfig } from "vite";

export default defineConfig(() => {
  return {
    plugins: [
      inject({
        htmx: "htmx.org",
      }),
    ],

    build: {
      // generate manifest.json in outDir
      manifest: true,
      rollupOptions: {
        // overwrite default .html entry
        input: [
          "./themes/default/css/app.scss",
          "./themes/default/js/app.js",
          "./themes/admin/css/admin.scss",
          "./themes/admin/js/admin.js",
        ],
      },
      outDir: "./public/assets/",
    },
  };
});
