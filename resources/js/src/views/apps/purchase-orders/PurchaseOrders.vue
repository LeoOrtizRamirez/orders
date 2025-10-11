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
                @approve-order="handleApproveOrder"
                @reject-order="handleRejectOrder"
                @mark-ordered="handleMarkOrdered"
                @receive-order="handleReceiveOrder"
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
        saveOrder,
        deleteOrder,
        approveOrder,
        rejectOrder,
        markAsOrdered,
        receiveOrder,
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

    const handleSaveOrder = async (orderParams: any) => {
        const success = await saveOrder();
        if (success) {
            handleCloseModal();
        }
    };

    const handleDeleteOrder = async (order: any) => {
        await deleteOrder(order);
    };

    const handleApproveOrder = async (order: any) => {
        await approveOrder(order);
    };

    const handleRejectOrder = async (order: any) => {
        const { value: reason } = await Swal.fire({
            title: 'Motivo del rechazo',
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
            await rejectOrder(order, reason);
        }
    };

    const handleMarkOrdered = async (order: any) => {
        await markAsOrdered(order);
    };

    const handleReceiveOrder = async (order: any) => {
        // Implementar modal de recepción
        console.log('Receive order:', order);
    };

    const handleFiltersUpdate = (newFilters: any) => {
        Object.assign(filters, newFilters);
        fetchOrders();
    };
</script>