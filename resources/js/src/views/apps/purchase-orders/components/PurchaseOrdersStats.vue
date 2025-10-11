<template>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-6">
        <!-- Total Órdenes -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ totalOrders }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $t('purchase_orders_page.stats.total_orders') }}
                    </div>
                </div>
                <div class="p-2 rounded-full bg-primary/10 text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Órdenes Pendientes -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ pendingOrders }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $t('purchase_orders_page.stats.pending_orders') }}
                    </div>
                </div>
                <div class="p-2 rounded-full bg-warning/10 text-warning">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Órdenes Aprobados -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ approvedOrders }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $t('purchase_orders_page.stats.approved_orders') }}
                    </div>
                </div>
                <div class="p-2 rounded-full bg-success/10 text-success">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Órdenes Ordenados -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ orderedOrders }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $t('purchase_orders_page.stats.ordered_orders') }}
                    </div>
                </div>
                <div class="p-2 rounded-full bg-info/10 text-info">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Órdenes Recibidos -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ receivedOrders }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $t('purchase_orders_page.stats.received_orders') }}
                    </div>
                </div>
                <div class="p-2 rounded-full bg-secondary/10 text-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    interface Props {
        totalOrders: number;
        pendingOrders: number;
        approvedOrders: number;
        orderedOrders: number;
        receivedOrders: number;
    }

    defineProps<Props>();
</script>