<template>
    <div class="panel p-0 border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th>{{ $t('products_page.table.sku') }}</th>
                        <th>{{ $t('products_page.table.product') }}</th>
                        <th>{{ $t('products_page.table.category') }}</th>
                        <th>{{ $t('products_page.table.price') }}</th>
                        <th>{{ $t('products_page.table.cost') }}</th>
                        <th>{{ $t('products_page.table.stock') }}</th>
                        <th>{{ $t('products_page.table.min_stock') }}</th>
                        <th>{{ $t('products_page.table.status') }}</th>
                        <th class="!text-center">{{ $t('products_page.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="products.length === 0">
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                {{ $t('products_page.alerts.no_products') }}
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr v-for="product in products" :key="product.id">
                            <td>
                                <div class="font-mono text-sm">{{ product.sku }}</div>
                            </td>
                            <td>
                                <div class="flex items-center w-max">
                                    <div class="font-semibold">{{ product.name }}</div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">
                                    {{ product.description }}
                                </div>
                            </td>
                            <td>
                                <span v-if="product.category" class="badge bg-primary/10 text-primary">
                                    {{ product.category }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td>
                                <div class="font-semibold text-success">{{ moneyFormat(product.price) }}</div>
                            </td>
                            <td>
                                <div class="text-gray-600">{{ moneyFormat(product.cost) }}</div>
                            </td>
                            <td>
                                <div class="flex items-center">
                                    <span class="font-semibold" :class="getStockClass(product)">
                                        {{ product.stock }}
                                    </span>
                                    <span class="text-gray-500 text-sm ml-1">
                                        {{ product.unit || $t('products_page.unit_default') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="text-gray-600">{{ product.min_stock }}</div>
                            </td>
                            <td>
                                <span 
                                    class="badge" 
                                    :class="product.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'"
                                >
                                    {{ product.is_active 
                                        ? $t('products_page.status.active') 
                                        : $t('products_page.status.inactive') 
                                    }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2 items-center justify-center">
                                    <button 
                                        v-if="authStore.can('edit products')"
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        @click="$emit('edit-product', product)"
                                    >
                                        {{ $t('products_page.actions.edit') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('delete products')"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="$emit('delete-product', product)"
                                        :disabled="product.purchase_order_items_count > 0"
                                        :title="product.purchase_order_items_count > 0 
                                            ? $t('products_page.alerts.cannot_delete') 
                                            : ''"
                                    >
                                        {{ $t('products_page.actions.delete') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('edit products')"
                                        type="button" 
                                        class="btn btn-sm" 
                                        :class="product.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                                        @click="$emit('toggle-status', product)"
                                    >
                                        {{ product.is_active 
                                            ? $t('products_page.actions.deactivate') 
                                            : $t('products_page.actions.activate') 
                                        }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
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
</style>