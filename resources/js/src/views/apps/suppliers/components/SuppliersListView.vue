<template>
    <div class="panel p-0 border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th>{{ $t('suppliers_page.table.name') }}</th>
                        <th>{{ $t('suppliers_page.table.contact_person') }}</th>
                        <th>{{ $t('suppliers_page.table.email') }}</th>
                        <th>{{ $t('suppliers_page.table.phone') }}</th>
                        <th>{{ $t('suppliers_page.table.city') }}</th>
                        <th>{{ $t('suppliers_page.table.country') }}</th>
                        <th>{{ $t('suppliers_page.table.purchase_orders') }}</th>
                        <th>{{ $t('suppliers_page.table.status') }}</th>
                        <th class="!text-center">{{ $t('suppliers_page.table.actions') }}</th>
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
                    <template v-else-if="suppliers.length === 0">
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                {{ $t('suppliers_page.alerts.no_suppliers') }}
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr v-for="supplier in suppliers" :key="supplier.id">
                            <td>
                                <div class="flex items-center w-max">
                                    <div class="font-semibold">{{ supplier.name }}</div>
                                </div>
                                <div v-if="supplier.tax_id" class="text-xs text-gray-500 mt-1">
                                    {{ $t('suppliers_page.table.tax_id') }}: {{ supplier.tax_id }}
                                </div>
                            </td>
                            <td>
                                <span v-if="supplier.contact_person" class="text-gray-700">
                                    {{ supplier.contact_person }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td>
                                <div v-if="supplier.email" class="flex items-center">
                                    <a :href="`mailto:${supplier.email}`" class="text-primary hover:underline">
                                        {{ supplier.email }}
                                    </a>
                                </div>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td>
                                <div v-if="supplier.phone" class="flex items-center">
                                    <a :href="`tel:${supplier.phone}`" class="text-gray-700 hover:text-primary">
                                        {{ supplier.phone }}
                                    </a>
                                </div>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td>
                                <span v-if="supplier.city" class="text-gray-600">
                                    {{ supplier.city }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td>
                                <span v-if="supplier.country" class="text-gray-600">
                                    {{ supplier.country }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="badge bg-info/10 text-info">
                                        {{ supplier.purchase_orders_count }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span 
                                    class="badge" 
                                    :class="supplier.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'"
                                >
                                    {{ supplier.is_active 
                                        ? $t('suppliers_page.status.active') 
                                        : $t('suppliers_page.status.inactive') 
                                    }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2 items-center justify-center">
                                    <button 
                                        v-if="authStore.can('edit suppliers')"
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        @click="$emit('edit-supplier', supplier)"
                                    >
                                        {{ $t('suppliers_page.actions.edit') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('delete suppliers')"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="$emit('delete-supplier', supplier)"
                                        :disabled="supplier.purchase_orders_count > 0"
                                        :title="supplier.purchase_orders_count > 0 
                                            ? $t('suppliers_page.alerts.cannot_delete') 
                                            : ''"
                                    >
                                        {{ $t('suppliers_page.actions.delete') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('edit suppliers')"
                                        type="button" 
                                        class="btn btn-sm" 
                                        :class="supplier.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                                        @click="$emit('toggle-status', supplier)"
                                    >
                                        {{ supplier.is_active 
                                            ? $t('suppliers_page.actions.deactivate') 
                                            : $t('suppliers_page.actions.activate') 
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
</style>