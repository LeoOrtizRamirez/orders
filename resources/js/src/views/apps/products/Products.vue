<template>
    <div>
        <!-- Header -->
        <ProductsHeader
            :display-type="displayType"
            :filters="filters"
            :categories="categories"
            :search-product="searchProduct"
            @update:display-type="displayType = $event"
            @update:filters="handleFiltersUpdate"
            @update:search-product="searchProduct = $event"
            @add-product="handleAddProduct"
            @open-import-modal="handleShowImportModal"
            @download-template="handleDownloadTemplate"
        />

        <!-- Alertas -->
        <AlertMessages
            :error-message="errorMessage"
            :success-message="successMessage"
            @clear-error="errorMessage = ''"
            @clear-success="successMessage = ''"
        />

        <!-- Estadísticas -->
        <ProductStats
            :total-products="totalProducts"
            :active-products="activeProducts"
            :low-stock-products="lowStockProducts"
            :out-of-stock-products="outOfStockProducts"
        />

        <!-- Vista de productos -->
        <div class="mt-5">
            <ProductsListView
                v-if="displayType === 'list'"
                :products="productsList"
                :loading="loading"
                @edit-product="handleEditProduct"
                @delete-product="handleDeleteProduct"
                @toggle-status="handleToggleStatus"
            />
            
            <ProductsGridView
                v-else
                :products="productsList"
                :loading="loading"
                @edit-product="handleEditProduct"
                @delete-product="handleDeleteProduct"
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

        <!-- Modal de Creación/Edición -->
        <ProductModal
            :show="showModal"
            :params="params"
            :errors="errors"
            :saving="saving"
            :categories="categories"
            @close="handleCloseModal"
            @save="handleSaveProduct"
        />

        <!-- Modal de Importación de Productos -->
        <ProductImportModal
            :show="showImportModal"
            :loading="importLoading"
            @close="handleCloseImportModal"
            @import="handleImportCsv"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed } from 'vue';
    import { useMeta } from '@/composables/use-meta';
    import { useProducts } from './composables/useProducts';
    
    // Components
    import ProductsHeader from './components/ProductsHeader.vue';
    import AlertMessages from '../../components/shared/AlertMessages.vue';
    import ProductStats from './components/ProductStats.vue';
    import ProductsListView from './components/ProductsListView.vue';
    import ProductsGridView from './components/ProductsGridView.vue';
    import ProductModal from './components/ProductModal.vue';
    import ProductImportModal from './components/ProductImportModal.vue';

    useMeta({ title: 'Gestión de Productos' });

    const displayType = ref<'list' | 'grid'>('list');
    const showModal = ref(false);
    const showImportModal = ref(false);
    const importLoading = ref(false);

    const {
        productsList,
        categories,
        loading,
        saving,
        errorMessage,
        successMessage,
        errors,
        searchProduct,
        filters,
        params,
        pagination,
        totalProducts,
        activeProducts,
        lowStockProducts,
        outOfStockProducts,
        fetchProducts,
        fetchCategories,
        saveProduct,
        deleteProduct,
        toggleProductStatus,
        editProduct,
        resetParams,
        showMessage,
        moneyFormat,
        importProductsCsv,
        downloadProductsCsvTemplate
    } = useProducts();

    onMounted(() => {
        fetchProducts(1);
        fetchCategories();
    });

    const changePage = (page: number) => {
        if (page > 0 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
            fetchProducts(page);
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

    const handleAddProduct = () => {
        editProduct(null);
        showModal.value = true;
    };

    const handleEditProduct = (product: any) => {
        editProduct(product);
        showModal.value = true;
    };

    const handleCloseModal = () => {
        showModal.value = false;
        resetParams();
    };

    const handleSaveProduct = async () => {
        const success = await saveProduct();
        if (success) {
            handleCloseModal();
        }
    };

    const handleDownloadTemplate = async () => {
        await downloadProductsCsvTemplate();
    };

    const handleShowImportModal = () => {
        showImportModal.value = true;
    };

    const handleImportCsv = async (file: File) => {
        importLoading.value = true;
        try {
            const success = await importProductsCsv(file);
            if (success) {
                showImportModal.value = false;
                fetchProducts(1);
            }
        } finally {
            importLoading.value = false;
        }
    };

    const handleCloseImportModal = () => {
        showImportModal.value = false;
    };

    const handleDeleteProduct = async (product: any) => {
        await deleteProduct(product);
    };

    const handleToggleStatus = async (product: any) => {
        await toggleProductStatus(product);
    };

    const handleFiltersUpdate = (newFilters: any) => {
        Object.assign(filters, newFilters);
        fetchProducts(1);
    };
</script>