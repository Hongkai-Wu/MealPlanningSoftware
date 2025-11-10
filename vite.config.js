import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            // 确保这里指向正确的入口文件
            input: 'resources/js/app.jsx', 
            refresh: true,
        }),
        react(),
    ],
    // *** 关键修复：让 Vite 在所有网络接口上监听 ***
    server: {
        host: true, // 允许 Vite 绑定到所有接口 (0.0.0.0)，方便 XAMPP 访问
        port: 5173, // 确保使用默认端口
    },
});
