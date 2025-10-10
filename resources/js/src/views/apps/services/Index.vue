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

        <!-- Modal -->
        <ProductModal
            :show="showModal"
            :params="params"
            :errors="errors"
            :saving="saving"
            :categories="categories"
            @close="handleCloseModal"
            @save="handleSaveProduct"
        />
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted } from 'vue';
    import { useMeta } from '@/composables/use-meta';
    import { useProducts } from './composables/useProducts';
    
    // Components
    import ProductsHeader from './components/ProductsHeader.vue';
    import AlertMessages from './components/AlertMessages.vue';
    import ProductStats from './components/ProductStats.vue';
    import ProductsListView from './components/ProductsListView.vue';
    import ProductsGridView from './components/ProductsGridView.vue';
    import ProductModal from './components/ProductModal.vue';

    useMeta({ title: 'Gestión de Productos' });

    const displayType = ref<'list' | 'grid'>('list');
    const showModal = ref(false);

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
        moneyFormat
    } = useProducts();

    onMounted(() => {
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

    const handleSaveProduct = async (productParams: any) => {
        const success = await saveProduct();
        if (success) {
            handleCloseModal();
        }
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