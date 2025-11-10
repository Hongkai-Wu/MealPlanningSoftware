import React, { useState, useEffect, useMemo, useCallback } from 'react';
// 修正：从 'firebase/app' 中导入必要的函数
import { initializeApp, getApps, getApp } from 'firebase/app'; 
import { getAuth, signInWithCustomToken, signInAnonymously } from 'firebase/auth';
import { getFirestore, doc, setDoc, onSnapshot } from 'firebase/firestore';
import { Loader2 } from 'lucide-react'; // Placeholder for simple loading indicator

// --- Firestore/Firebase Configuration ---
// Note: __app_id, __firebase_config, and __initial_auth_token are provided globally in this environment.
const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';
const firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : {};

// Style utility functions (assuming Tailwind is available)

const getBmiCategory = (bmi) => {
    if (bmi < 18.5) return { label: '体重过轻 (Underweight)', color: 'bg-yellow-400 text-yellow-800' };
    if (bmi >= 18.5 && bmi < 24) return { label: '健康体重 (Normal Weight)', color: 'bg-green-400 text-green-800' };
    if (bmi >= 24 && bmi < 28) return { label: '超重 (Overweight)', color: 'bg-orange-400 text-orange-800' };
    return { label: '肥胖 (Obese)', color: 'bg-red-500 text-white' };
};

