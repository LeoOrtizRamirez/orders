<template>
    <div class="panel p-0 border-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th>{{ $t('purchase_orders_page.table.order_number') }}</th>
                        <th>{{ $t('purchase_orders_page.table.supplier') }}</th>
                        <th>{{ $t('purchase_orders_page.table.order_date') }}</th>
                        <th>{{ $t('purchase_orders_page.table.expected_delivery') }}</th>
                        <th>{{ $t('purchase_orders_page.table.total') }}</th>
                        <th>{{ $t('purchase_orders_page.table.status') }}</th>
                        <th>{{ $t('purchase_orders_page.table.created_by') }}</th>
                        <th class="!text-center">{{ $t('purchase_orders_page.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="orders.length === 0">
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                {{ $t('purchase_orders_page.alerts.no_orders') }}
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr v-for="order in orders" :key="order.id">
                            <td>
                                <div class="font-mono font-semibold">{{ order.order_number }}</div>
                            </td>
                            <td>
                                <div class="font-semibold">{{ order.supplier.name }}</div>
                                <div class="text-xs text-gray-500">{{ order.supplier.contact_person }}</div>
                            </td>
                            <td>
                                <div>{{ formatDate(order.order_date) }}</div>
                            </td>
                            <td>
                                <div :class="{'text-warning': isDeliveryDelayed(order)}">
                                    {{ formatDate(order.expected_delivery_date) }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-success">{{ moneyFormat(order.total) }}</div>
                            </td>
                            <td>
                                <span class="badge" :class="getStatusClass(order.status)">
                                    {{ $t(`purchase_orders_page.status.${order.status}`) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm">{{ order.creator.name }}</div>
                                <div class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</div>
                            </td>
                            <td>
                                <div class="flex gap-2 items-center justify-center">
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        @click="$emit('view-order', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.view') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && order.can_be_edited"
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        @click="$emit('edit-order', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.edit') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('delete purchase_orders') && order.can_be_edited"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="$emit('delete-order', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.delete') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('approve purchase_orders') && order.status === 'pending'"
                                        type="button" 
                                        class="btn btn-sm btn-outline-success" 
                                        @click="$emit('approve-order', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.approve') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('approve purchase_orders') && order.status === 'pending'"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="$emit('reject-order', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.reject') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('receive purchase_orders') && order.can_be_received"
                                        type="button" 
                                        class="btn btn-sm btn-outline-secondary" 
                                        @click="$emit('receive-order', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.receive') }}
                                    </button>

                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && (order.status === 'approved')"
                                        type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        @click="$emit('mark-ordered', order)"
                                    >
                                        {{ $t('purchase_orders_page.actions.mark_ordered') }}
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
    import type { PurchaseOrder } from '@/types/purchase-orders';

    interface Props {
        orders: PurchaseOrder[];
        loading: boolean;
    }

    interface Emits {
        (e: 'view-order', order: PurchaseOrder): void;
        (e: 'edit-order', order: PurchaseOrder): void;
        (e: 'delete-order', order: PurchaseOrder): void;
        (e: 'approve-order', order: PurchaseOrder): void;
        (e: 'reject-order', order: PurchaseOrder): void;
        (e: 'mark-ordered', order: PurchaseOrder): void;
        (e: 'receive-order', order: PurchaseOrder): void;
    }

    defineProps<Props>();
    defineEmits<Emits>();

    const authStore = useAuthStore();

    const getStatusClass = (status: string): string => {
        const classes: Record<string, string> = {
            draft: 'bg-gray-100 text-gray-800',
            pending: 'bg-warning/10 text-warning',
            approved: 'bg-success/10 text-success',
            rejected: 'bg-danger/10 text-danger',
            ordered: 'bg-info/10 text-info',
            received: 'bg-secondary/10 text-secondary',
            cancelled: 'bg-danger/10 text-danger'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    };

    const isDeliveryDelayed = (order: PurchaseOrder): boolean => {
        if (!order.expected_delivery_date) return false;
        const expected = new Date(order.expected_delivery_date);
        const today = new Date();
        return expected < today && order.status !== 'received';
    };

    const formatDate = (dateString: string | null): string => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('es-ES');
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