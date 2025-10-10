import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export interface User {
    id: number;
    name: string;
    email: string;
    phone?: string;
    image?: string;
    user_only?: {
        user_name: string;
        user_password: string;
    };
}

export interface LoginCredentials {
    email: string;
    password: string;
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null);
    const token = ref<string | null>(localStorage.getItem('auth_token'));
    const isAuthenticated = ref<boolean>(!!token.value);
    const permissions = ref<string[]>([]); // Añade esta línea

    const setUser = (userData: User) => {
        user.value = userData;
        
        // Actualizar también en el storage correspondiente
        const storage = localStorage.getItem('auth_token') ? localStorage : sessionStorage;
        storage.setItem('user', JSON.stringify(userData));
    };

    // Método para actualizar datos parciales del usuario
    const updateUser = (updates: Partial<User>) => {
        if (user.value) {
            user.value = { ...user.value, ...updates };
            
            // Actualizar también en el storage correspondiente
            const storage = localStorage.getItem('auth_token') ? localStorage : sessionStorage;
            storage.setItem('user', JSON.stringify(user.value));
        }
    };

    const logout = () => {
        // Limpiar store
        token.value = null;
        user.value = null;
        isAuthenticated.value = false;
        permissions.value = [];
        
        // Limpiar localStorage
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        sessionStorage.removeItem('auth_token');
        sessionStorage.removeItem('user');
        sessionStorage.removeItem('permissions');
        
        // Limpiar headers de axios
        delete axios.defaults.headers.common['Authorization'];
    };

    const setAuth = (authToken: string, userData: User, remember: boolean = false) => {
        // Guardar en el store
        token.value = authToken;
        user.value = userData;
        isAuthenticated.value = true;
        
        // Guardar en storage según la opción remember
        if (remember) {
            localStorage.setItem('auth_token', authToken);
            localStorage.setItem('user', JSON.stringify(userData));
            // Limpiar sessionStorage si existe
            sessionStorage.removeItem('auth_token');
            sessionStorage.removeItem('user');
        } else {
            // Usar sessionStorage para sesiones de navegador
            sessionStorage.setItem('auth_token', authToken);
            sessionStorage.setItem('user', JSON.stringify(userData));
            // Limpiar localStorage si existe
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
        }
        
        // Configurar axios
        axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
        
        // Los permisos se establecerán explícitamente desde la respuesta del login
    };

    const checkAuth = () => {
        // Buscar primero en localStorage, luego en sessionStorage
        let storedToken = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        let storedUser = localStorage.getItem('user') || sessionStorage.getItem('user');
        let storedPermissions = localStorage.getItem('permissions') || sessionStorage.getItem('permissions');
        
        if (storedToken && storedUser) {
            token.value = storedToken;
            user.value = JSON.parse(storedUser);
            isAuthenticated.value = true;
            
            // Cargar permisos si existen
            if (storedPermissions) {
                permissions.value = JSON.parse(storedPermissions);
            }
            
            axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`;
        } else {
            // Usar la función logout ya definida
            logout();
        }
    };

    const can = (permission: string): boolean => {
        return permissions.value.includes(permission);
    };

    const login = async (credentials: LoginCredentials, remember: boolean = false) => {
        try {
            const response = await axios.post('/api/login', credentials);
            
            if (response.status === 200) {
                const { token: authToken, user: userData, permissions: userPermissions } = response.data;
                
                // Guardar token, usuario y permisos
                setAuth(authToken, userData, remember);
                
                // Establecer los permisos explícitamente
                permissions.value = userPermissions || [];
                
                // También guardar permisos en el almacenamiento
                if (remember) {
                    localStorage.setItem('permissions', JSON.stringify(userPermissions));
                    sessionStorage.removeItem('permissions');
                } else {
                    sessionStorage.setItem('permissions', JSON.stringify(userPermissions));
                    localStorage.removeItem('permissions');
                }
                
                return response;
            }
        } catch (error: any) {
            logout(); // Limpiar en caso de error
            throw error;
        }
    };

    // Configurar axios para usar el token por defecto (si existe)
    if (token.value) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    }

    // Verificar autenticación al inicializar el store
    checkAuth();
    
    const isAdmin = (() => {
        if (!user.value || !user.value.roles) return false;
        
        return user.value.roles.some(role => 
            role.name === 'admin' || 
            role.name === 'Admin' ||
            role.name === 'ADMIN'
        );
    });

    return {
        user,
        token,
        isAuthenticated,
        permissions,
        login,
        logout,
        checkAuth,
        setAuth,
        can,
        setUser,
        updateUser,
        isAdmin 
    };
});