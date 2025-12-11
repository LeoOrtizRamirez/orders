<template>
    <div>
        <ul class="flex space-x-2 rtl:space-x-reverse mb-6">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">{{ $t('dashboard') }}</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>{{ $t('ordenes.kanban_board') }}</span>
            </li>
        </ul>

        <div class="scrumboard-v2 space-y-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h2 class="text-xl leading-relaxed">{{ $t('ordenes.order_kanban_board') }}</h2>
                <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4">
                    <div class="flex gap-3">
                        <button type="button" class="btn btn-primary" @click="handleAddOrder">
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
                                class="w-5 h-5 ltr:mr-3 rtl:ml-3"
                            >
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            {{ $t('purchase_orders_page.add_order_btn') }}
                        </button>
                        <button type="button" class="btn btn-primary" @click="fetchOrdersKanban">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path
                                    d="M20.9999 12C20.9999 16.9706 16.9705 21 11.9999 21C7.02934 21 2.99994 16.9706 2.99994 12C2.99994 7.02944 7.02934 3 11.9999 3"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                />
                                <path
                                    d="M18.8477 15.6529C19.7897 14.0533 20.2522 13.0166 20.4939 12"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                />
                                <path d="M17.9999 3L20.9999 7H16.9999" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ $t('ordenes.refresh') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <AlertMessages
                :error-message="errorMessage"
                :success-message="successMessage"
                @clear-error="errorMessage = ''"
                @clear-success="successMessage = ''"
            />

            <div class="relative pt-5">
                <div class="h-full -mx-2">
                    <div v-if="loading" class="grid place-content-center h-[500px]">
                        <span class="animate-spin border-4 border-primary border-l-transparent rounded-full w-12 h-12 inline-block align-middle"></span>
                    </div>
                    <div v-else class="overflow-x-auto flex items-start flex-nowrap gap-5 px-2 pb-2">
                        <template v-for="statusCol in orderStatuses" :key="statusCol.id">
                            <div class="panel w-80 flex-none border-2 border-solid rounded-lg" :class="statusCol.borderColorClass">
                                <div class="flex justify-between mb-5">
                                    <h4 class="text-base font-semibold">{{ statusCol.title }}</h4>
                                    <span class="badge" :class="statusCol.badgeClass">{{ statusCol.orders.length }}</span>
                                </div>

                                <!-- task list -->
                                <draggable
                                    class="connect-sorting-content min-h-[150px]"
                                    :list="statusCol.orders"
                                    group="kanban-board"
                                    ghost-class="sortable-ghost"
                                    drag-class="sortable-drag"
                                    :animation="200"
                                    @end="onDragEnd"
                                    :data-status-id="statusCol.id"
                                >
                                    <div class="sortable-list" v-for="order in statusCol.orders" :key="order.id">
                                        <div class="shadow bg-[#f4f4f4] dark:bg-white-dark/20 p-3 pb-5 rounded-md mb-5 space-y-3 cursor-move">
                                            <div v-if="order.parent_id" class="flex items-center text-xs text-info font-semibold">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1"><polyline points="15 10 20 15 15 20"></polyline><path d="M4 4v7a4 4 0 0 0 4 4h12"></path></svg>
                                                <span>{{ $t('ordenes.sub_order') }}</span>
                                            </div>
                                            <div v-if="order.sub_orders_count > 0 && !order.parent_id" class="flex items-center text-xs text-primary font-semibold">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1"><path d="M17 2.1l4 4-4 4"></path><path d="M3 12.6v-2.1c0-4.4 3.6-8 8-8h7"></path><path d="M7 21.9l-4-4 4-4"></path><path d="M21 11.4v2.1c0 4.4-3.6 8-8 8H6"></path></svg>
                                                <span>Orden Principal</span>
                                            </div>
                                            <div class="text-base font-medium">{{ order.order_number }} - {{ order.creator?.name || 'N/A' }}</div>
                                            <p class="break-all text-sm text-gray-700 dark:text-gray-400">{{ order.notes ? (Array.isArray(order.notes) && order.notes.length > 0 ? order.notes[0].note : '') : 'No description' }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                                <span>{{ $t('ordenes.created_at') }}: {{ formatDate(order.created_at) }}</span>
                                            </div>
                                            <div class="flex justify-end mt-4 gap-2">
                                                <button type="button" class="btn btn-outline-info btn-sm" @click="handleViewOrder(order)">
                                                    {{ $t('ordenes.view_details') }}
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="handleEditOrder(order)">
                                                    {{ $t('edit') }}
                                                </button>
                                                <button v-if="!order.parent_id" type="button" class="btn btn-outline-warning btn-sm" @click="handleShowSplitModal(order)">
                                                    {{ $t('purchase_orders_page.actions.split') }}
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" @click="handleDeleteOrder(order)">
                                                    {{ $t('delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </draggable>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <PurchaseOrderModal
            :show="showModal"
            :params="params"
            :errors="errors"
            :saving="saving"
            :suppliers="suppliers"
            :products="products"
            @close="handleCloseModal"
            @save="handleSaveOrder"
            @add-item="addItem"
            @remove-item="removeItem"
            @product-change="handleProductChange"
        />

        <PurchaseOrderViewModal
            :show="viewModal.show"
            :order="viewModal.data"
            @close="closeViewModal"
            @view-order-id="handleViewOrder"
        />

        <!-- Modal de División de Orden -->
        <PurchaseOrderSplitModal
            :show="showSplitModal"
            :order="orderToSplit"
            :errors="errors"
            :saving="saving"
            @close="handleCloseSplitModal"
            @split="handleSplitOrder"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import { VueDraggableNext as draggable } from 'vue-draggable-next';
    import { useMeta } from '@/composables/use-meta';
    import axios from 'axios';
    import { useI18n } from 'vue-i18n';
    import Swal from 'sweetalert2'; 

    // Import the composable and components
    import { usePurchaseOrders } from './composables/usePurchaseOrders';
    import PurchaseOrderModal from './components/PurchaseOrderModal.vue';
    import PurchaseOrderViewModal from './components/PurchaseOrderViewModal.vue';
    import PurchaseOrderSplitModal from './components/PurchaseOrderSplitModal.vue'; 
    import AlertMessages from '../../components/shared/AlertMessages.vue';
    import { useRouter } from 'vue-router';
    import type { PurchaseOrder } from '@/types/purchase-orders';

    const { t } = useI18n();
    const router = useRouter(); 
    useMeta({ title: t('ordenes.order_kanban_board') });


    const {
        fetchSuppliers, 
        fetchProducts,  
        suppliers,
        products,
        loading,
        saving,
        errorMessage,
        successMessage,
        errors,
        params,
        viewModal,
        fetchOrders, // This is the generic fetchOrders that calls /api/purchase-orders
        saveOrder,
        deleteOrder: deleteOrderComposable, 
        editOrder,
        resetParams,
        viewOrder,
        closeViewModal,
        addItem,
        removeItem,
        handleProductChange, 
        showMessage, 
        splitOrder 
    } = usePurchaseOrders();

    const showModal = ref(false);
    const showSplitModal = ref(false); 
    const orderToSplit = ref<PurchaseOrder | null>(null); 

    const orderStatuses = ref([
        { id: 'nuevo pedido', title: t('ordenes.status.nuevo_pedido'), badgeClass: 'badge-outline-dark', borderColorClass: 'border-gray-500', orders: [] as any[] },
        { id: 'disponibilidad', title: t('ordenes.status.disponibilidad'), badgeClass: 'badge-outline-secondary', borderColorClass: 'border-secondary', orders: [] as any[] },
        { id: 'preparar pedido', title: t('ordenes.status.preparar_pedido'), badgeClass: 'badge-outline-success', borderColorClass: 'border-success', orders: [] as any[] },
        { id: 'en preparación', title: t('ordenes.status.en_preparacion'), badgeClass: 'badge-outline-info', borderColorClass: 'border-info', orders: [] as any[] },
        { id: 'facturación', title: t('ordenes.status.facturacion'), badgeClass: 'badge-outline-primary', borderColorClass: 'border-primary', orders: [] as any[] },
        { id: 'en despacho', title: t('ordenes.status.en_despacho'), badgeClass: 'badge-outline-warning', borderColorClass: 'border-warning', orders: [] as any[] },
        { id: 'en ruta', title: t('ordenes.status.en_ruta'), badgeClass: 'badge-outline-danger', borderColorClass: 'border-danger', orders: [] as any[] },
        { id: 'entregado', title: t('ordenes.status.entregado'), badgeClass: 'badge-outline-success', borderColorClass: 'border-success', orders: [] as any[] },
    ]);

    const fetchOrdersKanban = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/api/purchase-orders');
            const fetchedOrders = response.data.data;

            orderStatuses.value.forEach(statusCol => {
                statusCol.orders = [];
            });

            fetchedOrders.forEach((order: any) => {
                const statusCol = orderStatuses.value.find(col => col.id === order.status);
                if (statusCol) {
                    statusCol.orders.push(order);
                } else {
                    console.warn(`Order ${order.id} has unknown status: ${order.status}. Defaulting to 'nuevo pedido'.`);
                    const defaultCol = orderStatuses.value.find(col => col.id === 'nuevo pedido');
                    if (defaultCol) {
                        defaultCol.orders.push(order);
                    }
                }
            });

            

        } catch (err: any) {
            console.error('Error fetching orders for Kanban:', err);
            const msg = err.response?.data?.message || t('ordenes.error_fetching_orders');
            showMessage(msg, 'error');
        } finally {
            loading.value = false;
        }
    };

    const onDragEnd = async (event: any) => {
        const orderId = event.item._underlying_vm_.id; 
        const oldStatusId = event.from.dataset.statusId; 
        const newStatusId = event.to.dataset.statusId;   

        if (!newStatusId || !oldStatusId) {
            console.warn("Source or target status ID is undefined. Drag operation might be invalid. Reverting.");
            await fetchOrdersKanban(); 
            return;
        }

        const oldStatusColumn = orderStatuses.value.find(col => col.id === oldStatusId);
        const newStatusColumn = orderStatuses.value.find(col => col.id === newStatusId);

        if (!oldStatusColumn || !newStatusColumn) {
            console.warn("Source or target status column not found. Reverting.");
            await fetchOrdersKanban();
            return;
        }

        const movedOrder = newStatusColumn.orders.find(order => order.id === orderId);

        if (!movedOrder) {
            console.warn(`Order with ID ${orderId} not found in new status column ${newStatusId}. Reverting.`);
            await fetchOrdersKanban(); 
            return;
        }

        const originalStatusOfDraggedItem = oldStatusId; 

        try {
            await axios.put(`/api/purchase-orders/${orderId}/status`, { status: newStatusId });
            showMessage(t('ordenes.order_status_updated_successfully'), 'success');
        } catch (err: any) {
            console.error('Error updating order status:', err);
            const msg = err.response?.data?.message || t('ordenes.error_updating_order_status');
            showMessage(msg, 'error');

            const indexInNew = newStatusColumn.orders.findIndex(order => order.id === orderId);
            if (indexInNew !== -1) {
                const itemToRevert = newStatusColumn.orders.splice(indexInNew, 1)[0]; 
                oldStatusColumn.orders.splice(event.oldIndex, 0, itemToRevert); 
            }
            if (movedOrder) {
                movedOrder.status = originalStatusOfDraggedItem;
            }
            await fetchOrdersKanban(); 
        }
    };

    const handleAddOrder = () => {
        editOrder(null); 
        showModal.value = true;
    };

    const handleEditOrder = (order: PurchaseOrder) => {
        editOrder(order); 
        showModal.value = true;
    };

    const handleViewOrder = (orderOrId: PurchaseOrder | number) => {
        const orderId = typeof orderOrId === 'number' ? orderOrId : orderOrId.id;
        viewOrder(orderId);
    };

    const handleDeleteOrder = async (order: PurchaseOrder) => {
        const result = await Swal.fire({
            title: t('purchase_orders_page.delete_confirm.title'),
            text: t('purchase_orders_page.delete_confirm.text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: t('purchase_orders_page.delete_confirm.confirm'),
            cancelButtonText: t('purchase_orders_page.delete_confirm.cancel')
        });

        if (result.isConfirmed) {
            await deleteOrderComposable(order); 
            await fetchOrdersKanban(); 
        }
    };

    const handleCloseModal = () => {
        showModal.value = false;
        resetParams(); 
        fetchOrdersKanban(); 
    };

    const handleSaveOrder = async () => {
        const success = await saveOrder();
        if (success) {
            handleCloseModal();
        }
    };

    const handleShowSplitModal = (order: PurchaseOrder) => {
        orderToSplit.value = order;
        showSplitModal.value = true;
    };

    const handleCloseSplitModal = () => {
        showSplitModal.value = false;
        orderToSplit.value = null;
        errors.value = {}; 
    };

    const handleSplitOrder = async (splitData: { items: { item_id: number; quantity: number }[], expected_delivery_date: string | null, notes: string | null }) => {
        if (!orderToSplit.value?.id) {
            showMessage(t('purchase_orders_page.split_modal.alerts.no_order_selected'), 'error'); 
            return;
        }

        const success = await splitOrder(orderToSplit.value.id, splitData);
        if (success) {
            handleCloseSplitModal();
            await fetchOrdersKanban(); 
        }
    };

    const formatDate = (dateString: string) => {
        if (!dateString) return '';
        const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    };

    const getOrderStatusBadge = (status: string) => {
        const statusMap: { [key: string]: string } = {
            'nuevo pedido': 'badge-outline-dark',
            'disponibilidad': 'badge-outline-secondary',
            'preparar pedido': 'badge-outline-success',
            'en preparación': 'badge-outline-info',
            'facturación': 'badge-outline-primary',
            'en despacho': 'badge-outline-warning',
            'en ruta': 'badge-outline-danger',
            'entregado': 'badge-outline-success',
        };
        return statusMap[status.toLowerCase()] || 'badge-outline-light';
    };

    onMounted(() => {
        fetchOrdersKanban();
        fetchSuppliers(); 
        fetchProducts();  
    });
</script>