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
                :products="filteredProductsList"
                :loading="loading"
                @edit-product="handleEditProduct"
                @delete-product="handleDeleteProduct"
                @toggle-status="handleToggleStatus"
            />
            
            <ProductsGridView
                v-else
                :products="filteredProductsList"
                :loading="loading"
                @edit-product="handleEditProduct"
                @delete-product="handleDeleteProduct"
                @toggle-status="handleToggleStatus"
            />
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
    import { ref, onMounted } from 'vue';
    import { useMeta } from '@/composables/use-meta';
    import { useProducts } from './composables/useProducts';
    
    // Components
    import ProductsHeader from './components/ProductsHeader.vue';
    import AlertMessages from '../../components/shared/AlertMessages.vue';
    import ProductStats from './components/ProductStats.vue';
    import ProductsListView from './components/ProductsListView.vue';
    import ProductsGridView from './components/ProductsGridView.vue';
    import ProductModal from './components/ProductModal.vue';
    import ProductImportModal from './components/ProductImportModal.vue'; // New component

    useMeta({ title: 'Gestión de Productos' });

    const displayType = ref<'list' | 'grid'>('list');
    const showModal = ref(false);
    const showImportModal = ref(false); // New state for import modal
    const importLoading = ref(false); // New state for import loading

    // Use products composable
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
        filteredProductsList,
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

    // Console logs for debugging
    console.log('--- Products.vue Setup Debug ---');
    console.log('categories from useProducts:', categories.value);
    console.log('showImportModal (ref):', showImportModal.value);
    console.log('importLoading (ref):', importLoading.value);
    console.log('--- End Products.vue Setup Debug ---');

    onMounted(() => {
        console.log('Products.vue onMounted hook triggered.'); // Confirm onMounted is running
        fetchProducts();
        fetchCategories();
    });

    // Event handlers
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

    const handleSaveProduct = async () => { // Adjusted signature
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
                fetchProducts(); // Refresh products list
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
        fetchProducts();
    };
</script>