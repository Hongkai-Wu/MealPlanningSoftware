import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { Box, Container, AppBar, Toolbar, Typography, Button, CircularProgress } from '@mui/material';
import Login from './components/Login';
import Register from './components/Register';
import Dashboard from './components/Dashboard'; // 假设您有一个 Dashboard 组件
import { AuthProvider, useAuth } from './components/AuthContext';
import NavMenu from './components/NavMenu'; // 假设您有一个 NavMenu 组件

// =================================================================
// 1. ProtectedRoute 组件
// 用于保护需要用户登录才能访问的路由
// =================================================================
const ProtectedRoute: React.FC<{ children: React.ReactElement }> = ({ children }) => {
    const { isLoggedIn, isLoading } = useAuth();
    const location = useLocation();

    // 认证上下文正在初始化时
    if (isLoading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
                <CircularProgress />
                <Typography variant="h6" sx={{ ml: 2 }}>正在验证登录状态...</Typography>
            </Box>
        );
    }

    // 如果未登录，重定向到登录页面，并带上当前路径作为 state，以便登录后返回
    if (!isLoggedIn) {
        return <Navigate to="/login" state={{ from: location }} replace />;
    }

    // 已登录，渲染子组件 (即受保护页面)
    return children;
};

// =================================================================
// 2. 主应用组件 App
// =================================================================
const AppContent: React.FC = () => {
    const { isLoggedIn, userName, logout } = useAuth();
    const navigate = useNavigate();

    return (
        <Box sx={{ flexGrow: 1 }}>
            {/* 顶部导航栏 */}
            <AppBar position="static" color="primary">
                <Toolbar>
                    <Typography 
                        variant="h6" 
                        component="div" 
                        sx={{ flexGrow: 1, cursor: 'pointer' }}
                        onClick={() => navigate(isLoggedIn ? '/dashboard' : '/')}
                    >
                        React & Laravel 认证系统
                    </Typography>

                    <NavMenu 
                        isLoggedIn={isLoggedIn} 
                        userName={userName} 
                        onLogout={logout} 
                        onLogin={() => navigate('/login')}
                        onRegister={() => navigate('/register')}
                    />

                </Toolbar>
            </AppBar>

            {/* 主要内容区域 */}
            <Container sx={{ mt: 4 }}>
                <Routes>
                    {/* 默认首页 (可跳转到登录) */}
                    <Route path="/" element={<Home />} />
                    
                    {/* 认证路由 */}
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />

                    {/* 受保护路由 - 只有登录后才能访问 */}
                    <Route 
                        path="/dashboard" 
                        element={
                            <ProtectedRoute>
                                <Dashboard />
                            </ProtectedRoute>
                        } 
                    />

                    {/* 404 页面 */}
                    <Route path="*" element={<Typography variant="h4" align="center" mt={5}>404 - 页面未找到</Typography>} />
                </Routes>
            </Container>
        </Box>
    );
};

// 简单的首页组件
const Home: React.FC = () => (
    <Box sx={{ textAlign: 'center', mt: 10 }}>
        <Typography variant="h3" gutterBottom>
            欢迎使用 React & Laravel 全栈认证模板
        </Typography>
        <Typography variant="h6" color="text.secondary">
            请登录以访问仪表板，或注册新账户。
        </Typography>
    </Box>
);

// =================================================================
// 3. 根组件：包裹 Router 和 AuthProvider
// =================================================================
const App: React.FC = () => (
    <Router>
        {/* AuthProvider 必须包裹所有使用 useAuth() 的组件 */}
        <AuthProvider>
            <AppContent />
        </AuthProvider>
    </Router>
);

export default App;