<template>
    <div>
        <!-- Header -->
        <SuppliersHeader
            :display-type="displayType"
            :filters="filters"
            :search-supplier="searchSupplier"
            @update:display-type="displayType = $event"
            @update:filters="handleFiltersUpdate"
            @update:search-supplier="searchSupplier = $event"
            @add-supplier="handleAddSupplier"
        />

        <!-- Alertas -->
        <AlertMessages
            :error-message="errorMessage"
            :success-message="successMessage"
            @clear-error="errorMessage = ''"
            @clear-success="successMessage = ''"
        />

        <!-- Estadísticas -->
        <SupplierStats
            :total-suppliers="totalSuppliers"
            :active-suppliers="activeSuppliers"
            :inactive-suppliers="inactiveSuppliers"
            :suppliers-with-purchase-orders="suppliersWithPurchaseOrders"
        />

        <!-- Vista de proveedores -->
        <div class="mt-5">
            <SuppliersListView
                v-if="displayType === 'list'"
                :suppliers="filteredSuppliersList"
                :loading="loading"
                @edit-supplier="handleEditSupplier"
                @delete-supplier="handleDeleteSupplier"
                @toggle-status="handleToggleStatus"
            />
            
            <SuppliersGridView
                v-else
                :suppliers="filteredSuppliersList"
                :loading="loading"
                @edit-supplier="handleEditSupplier"
                @delete-supplier="handleDeleteSupplier"
                @toggle-status="handleToggleStatus"
            />
        </div>

        <!-- Modal -->
        <SupplierModal
            :show="showModal"
            :params="params"
            :errors="errors"
            :saving="saving"
            @close="handleCloseModal"
            @save="handleSaveSupplier"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import { useMeta } from '@/composables/use-meta';
    import { useSuppliers } from './composables/useSuppliers';
    
    // Components
    import SuppliersHeader from './components/SuppliersHeader.vue';
    import AlertMessages from '../../components/shared/AlertMessages.vue';
    import SupplierStats from './components/SupplierStats.vue';
    import SuppliersListView from './components/SuppliersListView.vue';
    import SuppliersGridView from './components/SuppliersGridView.vue';
    import SupplierModal from './components/SupplierModal.vue';

    useMeta({ title: 'Gestión de Proveedores' });

    const displayType = ref<'list' | 'grid'>('list');
    const showModal = ref(false);

    // Use suppliers composable
    const {
        suppliersList,
        loading,
        saving,
        errorMessage,
        successMessage,
        errors,
        searchSupplier,
        filters,
        params,
        filteredSuppliersList,
        totalSuppliers,
        activeSuppliers,
        inactiveSuppliers,
        suppliersWithPurchaseOrders,
        fetchSuppliers,
        saveSupplier,
        deleteSupplier,
        toggleSupplierStatus,
        editSupplier,
        resetParams,
        showMessage
    } = useSuppliers();

    onMounted(() => {
        fetchSuppliers();
    });

    // Event handlers
    const handleAddSupplier = () => {
        editSupplier(null);
        showModal.value = true;
    };

    const handleEditSupplier = (supplier: any) => {
        editSupplier(supplier);
        showModal.value = true;
    };

    const handleCloseModal = () => {
        showModal.value = false;
        resetParams();
    };

    const handleSaveSupplier = async (supplierParams: any) => {
        const success = await saveSupplier();
        if (success) {
            handleCloseModal();
        }
    };

    const handleDeleteSupplier = async (supplier: any) => {
        await deleteSupplier(supplier);
    };

    const handleToggleStatus = async (supplier: any) => {
        await toggleSupplierStatus(supplier);
    };

    const handleFiltersUpdate = (newFilters: any) => {
        Object.assign(filters, newFilters);
        fetchSuppliers();
    };
</script>