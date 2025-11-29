import { defineConfig } from 'vite';

export default defineConfig({
    root: './public', // Установите корень проекта в текущую директорию
    build: {
        outDir: './build', // Укажите правильный путь к выходной директории
        manifest: true,
        rollupOptions: {
            input: './assets/app.js', // Укажите правильный путь к входному файлу
        },
    }
});
