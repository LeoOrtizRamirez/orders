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
                                                                                    <div class="text-base font-medium">{{ order.order_number }} - {{ order.creator?.name || 'N/A' }}</div>
                                                                                    <p class="break-all text-sm text-gray-700 dark:text-gray-400">{{ order.notes ? (Array.isArray(order.notes) && order.notes.length > 0 ? order.notes[0].note : '') : 'No description' }}</p>                                            <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                                <span>{{ $t('ordenes.total') }}: ${{ order.total?.toLocaleString() }}</span>
                                                <span>{{ $t('ordenes.created_at') }}: {{ formatDate(order.created_at) }}</span>
                                            </div>
                                            <div class="flex gap-2 items-center flex-wrap mt-2">
                                                <div class="badge" :class="getOrderStatusBadge(order.status)">{{ $t(`ordenes.status.${order.status}`) }}</div>
                                            </div>
                                            <div class="flex justify-end mt-4 gap-2">
                                                <button type="button" class="btn btn-outline-info btn-sm" @click="handleViewOrder(order)">
                                                    {{ $t('ordenes.view_details') }}
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="handleEditOrder(order)">
                                                    {{ $t('edit') }}
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
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import { VueDraggableNext as draggable } from 'vue-draggable-next';
    import { useMeta } from '@/composables/use-meta';
    import axios from 'axios';
    import { useI18n } from 'vue-i18n';
    import Swal from 'sweetalert2'; // Keep Swal for local confirmation dialogs if needed

    // Import the composable and components
    import { usePurchaseOrders } from './composables/usePurchaseOrders';
    import PurchaseOrderModal from './components/PurchaseOrderModal.vue';
    import PurchaseOrderViewModal from './components/PurchaseOrderViewModal.vue';
    import AlertMessages from '../../components/shared/AlertMessages.vue';
    import { useRouter } from 'vue-router';
    import type { PurchaseOrder } from '@/types/purchase-orders';

    const { t } = useI18n();
    const router = useRouter(); // Initialize router
    useMeta({ title: t('ordenes.order_kanban_board') });

    // Use the composable
    const {
        ordersList, // Not directly used in kanban board, but part of composable
        fetchSuppliers, // Added for fetching suppliers
        fetchProducts,  // Added for fetching products
        suppliers,
        products,
        loading,
        saving,
        errorMessage,
        successMessage,
        errors,
        params,
        viewModal,
        fetchOrders,
        saveOrder,
        deleteOrder: deleteOrderComposable, // Alias to avoid conflict with local deleteOrder
        editOrder,
        resetParams,
        viewOrder,
        closeViewModal,
        addItem,
        removeItem,
        handleProductChange, // From composable
        showMessage // From composable
    } = usePurchaseOrders();

    const showModal = ref(false);

    // Initial status columns, now populated by fetchOrders
    const orderStatuses = ref([
        { id: 'draft', title: t('ordenes.status.draft'), badgeClass: 'badge-outline-dark', borderColorClass: 'border-gray-500', orders: [] as any[] },
        { id: 'pending', title: t('ordenes.status.pending'), badgeClass: 'badge-outline-secondary', borderColorClass: 'border-secondary', orders: [] as any[] },
        { id: 'approved', title: t('ordenes.status.approved'), badgeClass: 'badge-outline-success', borderColorClass: 'border-success', orders: [] as any[] },
        { id: 'ordered', title: t('ordenes.status.ordered'), badgeClass: 'badge-outline-info', borderColorClass: 'border-info', orders: [] as any[] },
        { id: 'received', title: t('ordenes.status.received'), badgeClass: 'badge-outline-primary', borderColorClass: 'border-primary', orders: [] as any[] },
        { id: 'rejected', title: t('ordenes.status.rejected'), badgeClass: 'badge-outline-danger', borderColorClass: 'border-danger', orders: [] as any[] },
        { id: 'cancelled', title: t('ordenes.status.cancelled'), badgeClass: 'badge-outline-warning', borderColorClass: 'border-warning', orders: [] as any[] },
    ]);

    // Override fetchOrders to distribute into kanban columns
    const fetchOrdersKanban = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/api/purchase-orders/kanban');
            const orders = response.data.data;

            // Clear existing orders in columns
            orderStatuses.value.forEach(statusCol => {
                statusCol.orders = [];
            });

            // Distribute orders into their respective status columns
            orders.forEach((order: any) => {
                const statusCol = orderStatuses.value.find(col => col.id === order.status);
                if (statusCol) {
                    statusCol.orders.push(order);
                } else {
                    console.warn(`Order ${order.id} has unknown status: ${order.status}. Defaulting to 'pending'.`);
                    const pendingCol = orderStatuses.value.find(col => col.id === 'pending');
                    if (pendingCol) {
                        pendingCol.orders.push(order);
                    }
                }
            });
        } catch (error: any) {
            console.error('Error fetching orders:', error);
            const msg = error.response?.data?.message || t('ordenes.error_fetching_orders');
            showMessage(msg, 'error');
        } finally {
            loading.value = false;
        }
    };

    const onDragEnd = async (event: any) => {
        // console.log('onDragEnd triggered!', event); // Remove debug log
        const orderId = event.item._underlying_vm_.id; // The ID of the order
        const oldStatusId = event.from.dataset.statusId; // ID of the source column
        const newStatusId = event.to.dataset.statusId;   // ID of the target column

        // console.log('oldStatusId:', oldStatusId, 'newStatusId:', newStatusId); // Remove debug log

        if (!newStatusId || !oldStatusId) {
            console.warn("Source or target status ID is undefined. Drag operation might be invalid. Reverting.");
            await fetchOrdersKanban(); // Re-fetch to revert changes
            return;
        }

        const oldStatusColumn = orderStatuses.value.find(col => col.id === oldStatusId);
        const newStatusColumn = orderStatuses.value.find(col => col.id === newStatusId);

        if (!oldStatusColumn || !newStatusColumn) {
            console.warn("Source or target status column not found. Reverting.");
            await fetchOrdersKanban();
            return;
        }

        // Find the order *in its new position* within the data model (since draggable already moved it)
        const movedOrder = newStatusColumn.orders.find(order => order.id === orderId);

        if (!movedOrder) {
            console.warn(`Order with ID ${orderId} not found in new status column ${newStatusId}. Reverting.`);
            await fetchOrdersKanban(); // Something went wrong with draggable's internal move
            return;
        }

        const originalStatusOfDraggedItem = oldStatusId; // Store original status for potential revert

        try {
            await axios.put(`/api/purchase-orders/${orderId}/status`, { status: newStatusId });
            showMessage(t('ordenes.order_status_updated_successfully'), 'success');
            // If successful, the frontend state (movedOrder.status and its position) is already correct.
            // The movedOrder object's status property is also implicitly updated by the draggable's list mutation.
        } catch (error: any) {
            console.error('Error updating order status:', error);
            const msg = error.response?.data?.message || t('ordenes.error_updating_order_status');
            showMessage(msg, 'error');

            // Revert optimistic update on API failure
            // 1. Manually move the item back in the local data structure (revert array mutation)
            const indexInNew = newStatusColumn.orders.findIndex(order => order.id === orderId);
            if (indexInNew !== -1) {
                const itemToRevert = newStatusColumn.orders.splice(indexInNew, 1)[0]; // Remove from new
                oldStatusColumn.orders.splice(event.oldIndex, 0, itemToRevert); // Add back to old at original index
            }
            // 2. Revert status property of the item
            movedOrder.status = originalStatusOfDraggedItem;

            await fetchOrdersKanban(); // Re-fetch as a safety net in case manual revert had issues
        }
    };

    const handleAddOrder = () => {
        editOrder(null); // Resets params in composable
        showModal.value = true;
    };

    const handleEditOrder = (order: PurchaseOrder) => {
        editOrder(order); // Populates params in composable
        showModal.value = true;
    };

    const handleViewOrder = (order: PurchaseOrder) => {
        viewOrder(order); // Sets viewModal.data in composable
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
            await deleteOrderComposable(order); // Call the aliased composable function
            await fetchOrdersKanban(); // Re-fetch after delete
        }
    };

    const handleCloseModal = () => {
        showModal.value = false;
        resetParams(); // Resets params in composable
        fetchOrdersKanban(); // Refresh orders after modal closes (in case of add/edit)
    };

    const handleSaveOrder = async () => {
        const success = await saveOrder();
        if (success) {
            handleCloseModal();
        }
    };

    const formatDate = (dateString: string) => {
        if (!dateString) return '';
        const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    };

    const getOrderStatusBadge = (status: string) => {
        const statusMap: { [key: string]: string } = {
            draft: 'badge-outline-dark',
            pending: 'badge-outline-secondary',
            approved: 'badge-outline-success',
            ordered: 'badge-outline-info',
            received: 'badge-outline-primary',
            rejected: 'badge-outline-danger',
            cancelled: 'badge-outline-warning',
        };
        return statusMap[status.toLowerCase()] || 'badge-outline-light';
    };

    onMounted(() => {
        fetchOrdersKanban();
        fetchSuppliers(); // Fetch suppliers for modals
        fetchProducts();  // Fetch products for modals
    });
</script>