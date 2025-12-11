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
                        <th>{{ $t('purchase_orders_page.table.status') }}</th>
                        <th>{{ $t('purchase_orders_page.table.created_by') }}</th>
                        <th class="!text-center">{{ $t('purchase_orders_page.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="loading">
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                            </td>
                        </tr>
                    </template>
                    <template v-else-if="orders.length === 0">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
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
                                <div class="font-semibold">{{ order.supplier?.name || 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ order.supplier?.contact_person || '-' }}</div>
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
                                <span class="badge" :class="getStatusClass(order.status)">
                                    {{ $t(`purchase_orders_page.status.${order.status}`) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm">{{ order.creator?.name || 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</div>
                            </td>
                            <td>
                                <div class="flex gap-2 items-center justify-center">
                                    <!-- Botón Ver -->
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        @click="$emit('view-order', order.id)"
                                        :title="$t('purchase_orders_page.actions.view')"
                                    >
                                        {{ $t('purchase_orders_page.actions.view') }}
                                    </button>

                                    <!-- Botón Editar -->
                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && order.can_be_edited"
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        @click="$emit('edit-order', order)"
                                        :title="$t('purchase_orders_page.actions.edit')"
                                    >
                                        {{ $t('purchase_orders_page.actions.edit') }}
                                    </button>

                                    <!-- Botón Eliminar -->
                                    <button 
                                        v-if="authStore.can('delete purchase_orders') && order.can_be_deleted"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="$emit('delete-order', order)"
                                        :title="$t('purchase_orders_page.actions.delete')"
                                    >
                                        {{ $t('purchase_orders_page.actions.delete') }}
                                    </button>

                                    <!-- Botón Enviar a Aprobación -->
                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && order.status === 'draft'"
                                        type="button" 
                                        class="btn btn-sm btn-outline-success" 
                                        @click="$emit('submit-order', order)"
                                        :title="$t('purchase_orders_page.actions.submit')"
                                    >
                                        {{ $t('purchase_orders_page.actions.submit') }}
                                    </button>

                                    <!-- Botón Aprobar -->
                                    <button 
                                        v-if="authStore.can('approve purchase_orders') && order.can_be_approved"
                                        type="button" 
                                        class="btn btn-sm btn-outline-success" 
                                        @click="$emit('approve-order', order)"
                                        :title="$t('purchase_orders_page.actions.approve')"
                                    >
                                        {{ $t('purchase_orders_page.actions.approve') }}
                                    </button>

                                    <!-- Botón Rechazar -->
                                    <button 
                                        v-if="authStore.can('approve purchase_orders') && order.can_be_rejected"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="$emit('reject-order', order)"
                                        :title="$t('purchase_orders_page.actions.reject')"
                                    >
                                        {{ $t('purchase_orders_page.actions.reject') }}
                                    </button>

                                    <!-- Botón Marcar como Ordenada -->
                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && order.can_be_marked_ordered"
                                        type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        @click="$emit('mark-ordered', order)"
                                        :title="$t('purchase_orders_page.actions.mark_ordered')"
                                    >
                                        {{ $t('purchase_orders_page.actions.mark_ordered') }}
                                    </button>

                                    <!-- Botón Recibir -->
                                    <button 
                                        v-if="authStore.can('receive purchase_orders') && order.can_be_received"
                                        type="button" 
                                        class="btn btn-sm btn-outline-secondary" 
                                        @click="$emit('receive-order', order)"
                                        :title="$t('purchase_orders_page.actions.receive')"
                                    >
                                        {{ $t('purchase_orders_page.actions.receive') }}
                                    </button>

                                    <!-- Botón Dividir Orden -->
                                    <button 
                                        v-if="authStore.can('create purchase_orders') && order.can_be_edited"
                                        type="button" 
                                        class="btn btn-sm btn-outline-warning" 
                                        @click="$emit('split-order', order)"
                                        :title="$t('purchase_orders_page.actions.split')"
                                    >
                                        {{ $t('purchase_orders_page.actions.split') }}
                                    </button>

                                    <!-- Botón Cancelar -->
                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && order.can_be_cancelled"
                                        type="button" 
                                        class="btn btn-sm btn-outline-warning" 
                                        @click="$emit('cancel-order', order)"
                                        :title="$t('purchase_orders_page.actions.cancel')"
                                    >
                                        {{ $t('purchase_orders_page.actions.cancel') }}
                                    </button>

                                    <!-- Botón Reabrir -->
                                    <button 
                                        v-if="authStore.can('edit purchase_orders') && order.can_be_reopened"
                                        type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        @click="$emit('reopen-order', order)"
                                        :title="$t('purchase_orders_page.actions.reopen')"
                                    >
                                        {{ $t('purchase_orders_page.actions.reopen') }}
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
        (e: 'view-order', orderId: number): void;
        (e: 'edit-order', order: PurchaseOrder): void;
        (e: 'delete-order', order: PurchaseOrder): void;
        (e: 'submit-order', order: PurchaseOrder): void;
        (e: 'approve-order', order: PurchaseOrder): void;
        (e: 'reject-order', order: PurchaseOrder): void;
        (e: 'mark-ordered', order: PurchaseOrder): void;
        (e: 'receive-order', order: PurchaseOrder): void;
        (e: 'cancel-order', order: PurchaseOrder): void;
        (e: 'reopen-order', order: PurchaseOrder): void;
        (e: 'split-order', order: PurchaseOrder): void; // New emit for splitting orders
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
        if (!order.expected_delivery_date || order.status === 'received') return false;
        const expected = new Date(order.expected_delivery_date);
        const today = new Date();
        return expected < today;
    };

    const formatDate = (dateString: string | null): string => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('es-ES');
    };
</script>

<style scoped>
    .badge {
        @apply px-2 py-1 text-xs font-medium rounded-full;
    }

    .btn {
        @apply transition-all duration-200 ease-in-out;
    }

    .btn:hover {
        @apply transform scale-105;
    }
</style>