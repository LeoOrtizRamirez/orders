<template>
    <div>
        <div class="scrumboard-v2 space-y-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h2 class="text-xl leading-relaxed">{{ $t('ordenes.order_kanban_board') }}</h2>
            </div>

            <!-- Filtros y Acciones -->
            <div class="panel mb-5">
                <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                    <!-- Filtros (Izquierda) -->
                    <div class="flex flex-col sm:flex-row items-end gap-4 flex-none">
                        <!-- Proveedor -->
                        <div class="w-full sm:w-64">
                            <label class="text-xs font-bold mb-1">{{ $t('purchase_orders_page.table.supplier') }}</label>
                            <select v-model="localFilters.supplier_id" class="form-select" @change="fetchOrdersKanban">
                                <option value="">{{ $t('purchase_orders_page.filters.all_suppliers') }}</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Fecha Desde -->
                        <div class="w-full sm:w-44">
                            <label class="text-xs font-bold mb-1">{{ $t('calendar_page.from') }}</label>
                            <input type="date" v-model="localFilters.date_from" class="form-input" @change="fetchOrdersKanban" />
                        </div>

                        <!-- Fecha Hasta -->
                        <div class="w-full sm:w-44">
                            <label class="text-xs font-bold mb-1">{{ $t('calendar_page.to') }}</label>
                            <input type="date" v-model="localFilters.date_to" class="form-input" @change="fetchOrdersKanban" />
                        </div>
                    </div>

                    <!-- Botón Crear (Derecha) -->
                    <div class="flex-none w-full md:w-auto">
                        <button v-if="authStore.can('create purchase_orders')" type="button" class="btn btn-primary w-full md:w-auto" @click="handleAddOrder">
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
                                class="w-5 h-5 ltr:mr-2 rtl:ml-2"
                            >
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            {{ $t('purchase_orders_page.add_order_btn') }}
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
                                            <div class="flex items-center text-sm text-gray-500 font-semibold">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 mr-1">
                                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                                </svg>
                                                <span>{{ order.supplier?.name || $t('purchase_orders_page.kanban.no_supplier') }}</span>
                                            </div>
                                            <p class="break-all text-sm text-gray-700 dark:text-gray-400">{{ order.notes ? (Array.isArray(order.notes) && order.notes.length > 0 ? order.notes[0].note : '') : $t('purchase_orders_page.kanban.no_description') }}</p>
                                            <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                                <span>{{ $t('ordenes.created_at') }}: {{ formatDate(order.created_at) }}</span>
                                            </div>
                                            <div class="flex justify-end mt-4 gap-2">
                                                <button type="button" class="btn btn-outline-info btn-sm" @click="handleViewOrder(order)">
                                                    {{ $t('ordenes.view_details') }}
                                                </button>
                                                <button v-if="authStore.can('edit purchase_orders') && order.can_be_edited" type="button" class="btn btn-outline-secondary btn-sm" @click="handleEditOrder(order)">
                                                    {{ $t('edit') }}
                                                </button>
                                                <button v-if="canSplitOrder(order) && authStore.can('create purchase_orders') && order.can_be_edited" type="button" class="btn btn-outline-warning btn-sm" @click="handleShowSplitModal(order)">
                                                    {{ $t('purchase_orders_page.actions.split') }}
                                                </button>
                                                <button v-if="authStore.can('delete purchase_orders') && order.can_be_deleted" type="button" class="btn btn-outline-danger btn-sm" @click="handleDeleteOrder(order)">
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
            :existingOrderNotes="orderNotes || []"
            @close="handleCloseModal"
            @save="handleSaveOrder"
            @add-item="addItem"
            @remove-item="removeItem"
        />

        <PurchaseOrderViewModal
            :show="viewModal.show"
            :order="viewModal.data"
            @close="closeViewModal"
            @view-order-id="handleViewOrder"
            @refresh-view-order="handleRefreshViewOrder"
        />

        <!-- Modal de División de Orden -->
        <PurchaseOrderSplitModal
            :show="showSplitModal"
            :order="orderToSplit"
            :products="products" 
            :errors="errors"
            :saving="saving"
            @close="handleCloseSplitModal"
            @split="handleSplitOrder"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted, watch } from 'vue';
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
    import { useRouter, useRoute } from 'vue-router';
    import type { PurchaseOrder } from '@/types/purchase-orders';
    import { useAuthStore } from '@/stores/auth';

    const { t } = useI18n();
    const router = useRouter(); 
    const route = useRoute();
    const authStore = useAuthStore();
    useMeta({ title: t('ordenes.order_kanban_board') });

    const localFilters = ref({
        search: '',
        supplier_id: '',
        date_from: '',
        date_to: '',
    });

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
        orderNotes, // Added orderNotes
        fetchOrders, // This is the generic fetchOrders that calls /api/purchase-orders
        saveOrder,
        deleteOrder: deleteOrderComposable, 
        editOrder,
        resetParams,
        viewOrder,
        refreshViewOrderData, // Import refreshViewOrderData
        closeViewModal,
        addItem,
        removeItem,
        // handleProductChange, // Removed as it's not emitted by modal
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
            const queryParams = new URLSearchParams();
            if (localFilters.value.search) queryParams.append('search', localFilters.value.search);
            if (localFilters.value.supplier_id) queryParams.append('supplier_id', localFilters.value.supplier_id);
            if (localFilters.value.date_from) queryParams.append('date_from', localFilters.value.date_from);
            if (localFilters.value.date_to) queryParams.append('date_to', localFilters.value.date_to);

            const response = await axios.get(`/api/purchase-orders?${queryParams.toString()}`);
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

    const clearFilters = () => {
        localFilters.value = {
            search: '',
            supplier_id: '',
            date_from: '',
            date_to: '',
        };
        fetchOrdersKanban();
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

        if (oldStatusId === newStatusId) return;

        const statusOrder = [
            'nuevo pedido',
            'disponibilidad',
            'preparar pedido',
            'en preparación',
            'facturación',
            'en despacho',
            'en ruta',
            'entregado',
        ];

        const oldStatusIndex = statusOrder.indexOf(oldStatusId);
        const newStatusIndex = statusOrder.indexOf(newStatusId);
        const billingIndex = statusOrder.indexOf('facturación');

        if (oldStatusIndex >= billingIndex && newStatusIndex < oldStatusIndex && newStatusIndex !== -1) {
            showMessage(t('ordenes.cannot_move_back_after_billing'), 'error');
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
            const response = await axios.put(`/api/purchase-orders/${orderId}/status`, { status: newStatusId });
            showMessage(t('ordenes.order_status_updated_successfully'), 'success');
            
            if (movedOrder && response.data.order) {
                // Actualizamos todo el objeto con la respuesta del servidor para que se refresquen
                // las propiedades calculadas (appends) como can_be_edited, can_be_deleted, etc.
                Object.assign(movedOrder, response.data.order);
            }
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

    const canSplitOrder = (order: PurchaseOrder) => {
        const allowedStatuses = ['nuevo pedido', 'disponibilidad', 'preparar pedido', 'en preparación'];
        return !order.parent_id && allowedStatuses.includes(order.status);
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

    const handleRefreshViewOrder = (orderId: number) => {
        refreshViewOrderData(orderId);
    };

    const handleDeleteOrder = async (order: PurchaseOrder) => {
        const success = await deleteOrderComposable(order);
        if (success) {
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

    const handleSplitOrder = async (splitData: { items: { item_id: number; quantity: number; notes?: string }[], expected_delivery_date: string | null, notes: string | null }) => {
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

    const findAndShowOrder = (orderId: string | string[] | undefined) => {
        if (!orderId) return;

        let foundOrder = null;
        for (const status of orderStatuses.value) {
            foundOrder = status.orders.find(o => o.id === parseInt(orderId as string));
            if (foundOrder) break;
        }

        if (foundOrder) {
            handleViewOrder(foundOrder);
        }
    };

    onMounted(async () => {
        await Promise.all([fetchOrdersKanban(), fetchSuppliers(), fetchProducts()]);
        findAndShowOrder(route.params.orderId);

        // Suscripción a canal de tablero para actualizaciones en tiempo real
        if (authStore.user?.id && window.Echo) {
            window.Echo.private('orders.board')
                .listen('OrderUpdated', (e: any) => {
                    console.log('Orden actualizada en tiempo real:', e);
                    // Actualizar la orden localmente con los datos completos
                    if (e.order) {
                        updateLocalOrder(e.order);
                    }
                });
        }
    });

    const updateLocalOrder = (updatedOrderData: any) => {
        const orderId = updatedOrderData.id;
        const newStatus = updatedOrderData.status;

        // Buscar la orden en todas las columnas
        let orderToMove = null;
        let oldStatusId = '';

        for (const col of orderStatuses.value) {
            const index = col.orders.findIndex(o => o.id === orderId);
            if (index !== -1) {
                orderToMove = col.orders[index];
                oldStatusId = col.id;
                
                // Actualizar siempre los datos de la orden (por si cambiaron permisos o notas)
                Object.assign(orderToMove, updatedOrderData);

                // Si el estado es el mismo, no necesitamos moverla
                if (oldStatusId === newStatus) return;
                
                // Quitar de la columna vieja
                col.orders.splice(index, 1);
                break;
            }
        }

        // Si encontramos la orden, moverla a la nueva columna
        if (orderToMove) {
            const newCol = orderStatuses.value.find(c => c.id === newStatus);
            if (newCol) {
                // orderToMove ya tiene el status actualizado por el Object.assign
                newCol.orders.unshift(orderToMove); 
            } else {
                fetchOrdersKanban();
            }
        } else {
            // Si es una orden nueva que no teníamos, recargar
            fetchOrdersKanban();
        }
    };

    watch(() => route.params.orderId, (newId) => {
        if (newId) {
            findAndShowOrder(newId);
        }
    });
</script>