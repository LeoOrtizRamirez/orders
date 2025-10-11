// composables/useProducts.ts
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Swal from 'sweetalert2';
import type { Product } from '@/types/products';

interface ProductFilters {
    category: string;
    stock_status: string;
}

interface ProductParams {
    id: number | null;
    name: string;
    sku: string;
    description: string;
    price: number;
    cost: number;
    stock: number;
    min_stock: number;
    reorder_point: number;
    unit: string;
    category: string;
    brand: string;
    supplier: string;
    is_active: boolean;
    order: number;
}

export function useProducts() {
    const { t } = useI18n();

    // States
    const productsList = ref<Product[]>([]);
    const categories = ref<string[]>([]);
    const loading = ref(false);
    const saving = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref<Record<string, string[]>>({});
    const searchProduct = ref('');
    
    const filters = ref<ProductFilters>({
        category: '',
        stock_status: ''
    });

    const defaultParams: ProductParams = {
        id: null,
        name: '',
        sku: '',
        description: '',
        price: 0,
        cost: 0,
        stock: 0,
        min_stock: 10,
        reorder_point: 5,
        unit: 'kg',
        category: '',
        brand: '',
        supplier: '',
        is_active: true,
        order: 0
    };

    const params = ref<ProductParams>({ ...defaultParams });

    // Computed
    const filteredProductsList = computed(() => {
        let filtered = productsList.value;

        if (searchProduct.value) {
            const search = searchProduct.value.toLowerCase();
            filtered = filtered.filter((product) => 
                product.name.toLowerCase().includes(search) ||
                product.sku.toLowerCase().includes(search) ||
                product.description.toLowerCase().includes(search)
            );
        }

        return filtered;
    });

    const totalProducts = computed(() => productsList.value.length);
    const activeProducts = computed(() => productsList.value.filter(p => p.is_active).length);
    const lowStockProducts = computed(() => productsList.value.filter(p => p.stock <= p.reorder_point && p.stock > 0).length);
    const outOfStockProducts = computed(() => productsList.value.filter(p => p.stock === 0).length);

    // Methods
    const fetchProducts = async () => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const queryParams = new URLSearchParams();
            if (filters.value.category) queryParams.append('category', filters.value.category);
            if (filters.value.stock_status) queryParams.append('stock_status', filters.value.stock_status);

            const response = await axios.get(`/api/products?${queryParams}`);
            productsList.value = response.data.data;
        } catch (error) {
            console.error('Error fetching products:', error);
            errorMessage.value = t('products_page.alerts.loading_error');
            showMessage(t('products_page.alerts.loading_error'), 'error');
        } finally {
            loading.value = false;
        }
    };

    const fetchCategories = async () => {
        try {
            const response = await axios.get('/api/products/categories');
            categories.value = response.data.data;
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    };

    const saveProduct = async (): Promise<boolean> => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                const { data } = await axios.put(`/api/products/${params.value.id}`, params.value);
                const index = productsList.value.findIndex(product => product.id === params.value.id);
                if (index !== -1) {
                    productsList.value.splice(index, 1, data);
                }
                showMessage(t('products_page.alerts.update_success'));
            } else {
                const { data } = await axios.post('/api/products', params.value);
                productsList.value.unshift(data);
                showMessage(t('products_page.alerts.save_success'));
            }
            
            await fetchCategories();
            return true;
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = t('products_page.alerts.validation_errors');
            } else {
                console.error('Error al guardar producto:', error);
                errorMessage.value = t('products_page.alerts.save_error');
                showMessage(t('products_page.alerts.save_error'), 'error');
            }
            return false;
        } finally {
            saving.value = false;
        }
    };

    const deleteProduct = async (product: Product): Promise<boolean> => {
        if (product.purchase_order_items_count > 0) {
            showMessage(t('products_page.alerts.cannot_delete'), 'error');
            return false;
        }

        const result = await Swal.fire({
            title: t('products_page.delete_confirm.title'),
            text: t('products_page.delete_confirm.text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: t('products_page.delete_confirm.confirm'),
            cancelButtonText: t('products_page.delete_confirm.cancel')
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/products/${product.id}`);
                productsList.value = productsList.value.filter(p => p.id !== product.id);
                showMessage(t('products_page.alerts.delete_success'));
                return true;
            } catch (error: any) {
                console.error('Error eliminando producto:', error);
                if (error.response?.status === 400) {
                    showMessage(error.response.data.message, 'error');
                } else {
                    showMessage(t('products_page.alerts.delete_error'), 'error');
                }
                return false;
            }
        }
        return false;
    };

    const toggleProductStatus = async (product: Product): Promise<boolean> => {
        try {
            const { data } = await axios.put(`/api/products/${product.id}/toggle-status`);
            const index = productsList.value.findIndex(p => p.id === product.id);
            if (index !== -1) {
                productsList.value[index].is_active = data.is_active;
            }
            
            const status = data.is_active ? 'activado' : 'desactivado';
            showMessage(t('products_page.alerts.status_toggle_success', { status }));
            return true;
        } catch (error) {
            console.error('Error cambiando estado del producto:', error);
            showMessage(t('products_page.alerts.status_error'), 'error');
            return false;
        }
    };

    const editProduct = (product: Product | null = null) => {
        errors.value = {};
        if (product) {
            params.value = {
                id: product.id,
                name: product.name,
                sku: product.sku,
                description: product.description || '',
                price: parseFloat(product.price.toString()),
                cost: parseFloat((product.cost || 0).toString()),
                stock: product.stock,
                min_stock: product.min_stock,
                reorder_point: product.reorder_point,
                unit: product.unit || 'unidad',
                category: product.category || '',
                brand: product.brand || '',
                supplier: product.supplier || '',
                is_active: product.is_active,
                order: product.order || 0
            };
        } else {
            params.value = { ...defaultParams };
        }
    };

    const resetParams = () => {
        params.value = { ...defaultParams };
        errors.value = {};
    };

    const getStockClass = (product: Product): string => {
        if (product.stock === 0) return 'text-danger';
        if (product.stock <= product.reorder_point) return 'text-warning';
        return 'text-success';
    };

    const showMessage = (msg = '', type = 'success') => {
        if (type === 'success') {
            successMessage.value = msg;
        } else {
            errorMessage.value = msg;
        }
        
        const toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            customClass: { container: 'toast' },
        });
        toast.fire({
            icon: type,
            title: msg,
            padding: '10px 20px',
        });
    };

    const moneyFormat = (valor = 0): string => {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(valor);
    };

    return {
        // States
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
        
        // Computed
        filteredProductsList,
        totalProducts,
        activeProducts,
        lowStockProducts,
        outOfStockProducts,
        
        // Methods
        fetchProducts,
        fetchCategories,
        saveProduct,
        deleteProduct,
        toggleProductStatus,
        editProduct,
        resetParams,
        getStockClass,
        showMessage,
        moneyFormat
    };
}