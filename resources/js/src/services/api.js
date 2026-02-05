import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_APP_URL || 'https://dev.blupage.co', // URL de tu backend Laravel
    withCredentials: true,
});

// Interceptor para incluir el token en las requests
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        const getCookie = (name) => {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        };

        const xsrfToken = getCookie('XSRF-TOKEN');

        if (xsrfToken) {
            config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken);
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar respuestas de error
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401 || error.response?.status === 419) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            localStorage.removeItem('permissions');
            sessionStorage.removeItem('auth_token');
            sessionStorage.removeItem('user');
            sessionStorage.removeItem('permissions');
            window.location.href = '/auth/boxed-signin';
            return new Promise(() => {});
        }
        return Promise.reject(error);
    }
);

export default api;