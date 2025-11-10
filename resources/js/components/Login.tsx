import React, { useState } from 'react';
import { TextField, Button, Box, Typography, Card, CardContent, CircularProgress } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import { useAuth } from './AuthContext'; // 导入认证上下文
import { API_BASE_URL } from '../config'; // 导入 API 基础 URL

/**
 * 调用后端登录 API 的核心函数
 * @param email 用户邮箱
 * @param pass 用户密码
 * @param navigate 路由导航函数
 * @param loginContext 更新 AuthContext 的登录函数
 * @param setError 设置错误信息的函数
 * @param setLoading 设置加载状态的函数
 */
function callLoginApi(
    email: string, 
    pass: string, 
    navigate: any, 
    loginContext: (username: string) => void, 
    setError: (msg: string | null) => void,
    setLoading: (loading: boolean) => void
) {
    if (email.trim() === "" || pass.trim() === "") {
        setError("邮箱和密码都不能为空。");
        return;
    }

    setLoading(true);
    setError(null);

    const requestBody = { 
        email: email, 
        password: pass 
    };
    
    // 完整的 API URL: http://127.0.0.1:20100/MealPlanningSoftware/public/api/login
    const apiLoginUrl = `${API_BASE_URL}login`;
    
    fetch(apiLoginUrl, {
        method: "POST",
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json', // 明确接受 JSON 响应
        },
        body: JSON.stringify(requestBody) 
    })
    .then(r => {
        setLoading(false);
        if (!r.ok) {
            // 如果 HTTP 状态码不是 2xx (如 401 Unauthorized)，尝试解析 JSON 错误
            return r.json().then(errorData => Promise.reject(errorData));
        }
        return r.json();
    })
    .then(data => {
        // 登录成功，后端应返回 success=true, access_token 和 user_name
        if (data.success && data.access_token) {
            // 1. 存储令牌 (关键步骤，用于后续所有认证请求)
            localStorage.setItem('authToken', data.access_token); 
            
            // 2. 更新 React 认证状态
            loginContext(data.user_name || 'User'); // 使用后端返回的用户名
            
            // 3. 导航到主应用界面
            navigate('/dashboard'); 
        } else {
            // 尽管 HTTP 状态码可能是 200，但业务逻辑失败
            setError(data.message || "登录失败：服务器返回未知错误。");
        }
    })
    .catch(errorData => {
        setLoading(false);
        let errorMessage = "无法连接到服务器或发生意外错误。";
        
        // 处理后端返回的自定义错误信息
        if (errorData.message) {
            errorMessage = errorData.message;
        } else if (errorData.errors) {
            // 处理 Laravel 验证错误 (例如邮箱格式错误)
            const validationErrors = Object.values(errorData.errors).flat();
            errorMessage = "验证失败: " + validationErrors.join(' | ');
        }
        
        setError(errorMessage);
        console.error("Fetch error or API rejection during login:", errorData);
    });
}

const Login: React.FC = () => {
    const [email, setEmail] = useState<string>("");
    const [pass, setPass] = useState<string>("");
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const navigate = useNavigate();
    // 从 AuthContext 获取登录函数
    const { login: loginContext } = useAuth(); 

    const handleLogin = (event: React.MouseEvent<HTMLButtonElement>) => {
        setError(null);
        callLoginApi(email, pass, navigate, loginContext, setError, setLoading);
    };

    return (
        <Box sx={{ maxWidth: 400, margin: 'auto', mt: 8 }}>
            <Card raised>
                <CardContent sx={{ p: 4 }}>
                    <Typography variant="h5" component="div" gutterBottom sx={{ mb: 3 }}>
                        用户登录
                    </Typography>
                    
                    {error && (
                        <Typography color="error" variant="body2" sx={{ mb: 2 }}>
                            {error}
                        </Typography>
                    )}

                    <TextField
                        label="邮箱"
                        fullWidth
                        margin="normal"
                        value={email}
                        onChange={e => setEmail(e.target.value)}
                        type="email"
                        required
                        disabled={loading}
                    />
                    <TextField
                        label="密码"
                        fullWidth
                        margin="normal"
                        value={pass}
                        onChange={e => setPass(e.target.value)}
                        type="password"
                        required
                        sx={{ mb: 3 }}
                        disabled={loading}
                    />
                    
                    <Button 
                        fullWidth
                        variant="contained" 
                        color="primary" 
                        onClick={handleLogin}
                        disabled={!email || !pass || loading}
                        sx={{ py: 1.5 }}
                        startIcon={loading ? <CircularProgress size={20} color="inherit" /> : null}
                    >
                        {loading ? '登录中...' : '登录'}
                    </Button>
                    
                    <Typography variant="body2" align="center" sx={{ mt: 3 }}>
                        还没有账户？ 
                        <Button variant="text" onClick={() => navigate('/register')} disabled={loading}>
                            去注册
                        </Button>
                    </Typography>
                </CardContent>
            </Card>
        </Box>
    );
};

export default Login;