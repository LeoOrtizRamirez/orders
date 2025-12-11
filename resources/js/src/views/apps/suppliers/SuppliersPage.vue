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
                :suppliers="suppliersList"
                :loading="loading"
                @edit-supplier="handleEditSupplier"
                @delete-supplier="handleDeleteSupplier"
                @toggle-status="handleToggleStatus"
            />
            
            <SuppliersGridView
                v-else
                :suppliers="suppliersList"
                :loading="loading"
                @edit-supplier="handleEditSupplier"
                @delete-supplier="handleDeleteSupplier"
                @toggle-status="handleToggleStatus"
            />
        </div>

        <!-- Paginación -->
        <div class="mt-6 flex justify-center">
            <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse m-auto mb-4">
                <li>
                    <button
                        type="button"
                        class="flex justify-center font-semibold px-3.5 py-2 rounded transition text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light"
                        :disabled="pagination.current_page === 1"
                        @click="changePage(pagination.current_page - 1)"
                    >
                        Prev
                    </button>
                </li>
                <li v-for="(page, index) in displayedPages" :key="index">
                    <button
                        v-if="page !== '...'"
                        type="button"
                        class="flex justify-center font-semibold px-3.5 py-2 rounded transition"
                        :class="{'text-primary border-2 border-primary dark:border-primary dark:text-white-light': pagination.current_page === page, 'text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light': pagination.current_page !== page}"
                        @click="changePage(page as number)"
                    >
                        {{ page }}
                    </button>
                    <span v-else class="px-3.5 py-2">...</span>
                </li>
                <li>
                    <button
                        type="button"
                        class="flex justify-center font-semibold px-3.5 py-2 rounded transition text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light"
                        :disabled="pagination.current_page === pagination.last_page"
                        @click="changePage(pagination.current_page + 1)"
                    >
                        Next
                    </button>
                </li>
            </ul>
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
    import { ref, onMounted, computed } from 'vue';
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
        pagination,
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
        fetchSuppliers(1);
    });

    const changePage = (page: number) => {
        if (page > 0 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
            fetchSuppliers(page);
        }
    };

    const displayedPages = computed(() => {
        const currentPage = pagination.value.current_page;
        const lastPage = pagination.value.last_page;
        const delta = 2;
        const pages: (number | string)[] = [];

        if (lastPage <= 7) {
            for (let i = 1; i <= lastPage; i++) {
                pages.push(i);
            }
            return pages;
        }

        pages.push(1);
        if (currentPage > delta + 2) {
            pages.push('...');
        }

        const start = Math.max(2, currentPage - delta);
        const end = Math.min(lastPage - 1, currentPage + delta);

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        if (currentPage < lastPage - delta - 1) {
            pages.push('...');
        }
        
        pages.push(lastPage);

        return pages;
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

    const handleSaveSupplier = async () => {
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
        fetchSuppliers(1);
    };
</script>