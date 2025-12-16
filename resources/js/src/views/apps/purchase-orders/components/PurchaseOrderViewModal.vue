<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="$emit('close')" class="relative z-[51]">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <DialogOverlay class="fixed inset-0 bg-[black]/60" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center px-4 py-8">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-4xl text-black dark:text-white-dark">
                            <button
                                type="button"
                                class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none"
                                @click="handleClose()"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24px"
                                    height="24px"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="w-6 h-6"
                                >
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            <div class="text-lg font-medium bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                {{ $t('purchase_orders_page.view_modal.title') }} 
                                <span v-if="order">{{ order.order_number }}</span>
                                <span v-else class="text-gray-500">{{ $t('purchase_orders_page.view_modal.status.loading') }}</span>
                            </div>
                            
                            <!-- Contenido del modal -->
                            <div class="p-5">
                                <template v-if="order">
                                    <!-- Información de la Orden -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <!-- Información Básica -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.supplier') }}:
                                                </label>
                                                <p class="text-lg">{{ order.supplier?.name }}</p>
                                                <p class="text-sm text-gray-500">{{ order.supplier?.contact_person }}</p>
                                                <p class="text-sm text-gray-500">{{ order.supplier?.email }}</p>
                                            </div>
                                            
                                            <div>
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.status') }}:
                                                </label>
                                                <span class="badge ml-2" :class="getStatusClass(order.status)">
                                                    {{ $t(`purchase_orders_page.status.${order.status.replace(/ /g, '_')}`) }}
                                                </span>
                                            </div>

                                            <div>
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.created_by') }}:
                                                </label>
                                                <p>{{ order.creator?.name }}</p>
                                                <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                                            </div>

                                            <!-- Información de la Orden Padre (si es una sub-orden) -->
                                            <div v-if="order.parent">
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.parent_order') }}:
                                                </label>
                                                <p class="text-lg">
                                                    <a href="#" @click.prevent="$emit('view-order-id', order.parent.id)" class="btn btn-outline-info btn-sm">
                                                        #{{ order.parent.order_number }}
                                                    </a>
                                                </p>
                                            </div>

                                            <!-- Información de Sub-órdenes (si tiene) -->
                                            <div v-if="order.sub_orders && order.sub_orders.length > 0">
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.sub_orders') }}:
                                                </label>
                                                <div class="flex flex-wrap gap-2">
                                                    <a v-for="subOrder in order.sub_orders" :key="subOrder.id" href="#" @click.prevent="$emit('view-order-id', subOrder.id)" class="btn btn-outline-info btn-sm">
                                                        #{{ subOrder.order_number }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fechas y Totales -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.order_date') }}:
                                                </label>
                                                <p>{{ formatDate(order.order_date) }}</p>
                                            </div>

                                            <div>
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.expected_delivery') }}:
                                                </label>
                                                <p :class="{'text-warning': isDeliveryDelayed(order)}">
                                                    {{ formatDate(order.expected_delivery_date) }}
                                                    <span v-if="isDeliveryDelayed(order)" class="text-xs ml-2">
                                                        ({{ $t('purchase_orders_page.view_modal.delivery_status.delayed') }})
                                                    </span>
                                                    <span v-else-if="order.expected_delivery_date" class="text-xs ml-2 text-success">
                                                        ({{ $t('purchase_orders_page.view_modal.delivery_status.on_time') }})
                                                    </span>
                                                </p>
                                            </div>

                                            <div v-if="order.delivery_date">
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.delivery_date') }}:
                                                </label>
                                                <p>{{ formatDate(order.delivery_date) }}</p>
                                            </div>

                                            <div v-if="order.approved_by">
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.approved_by') }}:
                                                </label>
                                                <p>{{ order.approver?.name }}</p>
                                                <p class="text-sm text-gray-500">{{ formatDate(order.approved_at) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Items de la Orden -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold mb-4">
                                            {{ $t('purchase_orders_page.view_modal.sections.order_items') }}
                                        </h3>
                                        <div class="table-responsive">
                                            <table class="table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>{{ $t('purchase_orders_page.view_modal.fields.product') }}</th>
                                                        <th class="text-center">
                                                            {{ $t('purchase_orders_page.view_modal.fields.quantity') }}
                                                        </th>
                                                        <th class="text-center">
                                                            {{ $t('purchase_orders_page.view_modal.fields.received') }}
                                                        </th>
                                                        <th class="text-center">
                                                            {{ $t('purchase_orders_page.view_modal.fields.pending') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in order.items" :key="item.id">
                                                        <td>
                                                            <div class="font-semibold">{{ item.product?.name }}</div>
                                                            <div class="text-sm text-gray-500">{{ item.product?.sku }}</div>
                                                            <div class="mt-2 space-y-2 text-xs">
                                                                <div v-for="note in item.item_notes" :key="note.id" class="p-2 rounded bg-gray-100 dark:bg-gray-700">
                                                                    <p class="font-semibold">{{ note.author?.name }} <span class="text-gray-500 text-xs">- {{ formatNoteTimestamp(note.created_at) }}</span></p>
                                                                    <p class="text-gray-600 dark:text-gray-300">{{ note.note }}</p>
                                                                </div>
                                                                <div v-if="!item.item_notes || item.item_notes.length === 0" class="text-gray-500">
                                                                    {{ $t('user_notes.no_notes_for_item') }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">{{ item.quantity }}</td>
                                                        <td class="text-center">
                                                            <span :class="getReceivedQuantityClass(item)">
                                                                {{ item.received_quantity }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span :class="getPendingQuantityClass(item)">
                                                                {{ item.quantity - item.received_quantity }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Notes and Rejection Reason -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div class="space-y-2">
                                            <div class="mb-2">
                                                <label class="font-semibold text-gray-600">
                                                    {{ $t('purchase_orders_page.view_modal.fields.notes') }}:
                                                </label>
                                                <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded space-y-3">
                                                    <div v-for="note in order.notes" :key="note.id" class="text-sm pb-2 border-b dark:border-gray-700 last:border-b-0">
                                                        <p class="font-semibold">{{ note.author?.name }} <span class="text-gray-500 text-xs">- {{ formatNoteTimestamp(note.created_at) }}</span></p>
                                                        <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ note.note }}</p>
                                                    </div>
                                                    <div v-if="!order.notes || order.notes.length === 0" class="text-gray-500">
                                                        {{ $t('user_notes.no_notes_general') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="order.rejection_reason">
                                                <label class="font-semibold text-danger">
                                                    {{ $t('purchase_orders_page.view_modal.fields.rejection_reason') }}:
                                                </label>
                                                <p class="bg-danger/10 text-danger p-3 rounded">{{ order.rejection_reason }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div class="flex justify-end items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-primary" 
                                            @click="printOrder"
                                        >
                                            <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            {{ $t('purchase_orders_page.view_modal.buttons.print') }}
                                        </button>
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-danger ltr:ml-4 rtl:mr-4" 
                                            @click="handleClose()"
                                        >
                                            {{ $t('purchase_orders_page.view_modal.buttons.close') }}
                                        </button>
                                    </div>
                                </template>

                                <!-- Estado de carga -->
                                <template v-else>
                                    <div class="text-center py-8">
                                        <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-8 h-8 mx-auto mb-4"></div>
                                        <p class="text-gray-500">{{ $t('purchase_orders_page.view_modal.status.loading') }}</p>
                                    </div>
                                </template>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script lang="ts" setup>
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import type { PurchaseOrder } from '@/types/purchase-orders';

    interface Props {
        show: boolean;
        order: PurchaseOrder | null;
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'view-order-id', orderId: number): void; // New emit for navigating to parent/sub-orders
        (e: 'refresh-view-order', orderId: number): void; // New emit for refreshing without loading state
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const getStatusClass = (status: string): string => {
        const classes: Record<string, string> = {
            'nuevo pedido': 'bg-gray-100 text-gray-800',
            'disponibilidad': 'bg-secondary/10 text-secondary',
            'preparar pedido': 'bg-success/10 text-success',
            'en preparación': 'bg-info/10 text-info',
            'facturación': 'bg-primary/10 text-primary',
            'en despacho': 'bg-warning/10 text-warning',
            'en ruta': 'bg-danger/10 text-danger',
            'entregado': 'bg-success/10 text-success'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    };

    const getReceivedQuantityClass = (item: any): string => {
        if (item.received_quantity === 0) return 'text-danger';
        if (item.received_quantity < item.quantity) return 'text-warning';
        return 'text-success';
    };

    const getPendingQuantityClass = (item: any): string => {
        const pending = item.quantity - item.received_quantity;
        if (pending === 0) return 'text-success';
        if (pending > 0) return 'text-warning';
        return 'text-danger';
    };

    const isDeliveryDelayed = (order: PurchaseOrder): boolean => {
        if (!order.expected_delivery_date) return false;
        const expected = new Date(order.expected_delivery_date);
        const today = new Date();
        return expected < today && order.status !== 'received';
    };

    const formatDate = (dateString: string | null): string => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const formatNoteTimestamp = (timestamp: string): string => {
        if (!timestamp) return '';
        return new Date(timestamp).toLocaleString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const printOrder = () => {
        window.print();
    };

    const handleClose = () => {
        console.log('PurchaseOrderViewModal: Emitting close event');
        emit('close');
    };
</script>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    
    .panel {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    button {
        display: none !important;
    }
}
</style>