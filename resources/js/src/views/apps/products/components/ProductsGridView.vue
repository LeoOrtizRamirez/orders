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
        <template v-else-if="products.length === 0">
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 text-lg">
                    {{ $t('products_page.alerts.no_products') }}
                </div>
            </div>
        </template>

        <!-- Product Cards -->
        <template v-else>
            <div 
                v-for="product in products" 
                :key="product.id"
                class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative border border-[#e0e6ed] dark:border-[#1b2e4b]"
            >
                <!-- Header con ícono -->
                <div class="rounded-t-md p-6 pb-0 relative h-32 bg-gradient-to-r from-primary/10 to-secondary/10">
                    <div class="grid place-content-center h-16 w-16 mx-auto rounded-full text-3xl mb-4 bg-white dark:bg-gray-800 shadow">
                        📦
                    </div>
                </div>

                <!-- Contenido -->
                <div class="px-6 pb-20 -mt-8 relative">
                    <div class="shadow-md bg-white dark:bg-gray-900 rounded-md px-4 py-6">
                        <!-- Nombre -->
                        <div class="text-lg font-semibold mb-2">{{ product.name }}</div>
                        
                        <!-- Descripción -->
                        <div class="text-white-dark text-sm min-h-[40px] line-clamp-2">
                            {{ product.description || $t('products_page.no_description') }}
                        </div>
                        
                        <!-- Detalles -->
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">{{ $t('products_page.table.sku') }}:</span>
                                <span class="font-mono text-sm">{{ product.sku }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">{{ $t('products_page.table.price') }}:</span>
                                <span class="font-semibold text-success">{{ moneyFormat(product.price) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">{{ $t('products_page.table.stock') }}:</span>
                                <span class="font-semibold" :class="getStockClass(product)">
                                    {{ product.stock }} {{ product.unit || $t('products_page.unit_default') }}
                                </span>
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="mt-4 flex justify-between items-center">
                            <span 
                                v-if="product.category" 
                                class="badge bg-primary/10 text-primary text-xs"
                            >
                                {{ product.category }}
                            </span>
                            <span 
                                class="badge" 
                                :class="product.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'"
                            >
                                {{ product.is_active 
                                    ? $t('products_page.status.active') 
                                    : $t('products_page.status.inactive') 
                                }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="absolute bottom-0 left-0 right-0 p-4 flex gap-2">
                    <button 
                        v-if="authStore.can('edit products')"
                        type="button" 
                        class="btn btn-outline-primary flex-1 btn-sm"
                        @click="$emit('edit-product', product)"
                    >
                        {{ $t('products_page.actions.edit') }}
                    </button>
                    
                    <button 
                        v-if="authStore.can('edit products')"
                        type="button" 
                        class="btn btn-sm" 
                        :class="product.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                        @click="$emit('toggle-status', product)"
                        :title="product.is_active 
                            ? $t('products_page.actions.deactivate') 
                            : $t('products_page.actions.activate') 
                        "
                    >
                        {{ product.is_active ? '🔴' : '🟢' }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="ts" setup>
    import { useAuthStore } from '@/stores/auth';
    import type { Product } from '@/types/products';

    interface Props {
        products: Product[];
        loading: boolean;
    }

    interface Emits {
        (e: 'edit-product', product: Product): void;
        (e: 'delete-product', product: Product): void;
        (e: 'toggle-status', product: Product): void;
    }

    defineProps<Props>();
    defineEmits<Emits>();

    const authStore = useAuthStore();

    const getStockClass = (product: Product): string => {
        if (product.stock === 0) return 'text-danger';
        if (product.stock <= product.reorder_point) return 'text-warning';
        return 'text-success';
    };

    const moneyFormat = (valor = 0): string => {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(valor);
    };
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