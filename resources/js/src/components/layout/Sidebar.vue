<template>
    <div :class="{ 'dark text-white-dark': store.semidark }">
        <nav class="sidebar fixed min-h-screen h-full top-0 bottom-0 w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] z-50 transition-all duration-300">
            <div class="bg-white dark:bg-[#0e1726] h-full">
                <div class="flex justify-between items-center px-4 py-3">
                    <router-link to="/" class="main-logo flex items-center shrink-0">
                        <img :class="'w-20 ml-[5px] flex-none ' + customClass" :src="logoPath" alt="" />
                    </router-link>
                    <a
                        href="javascript:;"
                        class="collapse-icon w-8 h-8 rounded-full flex items-center hover:bg-gray-500/10 dark:hover:bg-dark-light/10 dark:text-white-light transition duration-300 rtl:rotate-180 hover:text-primary"
                        @click="store.toggleSidebar()"
                    >
                        <svg class="w-5 h-5 m-auto" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                opacity="0.5"
                                d="M16.9998 19L10.9998 12L16.9998 5"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </a>
                </div>
                <perfect-scrollbar
                    :options="{
                        swipeEasing: true,
                        wheelPropagation: false,
                    }"
                    class="h-[calc(100vh-80px)] relative"
                >
                    <ul class="relative font-semibold space-y-0.5 p-4 py-0">

                        <li class="nav-item">
                            <router-link to="/" class="group" @click="toggleMobileMenu">
                                <div class="flex items-center">
                                    <svg
                                        class="group-hover:!text-primary shrink-0"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            opacity="0.5"
                                            d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                                            fill="currentColor"
                                        />
                                    </svg>

                                    <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                        $t('dashboard')
                                    }}</span>
                                </div>
                            </router-link>
                        </li>

                        <h2 class="py-3 px-7 flex items-center uppercase font-extrabold bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08] -mx-4 mb-1">
                            <svg
                                class="w-4 h-5 flex-none hidden"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>{{ $t('apps') }}</span>
                        </h2>

                        <li class="nav-item">
                            <ul>
                                <li class="nav-item" v-if="authStore.can('create users')">
                                    <router-link to="/apps/contacts" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <svg
                                                class="group-hover:!text-primary shrink-0"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <circle opacity="0.5" cx="15" cy="6" r="3" fill="currentColor" />
                                                <ellipse opacity="0.5" cx="16" cy="17" rx="5" ry="3" fill="currentColor" />
                                                <circle cx="9.00098" cy="6" r="4" fill="currentColor" />
                                                <ellipse cx="9.00098" cy="17.001" rx="7" ry="4" fill="currentColor" />
                                            </svg>

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('contacts')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>

                                <li class="menu nav-item" v-if="authStore.can('view roles')">
                                    <button
                                        type="button"
                                        class="nav-link group w-full"
                                        :class="{ active: activeDropdown === 'invoice' }"
                                        @click="activeDropdown === 'invoice' ? (activeDropdown = null) : (activeDropdown = 'invoice')"
                                    >
                                        <div class="flex items-center">
                                            <svg
                                                class="group-hover:!text-primary shrink-0"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    opacity="0.5"
                                                    d="M2 16C2 13.1716 2 11.7574 2.87868 10.8787C3.75736 10 5.17157 10 8 10H16C18.8284 10 20.2426 10 21.1213 10.8787C22 11.7574 22 13.1716 22 16C22 18.8284 22 20.2426 21.1213 21.1213C20.2426 22 18.8284 22 16 22H8C5.17157 22 3.75736 22 2.87868 21.1213C2 20.2426 2 18.8284 2 16Z"
                                                    fill="currentColor"
                                                />
                                                <path
                                                    d="M8 17C8.55228 17 9 16.5523 9 16C9 15.4477 8.55228 15 8 15C7.44772 15 7 15.4477 7 16C7 16.5523 7.44772 17 8 17Z"
                                                    fill="currentColor"
                                                />
                                                <path
                                                    d="M12 17C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15C11.4477 15 11 15.4477 11 16C11 16.5523 11.4477 17 12 17Z"
                                                    fill="currentColor"
                                                />
                                                <path
                                                    d="M17 16C17 16.5523 16.5523 17 16 17C15.4477 17 15 16.5523 15 16C15 15.4477 15.4477 15 16 15C16.5523 15 17 15.4477 17 16Z"
                                                    fill="currentColor"
                                                />
                                                <path
                                                    d="M6.75 8C6.75 5.10051 9.10051 2.75 12 2.75C14.8995 2.75 17.25 5.10051 17.25 8V10.0036C17.8174 10.0089 18.3135 10.022 18.75 10.0546V8C18.75 4.27208 15.7279 1.25 12 1.25C8.27208 1.25 5.25 4.27208 5.25 8V10.0546C5.68651 10.022 6.18264 10.0089 6.75 10.0036V8Z"
                                                    fill="currentColor"
                                                />
                                            </svg>

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('invoice')
                                            }}</span>
                                        </div>
                                        <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'invoice' }">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9 5L15 12L9 19"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                    <vue-collapsible :isOpen="activeDropdown === 'invoice'">
                                        <ul class="sub-menu text-gray-500">
                                            <li>
                                                <router-link to="/apps/manage-roles/roles-list" @click="toggleMobileMenu">{{ $t('preview') }}</router-link>
                                            </li>
                                            <li>
                                                <router-link to="/apps/manage-roles/permissions-list" @click="toggleMobileMenu">{{ $t('add') }}</router-link>
                                            </li>
                                        </ul>
                                    </vue-collapsible>
                                </li>

                                <li class="nav-item">
                                    <router-link to="/apps/suppliers" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <svg
                                                class="group-hover:!text-primary shrink-0"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M6.94028 2C7.35614 2 7.69326 2.32421 7.69326 2.72414V4.18487C8.36117 4.17241 9.10983 4.17241 9.95219 4.17241H13.9681C14.8104 4.17241 15.5591 4.17241 16.227 4.18487V2.72414C16.227 2.32421 16.5641 2 16.98 2C17.3958 2 17.733 2.32421 17.733 2.72414V4.24894C19.178 4.36022 20.1267 4.63333 20.8236 5.30359C21.5206 5.97385 21.8046 6.88616 21.9203 8.27586L22 9H2.92456H2V8.27586C2.11571 6.88616 2.39970 5.97385 3.09665 5.30359C3.79361 4.63333 4.74226 4.36022 6.18730 4.24894V2.72414C6.18730 2.32421 6.52442 2 6.94028 2Z"
                                                    fill="currentColor"
                                                />
                                                <path
                                                    opacity="0.5"
                                                    d="M21.9995 14.0001V12.0001C21.9995 11.161 21.9963 9.66527 21.9834 9H2.00917C1.99626 9.66527 1.99953 11.161 1.99953 12.0001V14.0001C1.99953 17.7713 1.99953 19.6569 3.17110 20.8285C4.34267 22.0001 6.22829 22.0001 9.99953 22.0001H13.9995C17.7708 22.0001 19.6564 22.0001 20.8280 20.8285C21.9995 19.6569 21.9995 17.7713 21.9995 14.0001Z"
                                                    fill="currentColor"
                                                />
                                            </svg>

                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('suppliers')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>

                                <li class="nav-item">
                                    <router-link to="/apps/products" class="group" @click="toggleMobileMenu">
                                        <div class="flex items-center">
                                            <svg
                                                class="group-hover:!text-primary shrink-0"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M6.94028 2C7.35614 2 7.69326 2.32421 7.69326 2.72414V4.18487C8.36117 4.17241 9.10983 4.17241 9.95219 4.17241H13.9681C14.8104 4.17241 15.5591 4.17241 16.227 4.18487V2.72414C16.227 2.32421 16.5641 2 16.98 2C17.3958 2 17.733 2.32421 17.733 2.72414V4.24894C19.178 4.36022 20.1267 4.63333 20.8236 5.30359C21.5206 5.97385 21.8046 6.88616 21.9203 8.27586L22 9H2.92456H2V8.27586C2.11571 6.88616 2.39970 5.97385 3.09665 5.30359C3.79361 4.63333 4.74226 4.36022 6.18730 4.24894V2.72414C6.18730 2.32421 6.52442 2 6.94028 2Z"
                                                    fill="currentColor"
                                                />
                                                <path
                                                    opacity="0.5"
                                                    d="M21.9995 14.0001V12.0001C21.9995 11.161 21.9963 9.66527 21.9834 9H2.00917C1.99626 9.66527 1.99953 11.161 1.99953 12.0001V14.0001C1.99953 17.7713 1.99953 19.6569 3.17110 20.8285C4.34267 22.0001 6.22829 22.0001 9.99953 22.0001H13.9995C17.7708 22.0001 19.6564 22.0001 20.8280 20.8285C21.9995 19.6569 21.9995 17.7713 21.9995 14.0001Z"
                                                    fill="currentColor"
                                                />
                                            </svg>
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{
                                                $t('products')
                                            }}</span>
                                        </div>
                                    </router-link>
                                </li>

                                <li class="menu nav-item">
                                    <button
                                        type="button"
                                        class="nav-link group w-full"
                                        :class="{ active: activeDropdown === 'reports' }"
                                        @click="activeDropdown === 'reports' ? (activeDropdown = null) : (activeDropdown = 'reports')"
                                    >
                                        <div class="flex items-center">
                                            <svg
                                                class="group-hover:!text-primary shrink-0"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path d="M9 17H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    opacity="0.5"
                                                    d="M20 17L20 12C20 8.22876 20 6.34315 18.8284 5.17157C17.6569 4 15.7712 4 12 4C8.22876 4 6.34315 4 5.17157 5.17157C4 6.34315 4 8.22876 4 12L4 17"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M20 21C18.8954 21 18 20.1046 18 19C18 17.8954 18.8954 17 20 17"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M4 21C5.10457 21 6 20.1046 6 19C6 17.8954 5.10457 17 4 17"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                                <path d="M12 21C10.8954 21 10 20.1046 10 19C10 17.8954 10.8954 17 12 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span class="ltr:pl-3 rtl:pr-3 text-black dark:text-[#506690] dark:group-hover:text-white-dark">{{ $t('reports') }}</span>
                                        </div>
                                        <div class="rtl:rotate-180" :class="{ '!rotate-90': activeDropdown === 'reports' }">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9 5L15 12L9 19"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                    <vue-collapsible :isOpen="activeDropdown === 'reports'">
                                        <ul class="sub-menu text-gray-500">
                                            <li>
                                                <router-link to="/apps/reports/sales" @click="toggleMobileMenu">Ventas</router-link>
                                            </li>
                                            <li>
                                                <router-link to="/apps/reports/inventory" @click="toggleMobileMenu">Inventario</router-link>
                                            </li>
                                            <li>
                                                <router-link to="/apps/reports/operations" @click="toggleMobileMenu">Operaciones</router-link>
                                            </li>
                                        </ul>
                                    </vue-collapsible>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </perfect-scrollbar>
            </div>
        </nav>
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed } from 'vue';
    import { useAppStore } from '@/stores/index';
    import { useAuthStore } from '@/stores/auth'; // Importa el store de autenticación
    import VueCollapsible from 'vue-height-collapsible/vue3';
    
    const store = useAppStore();
    const authStore = useAuthStore(); // Inicializa el store de autenticación
    
    const activeDropdown: any = ref('');
    const subActive: any = ref('');

    onMounted(() => {
        const selector = document.querySelector('.sidebar ul a[href="' + window.location.pathname + '"]');
        if (selector) {
            selector.classList.add('active');
            const ul: any = selector.closest('ul.sub-menu');
            if (ul) {
                let ele: any = ul.closest('li.menu').querySelectorAll('.nav-link') || [];
                if (ele.length) {
                    ele = ele[0];
                    setTimeout(() => {
                        ele.click();
                    });
                }
            }
        }
    });

    const toggleMobileMenu = () => {
        if (window.innerWidth < 1024) {
            store.toggleSidebar();
        }
    };

    const logoPath = computed(() => {

        if(store.menu == "collapsible-vertical"){
            return '/assets/images/B_blupage.png';
        }else{
            return store.theme == 'dark' 
                ? '/assets/images/logo-dark.png'
                : '/assets/images/logo-white.png';

        }
    });

    const customClass = computed(() => {
        return store.menu == "collapsible-vertical" ? 'logo-size' : 'logo';
    });


</script>
<style scoped>
    .logo{
        width: 150px;
    }

    .logo-size{
        width: 30px;
    }
</style>