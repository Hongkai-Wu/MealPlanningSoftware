import './bootstrap'; // 导入 Laravel/Bootstrap 依赖
import '../css/app.css'; // 导入全局 CSS 样式

import React from 'react';
import ReactDOM from 'react-dom/client';

// 导入主应用组件和所需的路由/上下文
// 🚨 关键修改: 从 components 目录导入主组件，它的路径是相对于当前 app.jsx 文件的
import App from './components/App'; 
import { BrowserRouter } from 'react-router-dom';
import { AuthProvider } from '../src/AuthContext'; 
import { CssBaseline } from '@mui/material';

// ----------------------------------------------------
// Laravel/Vite 引导代码
// ----------------------------------------------------

// 获取应用挂载点 (对应于 resources/views/welcome.blade.php 中的 <div id="app"></div>)
const appElement = document.getElementById('app');

if (appElement) {
    // 使用 ReactDOM 创建根并渲染应用
    // 在 .jsx 文件中使用 TypeScript/TSX 编写的组件可能会触发 @ts-ignore
    // 但在运行环境中通常可以正常工作。
    // @ts-ignore
    ReactDOM.createRoot(appElement).render(
        <React.StrictMode>
            <BrowserRouter>
                <AuthProvider>
                    <CssBaseline />
                    <App />
                </AuthProvider>
            </BrowserRouter>
        </React.StrictMode>
    );
}
