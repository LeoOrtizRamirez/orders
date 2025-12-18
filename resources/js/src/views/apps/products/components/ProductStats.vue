<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Total Productos -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ totalProducts }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('products_page.stats.total_products') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-primary/10 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Stock Bajo -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ lowStockProducts }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('products_page.stats.low_stock') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-warning/10 text-warning">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Sin Stock -->
        <div class="bg-white dark:bg-[#1c232f] rounded-md border border-[#e0e6ed] dark:border-[#1b2e4b] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">{{ outOfStockProducts }}</div>
                    <div class="text-gray-500 dark:text-gray-400">
                        {{ $t('products_page.stats.out_of_stock') }}
                    </div>
                </div>
                <div class="p-3 rounded-full bg-danger/10 text-danger">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    interface Props {
        totalProducts: number;
        activeProducts: number;
        lowStockProducts: number;
        outOfStockProducts: number;
    }

    defineProps<Props>();
</script>