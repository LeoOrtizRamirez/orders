<template>
    <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 w-full">
        <!-- Skeleton Loading -->
        <template v-if="loading">
            <div 
                v-for="i in 8" 
                :key="'skeleton-'+i" 
                class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative h-80"
            >
                <div class="animate-pulse h-full flex flex-col">
                    <div class="bg-gray-300 h-32 rounded-t-md"></div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="h-4 bg-gray-300 rounded"></div>
                            <div class="h-3 bg-gray-300 rounded"></div>
                            <div class="h-3 bg-gray-300 rounded w-3/4 mx-auto"></div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <div class="h-8 bg-gray-300 rounded flex-1"></div>
                            <div class="h-8 bg-gray-300 rounded flex-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <template v-else-if="suppliers.length === 0">
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 text-lg">
                    {{ $t('suppliers_page.alerts.no_suppliers') }}
                </div>
            </div>
        </template>

        <!-- Supplier Cards -->
        <template v-else>
            <div 
                v-for="supplier in suppliers" 
                :key="supplier.id"
                class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative border border-[#e0e6ed] dark:border-[#1b2e4b]"
            >
                <!-- Header con ícono -->
                <div class="rounded-t-md p-6 pb-0 relative h-32 bg-gradient-to-r from-primary/10 to-secondary/10">
                    <div class="grid place-content-center h-16 w-16 mx-auto rounded-full text-3xl mb-4 bg-white dark:bg-gray-800 shadow">
                        🏢
                    </div>
                </div>

                <!-- Contenido -->
                <div class="px-6 pb-20 -mt-8 relative">
                    <div class="shadow-md bg-white dark:bg-gray-900 rounded-md px-4 py-6">
                        <!-- Nombre -->
                        <div class="text-lg font-semibold mb-2">{{ supplier.name }}</div>
                        
                        <!-- Información de contacto -->
                        <div class="mt-4 space-y-2 text-sm">
                            <div v-if="supplier.contact_person" class="flex items-center justify-center text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ supplier.contact_person }}
                            </div>
                            <div v-if="supplier.email" class="flex items-center justify-center text-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ supplier.email }}
                            </div>
                            <div v-if="supplier.phone" class="flex items-center justify-center text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ supplier.phone }}
                            </div>
                            <div v-if="supplier.city || supplier.country" class="flex items-center justify-center text-gray-500 text-xs">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ [supplier.city, supplier.country].filter(Boolean).join(', ') }}
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="mt-4 flex justify-between items-center">
                            <span 
                                class="badge bg-info/10 text-info text-xs"
                            >
                                {{ $t('suppliers_page.table.purchase_orders') }}: {{ supplier.purchase_orders_count }}
                            </span>
                            <span 
                                class="badge" 
                                :class="supplier.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'"
                            >
                                {{ supplier.is_active 
                                    ? $t('suppliers_page.status.active') 
                                    : $t('suppliers_page.status.inactive') 
                                }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="absolute bottom-0 left-0 right-0 p-4 flex gap-2">
                    <button 
                        v-if="authStore.can('edit suppliers')"
                        type="button" 
                        class="btn btn-outline-primary flex-1 btn-sm"
                        @click="$emit('edit-supplier', supplier)"
                    >
                        {{ $t('suppliers_page.actions.edit') }}
                    </button>
                    
                    <button 
                        v-if="authStore.can('edit suppliers')"
                        type="button" 
                        class="btn btn-sm" 
                        :class="supplier.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                        @click="$emit('toggle-status', supplier)"
                        :title="supplier.is_active 
                            ? $t('suppliers_page.actions.deactivate') 
                            : $t('suppliers_page.actions.activate') 
                        "
                    >
                        {{ supplier.is_active ? '🔴' : '🟢' }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="ts" setup>
    import { useAuthStore } from '@/stores/auth';
    import type { Supplier } from '@/types/suppliers';

    interface Props {
        suppliers: Supplier[];
        loading: boolean;
    }

    interface Emits {
        (e: 'edit-supplier', supplier: Supplier): void;
        (e: 'delete-supplier', supplier: Supplier): void;
        (e: 'toggle-status', supplier: Supplier): void;
    }

    defineProps<Props>();
    defineEmits<Emits>();

    const authStore = useAuthStore();
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.min-h-\[40px\] {
    min-height: 40px;
}
</style>