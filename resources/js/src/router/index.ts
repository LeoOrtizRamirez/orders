import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useAppStore } from '@/stores/index';
import { useAuthStore } from '@/stores/auth';
import appSetting from '@/app-setting';

const routes: RouteRecordRaw[] = [
    // Default route points to Kanban Board
    {
        path: '/',
        name: 'home', // Keeping 'home' name but changing component and path mapping
        redirect: '/apps/purchase-orders/kanban', // Redirect to the Kanban board
        meta: { requiresAuth: true }
    },

    // apps
    {
        path: '/apps/chat',
        name: 'chat',
        component: () => import(/* webpackChunkName: "apps-chat" */ '../views/apps/chat.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/mailbox',
        name: 'mailbox',
        component: () => import(/* webpackChunkName: "apps-mailbox" */ '../views/apps/mailbox.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/todolist',
        name: 'todolist',
        component: () => import(/* webpackChunkName: "apps-todolist" */ '../views/apps/todolist.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/notes',
        name: 'notes',
        component: () => import(/* webpackChunkName: "apps-notes" */ '../views/apps/notes.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/scrumboard',
        name: 'scrumboard',
        component: () => import(/* webpackChunkName: "apps-scrumboard" */ '../views/apps/scrumboard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/contacts',
        name: 'contacts',
        component: () => import(/* webpackChunkName: "apps-contacts" */ '../views/apps/contacts.vue'),
        meta: { requiresAuth: true }
    },
    // invoice
    {
        path: '/apps/manage-roles/roles-list',
        name: 'roles-list',
        component: () => import(/* webpackChunkName: "apps-roles-list" */ '../views/apps/roles/roles-list.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/manage-roles/permissions-list',
        name: 'permissions-list',
        component: () => import(/* webpackChunkName: "apps-roles-preview" */ '../views/apps/roles/permissions-list.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/apps/products/',
        name: 'products',
        component: () => import(/* webpackChunkName: "apps-services" */ '../views/apps/products/Products.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/apps/suppliers/',
        name: 'suppliers',
        component: () => import(/* webpackChunkName: "apps-services" */ '../views/apps/suppliers/SuppliersPage.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/apps/purchase-orders/kanban/:orderId?',
        name: 'purchase-orders-kanban',
        component: () => import(/* webpackChunkName: "apps-purchase-orders-kanban" */ '../views/apps/purchase-orders/OrderKanbanBoard.vue'),
        meta: { requiresAuth: true }
    },

    {
        path: '/apps/reports/sales',
        name: 'sales-dashboard',
        component: () => import(/* webpackChunkName: "apps-reports-sales" */ '../views/apps/reports/SalesDashboard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/reports/inventory',
        name: 'inventory-report',
        component: () => import(/* webpackChunkName: "apps-reports-inventory" */ '../views/apps/reports/InventoryReport.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/apps/reports/operations',
        name: 'operations-report',
        component: () => import(/* webpackChunkName: "apps-reports-operations" */ '../views/apps/reports/OperationsReport.vue'),
        meta: { requiresAuth: true }
    },


    // components
    {
        path: '/components/tabs',
        name: 'tabs',
        component: () => import(/* webpackChunkName: "components-tabs" */ '../views/components/tabs.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/accordions',
        name: 'accordions',
        component: () => import(/* webpackChunkName: "components-accordions" */ '../views/components/accordions.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/modals',
        name: 'modals',
        component: () => import(/* webpackChunkName: "components-modals" */ '../views/components/modals.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/cards',
        name: 'cards',
        component: () => import(/* webpackChunkName: "components-cards" */ '../views/components/cards.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/carousel',
        name: 'carousel',
        component: () => import(/* webpackChunkName: "components-carousel" */ '../views/components/carousel.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/countdown',
        name: 'countdown',
        component: () => import(/* webpackChunkName: "components-countdown" */ '../views/components/countdown.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/counter',
        name: 'counter',
        component: () => import(/* webpackChunkName: "components-counter" */ '../views/components/counter.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/sweetalert',
        name: 'sweetalert',
        component: () => import(/* webpackChunkName: "components-sweetalert" */ '../views/components/sweetalert.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/timeline',
        name: 'timeline',
        component: () => import(/* webpackChunkName: "components-timeline" */ '../views/components/timeline.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/notifications',
        name: 'notifications',
        component: () => import(/* webpackChunkName: "components-notifications" */ '../views/components/notifications.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/media-object',
        name: 'media-object',
        component: () => import(/* webpackChunkName: "components-media-object" */ '../views/components/media-object.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/list-group',
        name: 'list-group',
        component: () => import(/* webpackChunkName: "components-list-group" */ '../views/components/list-group.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/pricing-table',
        name: 'pricing-table',
        component: () => import(/* webpackChunkName: "components-pricing-table" */ '../views/components/pricing-table.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/components/lightbox',
        name: 'lightbox',
        component: () => import(/* webpackChunkName: "components-lightbox" */ '../views/components/lightbox.vue'),
        meta: { requiresAuth: true }
    },

    //elements
    {
        path: '/elements/alerts',
        name: 'alerts',
        component: () => import(/* webpackChunkName: "elements-alerts" */ '../views/elements/alerts.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/avatar',
        name: 'avatar',
        component: () => import(/* webpackChunkName: "elements-avatar" */ '../views/elements/avatar.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/badges',
        name: 'badges',
        component: () => import(/* webpackChunkName: "elements-badges" */ '../views/elements/badges.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/breadcrumbs',
        name: 'breadcrumbs',
        component: () => import(/* webpackChunkName: "elements-breadcrumbs" */ '../views/elements/breadcrumbs.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/buttons',
        name: 'buttons',
        component: () => import(/* webpackChunkName: "elements-buttons" */ '../views/elements/buttons.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/buttons-group',
        name: 'buttons-group',
        component: () => import(/* webpackChunkName: "elements-buttons-group" */ '../views/elements/buttons-group.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/color-library',
        name: 'color-library',
        component: () => import(/* webpackChunkName: "elements-color-library" */ '../views/elements/color-library.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/dropdown',
        name: 'dropdown',
        component: () => import(/* webpackChunkName: "elements-dropdown" */ '../views/elements/dropdown.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/infobox',
        name: 'infobox',
        component: () => import(/* webpackChunkName: "elements-infobox" */ '../views/elements/infobox.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/jumbotron',
        name: 'jumbotron',
        component: () => import(/* webpackChunkName: "elements-jumbotron" */ '../views/elements/jumbotron.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/loader',
        name: 'loader',
        component: () => import(/* webpackChunkName: "elements-loader" */ '../views/elements/loader.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/pagination',
        name: 'pagination',
        component: () => import(/* webpackChunkName: "elements-pagination" */ '../views/elements/pagination.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/popovers',
        name: 'popovers',
        component: () => import(/* webpackChunkName: "elements-popovers" */ '../views/elements/popovers.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/progress-bar',
        name: 'progress-bar',
        component: () => import(/* webpackChunkName: "elements-progress-bar" */ '../views/elements/progress-bar.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/search',
        name: 'search',
        component: () => import(/* webpackChunkName: "elements-search" */ '../views/elements/search.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/tooltips',
        name: 'tooltips',
        component: () => import(/* webpackChunkName: "elements-tooltips" */ '../views/elements/tooltips.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/treeview',
        name: 'treeview',
        component: () => import(/* webpackChunkName: "elements-treeview" */ '../views/elements/treeview.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/elements/typography',
        name: 'typography',
        component: () => import(/* webpackChunkName: "elements-typography" */ '../views/elements/typography.vue'),
        meta: { requiresAuth: true }
    },

    //charts
    {
        path: '/charts',
        name: 'charts',
        component: () => import(/* webpackChunkName: "charts" */ '../views/charts.vue'),
        meta: { requiresAuth: true }
    },

    //widgets
    {
        path: '/widgets',
        name: 'widgets',
        component: () => import(/* webpackChunkName: "widgets" */ '../views/widgets.vue'),
        meta: { requiresAuth: true }
    },

    //font-icons
    {
        path: '/font-icons',
        name: 'font-icons',
        component: () => import(/* webpackChunkName: "font-icons" */ '../views/font-icons.vue'),
        meta: { requiresAuth: true }
    },

    //dragndrop
    {
        path: '/dragndrop',
        name: 'dragndrop',
        component: () => import(/* webpackChunkName: "dragndrop" */ '../views/dragndrop.vue'),
        meta: { requiresAuth: true }
    },

    //tables
    {
        path: '/tables',
        name: 'tables',
        component: () => import(/* webpackChunkName: "tables" */ '../views/tables.vue'),
        meta: { requiresAuth: true }
    },

    //datatables
    {
        path: '/datatables/basic',
        name: 'datatables-basic',
        component: () => import(/* webpackChunkName: "datatables-basic" */ '../views/datatables/basic.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/advanced',
        name: 'datatables-advanced',
        component: () => import(/* webpackChunkName: "datatables-advanced" */ '../views/datatables/advanced.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/skin',
        name: 'skin',
        component: () => import(/* webpackChunkName: "datatables-skin" */ '../views/datatables/skin.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/order-sorting',
        name: 'order-sorting',
        component: () => import(/* webpackChunkName: "datatables-order-sorting" */ '../views/datatables/order-sorting.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/columns-filter',
        name: 'columns-filter',
        component: () => import(/* webpackChunkName: "datatables-columns-filter" */ '../views/datatables/columns-filter.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/multi-column',
        name: 'multi-column',
        component: () => import(/* webpackChunkName: "datatables-multi-column" */ '../views/datatables/multi-column.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/multiple-tables',
        name: 'multiple-tables',
        component: () => import(/* webpackChunkName: "datatables-multiple-tables" */ '../views/datatables/multiple-tables.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/alt-pagination',
        name: 'alt-pagination',
        component: () => import(/* webpackChunkName: "datatables-alt-pagination" */ '../views/datatables/alt-pagination.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/checkbox',
        name: 'checkbox',
        component: () => import(/* webpackChunkName: "datatables-checkbox" */ '../views/datatables/checkbox.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/range-search',
        name: 'range-search',
        component: () => import(/* webpackChunkName: "datatables-range-search" */ '../views/datatables/range-search.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/export',
        name: 'export',
        component: () => import(/* webpackChunkName: "datatables-export" */ '../views/datatables/export.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/sticky-header',
        name: 'sticky-header',
        component: () => import(/* webpackChunkName: "datatables-sticky-header" */ '../views/datatables/sticky-header.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/clone-header',
        name: 'clone-header',
        component: () => import(/* webpackChunkName: "datatables-clone-header" */ '../views/datatables/clone-header.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/datatables/column-chooser',
        name: 'column-chooser',
        component: () => import(/* webpackChunkName: "datatables-column-chooser" */ '../views/datatables/column-chooser.vue'),
        meta: { requiresAuth: true }
    },

    //forms
    {
        path: '/forms/basic',
        name: 'basic',
        component: () => import(/* webpackChunkName: "forms-basic" */ '../views/forms/basic.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/input-group',
        name: 'input-group',
        component: () => import(/* webpackChunkName: "forms-input-group" */ '../views/forms/input-group.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/layouts',
        name: 'layouts',
        component: () => import(/* webpackChunkName: "forms-layouts" */ '../views/forms/layouts.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/validation',
        name: 'validation',
        component: () => import(/* webpackChunkName: "forms-validation" */ '../views/forms/validation.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/input-mask',
        name: 'input-mask',
        component: () => import(/* webpackChunkName: "forms-input-mask" */ '../views/forms/input-mask.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/select2',
        name: 'select2',
        component: () => import(/* webpackChunkName: "forms-select2" */ '../views/forms/select2.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/touchspin',
        name: 'touchspin',
        component: () => import(/* webpackChunkName: "forms-touchspin" */ '../views/forms/touchspin.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/checkbox-radio',
        name: 'checkbox-radio',
        component: () => import(/* webpackChunkName: "forms-checkbox-radio" */ '../views/forms/checkbox-radio.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/switches',
        name: 'switches',
        component: () => import(/* webpackChunkName: "forms-switches" */ '../views/forms/switches.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/wizards',
        name: 'wizards',
        component: () => import(/* webpackChunkName: "forms-wizards" */ '../views/forms/wizards.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/file-upload',
        name: 'file-upload',
        component: () => import(/* webpackChunkName: "forms-file-upload" */ '../views/forms/file-upload.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/quill-editor',
        name: 'quill-editor',
        component: () => import(/* webpackChunkName: "forms-quill-editor" */ '../views/forms/quill-editor.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/markdown-editor',
        name: 'markdown-editor',
        component: () => import(/* webpackChunkName: "forms-markdown-editor" */ '../views/forms/markdown-editor.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/date-picker',
        name: 'date-picker',
        component: () => import(/* webpackChunkName: "forms-date-picker" */ '../views/forms/date-picker.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/forms/clipboard',
        name: 'clipboard',
        component: () => import(/* webpackChunkName: "forms-clipboard" */ '../views/forms/clipboard.vue'),
        meta: { requiresAuth: true }
    },

    // users
    {
        path: '/users/profile',
        name: 'profile',
        component: () => import(/* webpackChunkName: "users-profile" */ '../views/users/profile.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/users/user-account-settings/:id?',
        name: 'user-account-settings',
        component: () => import(/* webpackChunkName: "users-user-account-settings" */ '../views/users/user-account-settings.vue'),
        meta: { requiresAuth: true }
    },

    // pages
    {
        path: '/pages/knowledge-base',
        name: 'knowledge-base',
        component: () => import(/* webpackChunkName: "pages-knowledge-base" */ '../views/pages/knowledge-base.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/pages/contact-us-boxed',
        name: 'contact-us-boxed',
        component: () => import(/* webpackChunkName: "pages-contact-us-boxed" */ '../views/pages/contact-us-boxed.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/contact-us-cover',
        name: 'contact-us-cover',
        component: () => import(/* webpackChunkName: "pages-contact-us-cover" */ '../views/pages/contact-us-cover.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/faq',
        name: 'faq',
        component: () => import(/* webpackChunkName: "pages-faq" */ '../views/pages/faq.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/pages/coming-soon-boxed',
        name: 'coming-soon-boxed',
        component: () => import(/* webpackChunkName: "pages-coming-soon-boxed" */ '../views/pages/coming-soon-boxed.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/coming-soon-cover',
        name: 'coming-soon-cover',
        component: () => import(/* webpackChunkName: "pages-coming-soon-cover" */ '../views/pages/coming-soon-cover.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/error404',
        name: 'error404',
        component: () => import(/* webpackChunkName: "pages-error404" */ '../views/pages/error404.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/error500',
        name: 'error500',
        component: () => import(/* webpackChunkName: "pages-error500" */ '../views/pages/error500.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/error503',
        name: 'error503',
        component: () => import(/* webpackChunkName: "pages-error503" */ '../views/pages/error503.vue'),
        meta: { layout: 'auth' },
    },
    {
        path: '/pages/maintenence',
        name: 'maintenence',
        component: () => import(/* webpackChunkName: "pages-maintenence" */ '../views/pages/maintenence.vue'),
        meta: { layout: 'auth' },
    },

    // authentication
    {
        path: '/auth/boxed-signin',
        name: 'boxed-signin',
        component: () => import(/* webpackChunkName: "auth-boxed-signin" */ '../views/auth/boxed-signin.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/boxed-signup',
        name: 'boxed-signup',
        component: () => import(/* webpackChunkName: "auth-boxed-signup" */ '../views/auth/boxed-signup.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/boxed-lockscreen',
        name: 'boxed-lockscreen',
        component: () => import(/* webpackChunkName: "auth-boxed-lockscreen" */ '../views/auth/boxed-lockscreen.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/boxed-password-reset',
        name: 'boxed-password-reset',
        component: () => import(/* webpackChunkName: "auth-boxed-password-reset" */ '../views/auth/boxed-password-reset.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/cover-login',
        name: 'cover-login',
        component: () => import(/* webpackChunkName: "auth-cover-login" */ '../views/auth/cover-login.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/cover-register',
        name: 'cover-register',
        component: () => import(/* webpackChunkName: "auth-cover-register" */ '../views/auth/cover-register.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/cover-lockscreen',
        name: 'cover-lockscreen',
        component: () => import(/* webpackChunkName: "auth-cover-lockscreen" */ '../views/auth/cover-lockscreen.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
    {
        path: '/auth/cover-password-reset',
        name: 'cover-password-reset',
        component: () => import(/* webpackChunkName: "auth-cover-password-reset" */ '../views/auth/cover-password-reset.vue'),
        meta: { layout: 'auth', requiresGuest: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    linkExactActiveClass: 'active',
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { left: 0, top: 0 };
        }
    },
});

// Función de guardia de navegación
const authGuard = (to: any, from: any, next: any) => {
    const authStore = useAuthStore();
    const appStore = useAppStore();

    // Siempre verificar la autenticación al cambiar de ruta
    authStore.checkAuth();

    // Configurar layout
    if (to?.meta?.layout == 'auth') {
        appStore.setMainLayout('auth');
    } else {
        appStore.setMainLayout('app');
    }

    // Verificar autenticación
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/auth/boxed-signin');
    } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next('/');
    } else {
        next();
    }
};

// Aplicar el guardia de navegación
router.beforeEach(authGuard);

router.afterEach((to, from) => {
    appSetting.changeAnimation();
});

export default router;