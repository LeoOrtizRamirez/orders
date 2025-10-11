<template>
    <div>
        <!-- Header -->
        <PurchaseOrdersHeader
            :filters="filters"
            :suppliers="suppliers"
            :search-order="searchOrder"
            @update:filters="handleFiltersUpdate"
            @update:search-order="searchOrder = $event"
            @add-order="handleAddOrder"
        />

        <!-- Alertas -->
        <AlertMessages
            :error-message="errorMessage"
            :success-message="successMessage"
            @clear-error="errorMessage = ''"
            @clear-success="successMessage = ''"
        />

        <!-- Estadísticas -->
        <PurchaseOrdersStats
            :total-orders="totalOrders"
            :pending-orders="pendingOrders"
            :approved-orders="approvedOrders"
            :ordered-orders="orderedOrders"
            :received-orders="receivedOrders"
        />

        <!-- Vista de órdenes -->
        <div class="mt-5">
            <PurchaseOrdersListView
                :orders="filteredOrdersList"
                :loading="loading"
                @edit-order="handleEditOrder"
                @delete-order="handleDeleteOrder"
                @view-order="handleViewOrder"
                @submit-order="handleSubmitOrder"
                @approve-order="handleApproveOrder"
                @reject-order="handleRejectOrder"
                @mark-ordered="handleMarkOrdered"
                @receive-order="handleReceiveOrder"
                @cancel-order="handleCancelOrder"
                @reopen-order="handleReopenOrder"
            />
        </div>

        <!-- Modal de Crear/Editar -->
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

        <!-- Modal de Vista -->
        <PurchaseOrderViewModal
            :show="viewModal.show"
            :order="viewModal.data"
            @close="closeViewModal"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import Swal from 'sweetalert2';
    import { useMeta } from '@/composables/use-meta';
    import { usePurchaseOrders } from './composables/usePurchaseOrders';
    
    // Components
    import PurchaseOrdersHeader from './components/PurchaseOrdersHeader.vue';
    import AlertMessages from '../../components/shared/AlertMessages.vue';
    import PurchaseOrdersStats from './components/PurchaseOrdersStats.vue';
    import PurchaseOrdersListView from './components/PurchaseOrdersListView.vue';
    import PurchaseOrderModal from './components/PurchaseOrderModal.vue';
    import PurchaseOrderViewModal from './components/PurchaseOrderViewModal.vue';

    useMeta({ title: 'Gestión de Órdenes de Compra' });

    const showModal = ref(false);

    // Use purchase orders composable
    const {
        ordersList,
        suppliers,
        products,
        loading,
        saving,
        errorMessage,
        successMessage,
        errors,
        searchOrder,
        filters,
        params,
        filteredOrdersList,
        totalOrders,
        pendingOrders,
        approvedOrders,
        orderedOrders,
        receivedOrders,
        viewModal,
        fetchOrders,
        fetchSuppliers,
        fetchProducts,
        updateItemUnitPrice,
        saveOrder,
        deleteOrder,
        submitOrder,
        approveOrder,
        rejectOrder,
        markAsOrdered,
        receiveOrder,
        cancelOrder,
        reopenOrder,
        editOrder,
        resetParams,
        addItem,
        removeItem,
        viewOrder,
        closeViewModal,
        showMessage
    } = usePurchaseOrders();

    onMounted(() => {
        fetchOrders();
        fetchSuppliers();
        fetchProducts();
    });

    // Event handlers
    const handleAddOrder = () => {
        editOrder(null);
        showModal.value = true;
    };

    const handleEditOrder = (order: any) => {
        editOrder(order);
        showModal.value = true;
    };

    const handleViewOrder = (order: any) => {
        viewOrder(order);
    };

    const handleCloseModal = () => {
        showModal.value = false;
        resetParams();
    };

    const handleSaveOrder = async () => {
        const success = await saveOrder();
        if (success) {
            handleCloseModal();
        }
    };

    const handleDeleteOrder = async (order: any) => {
        await deleteOrder(order);
    };

    const handleSubmitOrder = async (order: any) => {
        const result = await Swal.fire({
            title: 'Enviar a Aprobación',
            text: '¿Estás seguro de que deseas enviar esta orden para aprobación?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            await submitOrder(order);
        }
    };

    const handleApproveOrder = async (order: any) => {
        const result = await Swal.fire({
            title: 'Aprobar Orden',
            text: '¿Estás seguro de que deseas aprobar esta orden?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            await approveOrder(order);
        }
    };

    const handleRejectOrder = async (order: any) => {
        const { value: reason } = await Swal.fire({
            title: 'Rechazar Orden',
            input: 'textarea',
            inputLabel: 'Por favor ingrese el motivo del rechazo:',
            inputPlaceholder: 'Describa el motivo del rechazo...',
            inputAttributes: {
                'aria-label': 'Motivo del rechazo'
            },
            showCancelButton: true,
            confirmButtonText: 'Rechazar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return 'Debe ingresar un motivo para rechazar la orden';
                }
            }
        });

        if (reason) {
            await rejectOrder(order);
        }
    };

    const handleMarkOrdered = async (order: any) => {
        const result = await Swal.fire({
            title: 'Marcar como Ordenada',
            text: '¿Estás seguro de que deseas marcar esta orden como enviada al proveedor?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, marcar como ordenada',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            await markAsOrdered(order);
        }
    };

    const handleReceiveOrder = async (order: any) => {
        const result = await Swal.fire({
            title: 'Recibir Orden',
            text: '¿Estás seguro de que deseas marcar esta orden como recibida?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, recibir',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            await receiveOrder(order);
        }
    };

    const handleCancelOrder = async (order: any) => {
        const { value: reason } = await Swal.fire({
            title: 'Cancelar Orden',
            input: 'textarea',
            inputLabel: 'Motivo de cancelación (opcional):',
            inputPlaceholder: 'Describa el motivo de la cancelación...',
            inputAttributes: {
                'aria-label': 'Motivo de cancelación'
            },
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'Cancelar'
        });

        if (reason !== undefined) {
            await cancelOrder(order);
        }
    };

    const handleReopenOrder = async (order: any) => {
        const result = await Swal.fire({
            title: 'Reabrir Orden',
            text: '¿Estás seguro de que deseas reabrir esta orden?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reabrir',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            await reopenOrder(order);
        }
    };

    const handleProductChange = ({ index, productId }: { index: number; productId: number | null }) => {
        updateItemUnitPrice(index, productId);
    };

    const handleFiltersUpdate = (newFilters: any) => {
        Object.assign(filters, newFilters);
        fetchOrders();
    };
</script>