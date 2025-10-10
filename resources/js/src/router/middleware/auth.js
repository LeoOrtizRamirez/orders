import { useAuthStore } from '@/stores/auth';

export function authGuard(to, from, next) {
    const authStore = useAuthStore();
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/auth/boxed-signin');
    } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next('/');
    } else {
        next();
    }
}