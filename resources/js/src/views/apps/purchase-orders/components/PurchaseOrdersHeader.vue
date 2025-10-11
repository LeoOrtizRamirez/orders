<template>
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h2 class="text-xl">{{ $t('purchase_orders_page.title') }}</h2>
        <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4 w-full sm:w-auto">
            <div class="flex gap-3">
                <!-- Botón Nueva Orden -->
                <div>
                    <button 
                        v-if="authStore.can('create purchase_orders')"
                        type="button" 
                        class="btn btn-primary" 
                        @click="$emit('add-order')"
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z" stroke="currentColor" stroke-width="1.5"/>
                            <path opacity="0.5" d="M8 12H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path opacity="0.5" d="M12 16V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        {{ $t('purchase_orders_page.add_order_btn') }}
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="flex gap-2">
                <select 
                    v-model="localFilters.status" 
                    class="form-select py-2" 
                    @change="handleFiltersChange"
                >
                    <option value="">{{ $t('purchase_orders_page.filters.all_status') }}</option>
                    <option value="draft">{{ $t('purchase_orders_page.filters.draft') }}</option>
                    <option value="pending">{{ $t('purchase_orders_page.filters.pending') }}</option>
                    <option value="approved">{{ $t('purchase_orders_page.filters.approved') }}</option>
                    <option value="rejected">{{ $t('purchase_orders_page.filters.rejected') }}</option>
                    <option value="ordered">{{ $t('purchase_orders_page.filters.ordered') }}</option>
                    <option value="received">{{ $t('purchase_orders_page.filters.received') }}</option>
                    <option value="cancelled">{{ $t('purchase_orders_page.filters.cancelled') }}</option>
                </select>
                
                <select 
                    v-model="localFilters.supplier_id" 
                    class="form-select py-2" 
                    @change="handleFiltersChange"
                >
                    <option value="">{{ $t('purchase_orders_page.filters.all_suppliers') }}</option>
                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                        {{ supplier.name }}
                    </option>
                </select>
            </div>

            <!-- Búsqueda -->
            <div class="relative">
                <input
                    type="text"
                    :placeholder="$t('purchase_orders_page.search_placeholder')"
                    class="form-input py-2 ltr:pr-11 rtl:pl-11 peer"
                    :value="searchOrder"
                    @input="$emit('update:search-order', ($event.target as HTMLInputElement).value)"
                />
                <div class="absolute ltr:right-[11px] rtl:left-[11px] top-1/2 -translate-y-1/2 peer-focus:text-primary">
                    <svg class="mx-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5"></circle>
                        <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { ref, watch } from 'vue';
    import { useAuthStore } from '@/stores/auth';
    import type { Supplier } from '@/types/purchase-orders';

    interface Props {
        filters: {
            status: string;
            supplier_id: string;
        };
        suppliers: Supplier[];
        searchOrder: string;
    }

    interface Emits {
        (e: 'update:filters', value: Props['filters']): void;
        (e: 'update:search-order', value: string): void;
        (e: 'add-order'): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();
    const authStore = useAuthStore();

    const localFilters = ref({ ...props.filters });

    const handleFiltersChange = () => {
        emit('update:filters', { ...localFilters.value });
    };

    // Sincronizar cuando los props cambien
    watch(() => props.filters, (newFilters) => {
        localFilters.value = { ...newFilters };
    }, { deep: true });
</script>