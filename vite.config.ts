import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Freebuff manages the dev server and always injects PORT.
// HMR stays disabled because Freebuff requires it off.
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: Number(process.env.PORT) || 5173,
    hmr: false,
    // The preview is served through a managed proxy host, which Vite would
    // otherwise reject with "Blocked request" unless it is allow-listed.
    allowedHosts: true,
  },
  preview: {
    host: '0.0.0.0',
    port: Number(process.env.PORT) || 4173,
    allowedHosts: true,
  },
  build: {
    outDir: 'dist',
    target: 'es2020',
    cssCodeSplit: true,
    reportCompressedSize: false,
    chunkSizeWarningLimit: 600,
    rollupOptions: {
      output: {
        // Split the framework out of the app bundle so it caches independently
        // of content changes.
        manualChunks: {
          'vendor-react': ['react', 'react-dom', 'react-router-dom'],
        },
      },
    },
  },
});