// Main Dashboard Component
export default function Dashboard({ user }) {
    // Firebase State
    const [db, setDb] = useState(null);
    const [auth, setAuth] = useState(null);
    const [isFirebaseReady, setIsFirebaseReady] = useState(false);
    const [userId, setUserId] = useState(null);

    // Health Data State
    const [heightCm, setHeightCm] = useState(0);
    const [weightKg, setWeightKg] = useState(0);
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);
    const [message, setMessage] = useState('');

    // --- 1. Firebase Initialization and Authentication ---
    useEffect(() => {
        // Initialize Firebase only once
        if (Object.keys(firebaseConfig).length === 0 || isFirebaseReady) {
            if (!user) {
                // If the user object is not passed from the main App, we can't proceed with data ops.
                console.error("User object not provided to Dashboard component.");
                setIsLoading(false);
            }
            return;
        }

        try {
            let app;
            const appName = `app-${appId}`;
            
            // 修正后的初始化逻辑：直接使用导入的 initializeApp 和 getApps/getApp
            // 检查应用是否已初始化，以避免重复初始化错误
            const existingApps = getApps();
            if (existingApps.length > 0 && existingApps.find(a => a.name === appName)) {
                 app = getApp(appName);
            } else {
                 // 使用 initializeApp 进行初始化
                 app = initializeApp(firebaseConfig, appName);
            }

            const currentAuth = getAuth(app);
            const currentDb = getFirestore(app);

            setAuth(currentAuth);
            setDb(currentDb);

            // Handle Authentication using the provided token (mandatory for Canvas environment)
            const authenticate = async () => {
                try {
                    if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
                        await signInWithCustomToken(currentAuth, __initial_auth_token);
                    } else {
                        // Fallback to anonymous sign-in if no token is provided
                        await signInAnonymously(currentAuth);
                    }
                    // Get the final user ID after successful sign-in
                    const finalUser = currentAuth.currentUser;
                    if (finalUser) {
                        setUserId(finalUser.uid);
                        console.log("Firebase Auth Ready. User UID:", finalUser.uid);
                    } else {
                        throw new Error("Authentication failed: No user found after sign-in.");
                    }
                } catch (error) {
                    console.error("Firebase Authentication Error:", error);
                    setMessage("认证失败，无法加载或保存数据。");
                } finally {
                    setIsFirebaseReady(true);
                }
            };

            authenticate();

        } catch (e) {
            console.error("Firebase initialization failed:", e);
            setMessage("Firebase 初始化失败。");
            setIsLoading(false);
        }
    }, [user]);

    // --- 2. Data Fetching (onSnapshot) ---
    useEffect(() => {
        if (!isFirebaseReady || !db || !userId) {
            // Wait for Firebase to be ready and userId to be set
            return;
        }

        // Define the document path: /artifacts/{appId}/users/{userId}/healthData/profile
        const docRef = doc(db, 
            'artifacts', appId, 
            'users', userId, 
            'healthData', 'profile'
        );

        console.log("Setting up snapshot listener for:", docRef.path);
        
        // Listen for real-time changes
        const unsubscribe = onSnapshot(docRef, (docSnap) => {
            if (docSnap.exists()) {
                const data = docSnap.data();
                console.log("Data loaded:", data);
                // Load saved data into state
                setHeightCm(data.heightCm || 0);
                setWeightKg(data.weightKg || 0);
                setMessage('数据已加载。');
            } else {
                console.log("No data found for this user, using defaults.");
                setMessage('新用户，请填写数据。');
            }
            setIsLoading(false); // Stop loading once initial data is fetched or confirmed missing
        }, (error) => {
            console.error("Firestore onSnapshot error:", error);
            setMessage(`数据加载失败: ${error.code}`);
            setIsLoading(false);
        });

        // Cleanup the listener on component unmount
        return () => unsubscribe();
    }, [isFirebaseReady, db, userId]);

    // --- 3. BMI Calculation Logic ---
    // Memoize the BMI calculation for efficiency
    const bmiResult = useMemo(() => {
        const heightM = heightCm / 100; // Convert cm to meters
        if (heightM <= 0 || weightKg <= 0) {
            return { value: 0, category: { label: '输入无效', color: 'bg-gray-200 text-gray-700' } };
        }
        
        // BMI = weight (kg) / [height (m)]^2
        const bmi = weightKg / (heightM * heightM);
        const roundedBmi = parseFloat(bmi.toFixed(1));
        
        return {
            value: roundedBmi,
            category: getBmiCategory(roundedBmi)
        };
    }, [heightCm, weightKg]);

    // --- 4. Data Saving Function ---
    const handleSave = useCallback(async () => {
        if (!db || !userId) {
            setMessage('错误：Firebase 或用户 ID 未准备好。');
            return;
        }
        if (heightCm <= 0 || weightKg <= 0) {
            setMessage('请确保身高和体重输入有效值。');
            return;
        }

        setIsSaving(true);
        setMessage('正在保存数据...');

        // Define the document path: /artifacts/{appId}/users/{userId}/healthData/profile
        const docRef = doc(db, 
            'artifacts', appId, 
            'users', userId, 
            'healthData', 'profile'
        );

        const dataToSave = {
            userId: userId,
            heightCm: heightCm,
            weightKg: weightKg,
            bmi: bmiResult.value,
            category: bmiResult.category.label,
            updatedAt: new Date().toISOString()
        };

        try {
            await setDoc(docRef, dataToSave, { merge: true });
            setMessage('数据已成功保存！');
        } catch (error) {
            console.error("Error saving document: ", error);
            setMessage(`保存失败: ${error.message}`);
        } finally {
            setIsSaving(false);
            // Clear message after a brief display
            setTimeout(() => setMessage(''), 3000);
        }
    }, [db, userId, heightCm, weightKg, bmiResult]);


    // Loading State UI
    if (isLoading) {
        return (
            <div className="flex justify-center items-center h-64">
                <Loader2 className="h-8 w-8 animate-spin text-indigo-600" />
                <p className="ml-3 text-lg font-medium text-gray-600">正在加载个人资料...</p>
            </div>
        );
    }
    
    // User ID display for collaborative purposes (MANDATORY)
    const displayUserId = userId || 'N/A';
    
    // Main Content UI
    return (
        <div className="p-4 md:p-8 bg-gray-50 min-h-screen">
            <h1 className="text-3xl font-extrabold text-gray-900 mb-6 border-b pb-2">
                健康仪表板
            </h1>
            
            <p className="mb-6 text-sm text-gray-600">
                **您的用户ID (User ID):** <span className="font-mono bg-gray-200 p-1 rounded text-xs">{displayUserId}</span>
                <span className="ml-4 text-xs text-red-500">请勿与他人共享您的用户ID。</span>
            </p>

            {message && (
                <div className={`p-3 mb-4 rounded-lg text-sm font-medium ${message.includes('失败') ? 'bg-red-100 text-red-700' : 'bg-indigo-100 text-indigo-700'}`}>
                    {message}
                </div>
            )}

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {/* --- Input Form Card --- */}
                <div className="lg:col-span-2 bg-white p-6 shadow-xl rounded-xl">
                    <h2 className="text-2xl font-semibold text-gray-800 mb-6">我的身体数据</h2>

                    {/* Height Input */}
                    <div className="mb-5">
                        <label htmlFor="height" className="block text-sm font-medium text-gray-700 mb-2">
                            身高 (Height) <span className="text-indigo-600 font-bold">(cm)</span>
                        </label>
                        <input
                            id="height"
                            type="number"
                            min="1"
                            placeholder="例如: 175"
                            value={heightCm === 0 ? '' : heightCm}
                            onChange={(e) => setHeightCm(Number(e.target.value))}
                            className="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        />
                    </div>

                    {/* Weight Input */}
                    <div className="mb-6">
                        <label htmlFor="weight" className="block text-sm font-medium text-gray-700 mb-2">
                            当前体重 (Current Weight) <span className="text-indigo-600 font-bold">(kg)</span>
                        </label>
                        <input
                            id="weight"
                            type="number"
                            min="1"
                            placeholder="例如: 70"
                            value={weightKg === 0 ? '' : weightKg}
                            onChange={(e) => setWeightKg(Number(e.target.value))}
                            className="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                        />
                    </div>
                    
                    {/* Save Button */}
                    <button
                        onClick={handleSave}
                        disabled={isSaving || heightCm <= 0 || weightKg <= 0}
                        className="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-lg font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isSaving ? (
                            <div className="flex items-center">
                                <Loader2 className="h-5 w-5 mr-2 animate-spin" /> 正在保存...
                            </div>
                        ) : (
                            'Save Data (保存数据到云端)'
                        )}
                    </button>
                    <p className="mt-2 text-xs text-gray-500 text-center">数据将自动加载到下次访问。</p>
                </div>

                {/* --- BMI Result Card --- */}
                <div className={`lg:col-span-1 p-6 rounded-xl shadow-xl transition duration-300 transform hover:scale-[1.02] ${bmiResult.category.color}`}>
                    <h2 className={`text-2xl font-bold mb-4 ${bmiResult.category.color.includes('white') ? 'text-white' : 'text-gray-800'}`}>
                        身体质量指数 (BMI)
                    </h2>
                    
                    <div className={`text-6xl font-extrabold mb-4 p-4 rounded-xl bg-white bg-opacity-70 text-center shadow-inner`}>
                        {bmiResult.value > 0 ? bmiResult.value : '--'}
                    </div>

                    <div className="text-lg font-semibold mt-4">
                        <span className="font-bold">分类:</span> 
                        <span className={`ml-2 px-3 py-1 rounded-full text-sm font-bold ${bmiResult.category.color.replace('bg-', 'bg-')}`}>
                           {bmiResult.category.label}
                        </span>
                    </div>

                    <p className={`mt-3 text-sm italic ${bmiResult.category.color.includes('white') ? 'text-gray-100' : 'text-gray-600'}`}>
                        {bmiResult.value > 0 
                            ? (bmiResult.value < 18.5 ? '目标：增加健康体重。' : 
                               (bmiResult.value < 24 ? '状态良好，请保持。' : 
                               (bmiResult.value < 28 ? '注意体重管理，适度运动。' : '请咨询医生进行健康管理。')))
                            : '请输入身高和体重进行计算。'}
                    </p>
                </div>
            </div>
            
            {/* Additional Sections Placeholder (Future Feature) */}
            <div className="mt-10">
                <h2 className="text-2xl font-semibold text-gray-800 mb-4">未来的健康计划</h2>
                <div className="bg-white p-6 rounded-xl shadow-lg">
                    <p className="text-gray-500">
                        在这里，我们可以添加每日营养追踪、运动目标设置和历史数据图表等功能。
                    </p>
                </div>
            </div>
        </div>
    );
}
