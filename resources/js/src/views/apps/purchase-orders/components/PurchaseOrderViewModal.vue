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
                                @click="$emit('close')"
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
                                {{ $t('purchase_orders_page.modal.view_title') }} - {{ order.order_number }}
                            </div>
                            <div class="p-5">
                                <!-- Información de la Orden -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <!-- Información Básica -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="font-semibold text-gray-600">Proveedor:</label>
                                            <p class="text-lg">{{ order.supplier?.name }}</p>
                                            <p class="text-sm text-gray-500">{{ order.supplier?.contact_person }}</p>
                                            <p class="text-sm text-gray-500">{{ order.supplier?.email }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="font-semibold text-gray-600">Estado:</label>
                                            <span class="badge ml-2" :class="getStatusClass(order.status)">
                                                {{ $t(`purchase_orders_page.status.${order.status}`) }}
                                            </span>
                                        </div>

                                        <div>
                                            <label class="font-semibold text-gray-600">Creado por:</label>
                                            <p>{{ order.creator?.name }}</p>
                                            <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                                        </div>
                                    </div>

                                    <!-- Fechas y Totales -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="font-semibold text-gray-600">Fecha de Orden:</label>
                                            <p>{{ formatDate(order.order_date) }}</p>
                                        </div>

                                        <div>
                                            <label class="font-semibold text-gray-600">Entrega Esperada:</label>
                                            <p :class="{'text-warning': isDeliveryDelayed(order)}">
                                                {{ formatDate(order.expected_delivery_date) }}
                                            </p>
                                        </div>

                                        <div v-if="order.delivery_date">
                                            <label class="font-semibold text-gray-600">Fecha de Recepción:</label>
                                            <p>{{ formatDate(order.delivery_date) }}</p>
                                        </div>

                                        <div v-if="order.approved_by">
                                            <label class="font-semibold text-gray-600">Aprobado por:</label>
                                            <p>{{ order.approver?.name }}</p>
                                            <p class="text-sm text-gray-500">{{ formatDate(order.approved_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items de la Orden -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-4">Items de la Orden</h3>
                                    <div class="table-responsive">
                                        <table class="table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Recibido</th>
                                                    <th class="text-center">Pendiente</th>
                                                    <th class="text-right">Precio Unitario</th>
                                                    <th class="text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="item in order.items" :key="item.id">
                                                    <td>
                                                        <div class="font-semibold">{{ item.product?.name }}</div>
                                                        <div class="text-sm text-gray-500">{{ item.product?.sku }}</div>
                                                        <div v-if="item.notes" class="text-xs text-gray-400">
                                                            {{ item.notes }}
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
                                                    <td class="text-right">{{ moneyFormat(item.unit_price) }}</td>
                                                    <td class="text-right font-semibold">{{ moneyFormat(item.total_price) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Totales -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-2">
                                        <div v-if="order.notes">
                                            <label class="font-semibold text-gray-600">Notas:</label>
                                            <p class="bg-gray-50 dark:bg-gray-800 p-3 rounded">{{ order.notes }}</p>
                                        </div>

                                        <div v-if="order.rejection_reason">
                                            <label class="font-semibold text-danger">Motivo de Rechazo:</label>
                                            <p class="bg-danger/10 text-danger p-3 rounded">{{ order.rejection_reason }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                        <h4 class="font-semibold mb-3">Resumen de Totales</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span>Subtotal:</span>
                                                <span class="font-semibold">{{ moneyFormat(order.subtotal) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Impuestos:</span>
                                                <span class="font-semibold">{{ moneyFormat(order.tax) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Envío:</span>
                                                <span class="font-semibold">{{ moneyFormat(order.shipping) }}</span>
                                            </div>
                                            <hr class="my-2">
                                            <div class="flex justify-between text-lg font-bold">
                                                <span>Total:</span>
                                                <span class="text-success">{{ moneyFormat(order.total) }}</span>
                                            </div>
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
                                        Imprimir
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-danger ltr:ml-4 rtl:mr-4" 
                                        @click="$emit('close')"
                                    >
                                        Cerrar
                                    </button>
                                </div>
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
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

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

    const moneyFormat = (valor = 0): string => {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(valor);
    };

    const printOrder = () => {
        window.print();
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