import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { useNavigate } from 'react-router-dom';
import { API_BASE_URL } from '../config';

// 1. 定义上下文的类型
interface AuthContextType {
    isLoggedIn: boolean;
    userName: string | null;
    login: (name: string) => void;
    logout: () => void;
    isLoading: boolean; // 用于表示初始化检查是否完成
}

// 2. 创建上下文，并提供默认值
const AuthContext = createContext<AuthContextType | undefined>(undefined);

// 3. 定义 AuthProvider 组件的属性
interface AuthProviderProps {
    children: ReactNode;
}

/**
 * AuthProvider 组件：提供认证状态和操作的全局上下文
 */
export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [userName, setUserName] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(true); // 初始时设置为加载中

    const navigate = useNavigate();

    // 4. 初始化检查：检查本地存储中的令牌是否有效
    useEffect(() => {
        const token = localStorage.getItem('authToken');
        
        if (token) {
            // 如果存在令牌，我们尝试向后端请求用户信息来验证令牌有效性
            const checkAuth = async () => {
                try {
                    const response = await fetch(`${API_BASE_URL}user`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`, // 发送令牌进行验证
                        },
                    });

                    if (response.ok) {
                        const user = await response.json();
                        setIsLoggedIn(true);
                        // Laravel Sanctum 默认返回 user 对象，我们尝试获取 name 或 email 作为用户名
                        setUserName(user.name || user.email || '用户'); 
                    } else {
                        // 令牌无效或过期，清除本地存储
                        console.log("Token check failed. Clearing local storage.");
                        localStorage.removeItem('authToken');
                        setIsLoggedIn(false);
                    }
                } catch (error) {
                    console.error("Auth check error:", error);
                    localStorage.removeItem('authToken');
                    setIsLoggedIn(false);
                } finally {
                    setIsLoading(false); // 检查完成
                }
            };
            checkAuth();
        } else {
            setIsLoading(false); // 没有令牌，直接完成检查
        }
    }, []); // 仅在组件挂载时运行一次

    // 5. 登录操作：在 Login.tsx 成功调用 API 后调用
    const login = (name: string) => {
        setIsLoggedIn(true);
        setUserName(name);
        // 注意：令牌存储在 Login.tsx 中完成
    };

    // 6. 登出操作：清除状态和令牌
    const logout = async () => {
        const token = localStorage.getItem('authToken');
        if (token) {
            try {
                // 可选：向后端发送登出请求，使服务器端的令牌失效
                await fetch(`${API_BASE_URL}logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    },
                });
            } catch (error) {
                console.error("Logout API call failed, but clearing client state anyway.", error);
            }
        }
        
        localStorage.removeItem('authToken');
        setIsLoggedIn(false);
        setUserName(null);
        navigate('/login'); // 重定向到登录页面
    };

    const value = {
        isLoggedIn,
        userName,
        login,
        logout,
        isLoading,
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

// 7. 自定义 Hook：方便在任何组件中使用认证上下文
export const useAuth = () => {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth 必须在 AuthProvider 内部使用');
    }
    return context;
};