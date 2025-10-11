<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <!-- Total Proveedores -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ totalSuppliers }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('suppliers_page.stats.total_suppliers') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-primary/10 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Proveedores Activos -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ activeSuppliers }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('suppliers_page.stats.active_suppliers') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-success/10 text-success">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Proveedores Inactivos -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ inactiveSuppliers }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('suppliers_page.stats.inactive_suppliers') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-warning/10 text-warning">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Con Órdenes de Compra -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ suppliersWithPurchaseOrders }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('suppliers_page.stats.with_purchase_orders') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-info/10 text-info">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    interface Props {
        totalSuppliers: number;
        activeSuppliers: number;
        inactiveSuppliers: number;
        suppliersWithPurchaseOrders: number;
    }

    defineProps<Props>();
</script>