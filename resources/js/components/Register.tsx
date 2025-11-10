import React, { useState } from 'react';
import { TextField, Button, Box, Typography, Card, CardContent } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import { API_BASE_URL } from '../config'; // 路径修正为上级目录的 config.ts

function callRegisterApi(name: string, email: string, pass: string, config: any, navigate: any, setError: (msg: string) => void) {
    // 检查字段是否为空
    if (name.trim() === "" || email.trim() === "" || pass.trim() === "") {
        setError("所有字段都不能为空。");
        return;
    }

    // 构建请求体
    const requestBody = { 
        name: name, 
        email: email, 
        password: pass 
    };
    
    // 构造完整的 API URL
    // 注意: 这里将配置项 'register' 设置为 'register'，对应后端 /api/register 路由
    const apiRegisterUrl = `${API_BASE_URL}${config.register}`;
    
    // 发送 POST 请求到 Laravel 后端 API
    fetch(apiRegisterUrl, {
        method: "POST",
        mode: "cors", 
        // 关键：Laravel 默认的 Sanctum 认证需要 Include Credentials
        credentials: "include", 
        headers: { 'Content-Type': 'application/json' }, // 发送 JSON 数据
        body: JSON.stringify(requestBody) 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 注册成功，导航到登录页面
            console.log("注册成功！请登录。");
            // 使用 replace: true 以防止用户通过后退按钮返回注册页
            navigate('/login', { replace: true }); 
        } else {
            // 注册失败，显示错误信息
            setError(data.message || "注册失败。请检查输入信息。");
        }
    })
    .catch(error => {
        setError("无法连接到服务器。请检查控制台和 API 地址。");
        console.error("Fetch error during registration:", error);
    });
}

const Register: React.FC = () => {
    const [name, setName] = useState<string>("");
    const [email, setEmail] = useState<string>("");
    const [pass, setPass] = useState<string>("");
    const [error, setError] = useState<string | null>(null);
    const navigate = useNavigate();

    // 假设后端注册 API 路径为 /api/register
    const config = { register: "register" }; 

    const handleRegister = (event: React.MouseEvent<HTMLButtonElement>) => {
        setError(null);
        callRegisterApi(name, email, pass, config, navigate, setError);
    };

    return (
        <Box sx={{ maxWidth: 400, margin: 'auto', mt: 8 }}>
            <Card raised>
                <CardContent sx={{ p: 4 }}>
                    <Typography variant="h5" component="div" gutterBottom sx={{ mb: 3 }}>
                        创建账户 (Meal Planner)
                    </Typography>
                    
                    {error && (
                        <Typography color="error" variant="body2" sx={{ mb: 2 }}>
                            {error}
                        </Typography>
                    )}

                    <TextField
                        label="用户名"
                        fullWidth
                        margin="normal"
                        value={name}
                        onChange={e => setName(e.target.value)}
                        required
                    />
                    <TextField
                        label="邮箱"
                        fullWidth
                        margin="normal"
                        value={email}
                        onChange={e => setEmail(e.target.value)}
                        type="email"
                        required
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
                    />
                    
                    <Button 
                        fullWidth
                        variant="contained" 
                        color="secondary" 
                        onClick={handleRegister}
                        disabled={!name || !email || !pass}
                        sx={{ py: 1.5 }}
                    >
                        注册
                    </Button>
                    
                    <Typography variant="body2" align="center" sx={{ mt: 3 }}>
                        已有账户？ 
                        <Button variant="text" onClick={() => navigate('/login')}>
                            去登录
                        </Button>
                    </Typography>
                </CardContent>
            </Card>
        </Box>
    );
};

export default Register;