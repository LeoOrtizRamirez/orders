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
    const categories = ref<{id: string, name: string}[]>([]);
    const units = ref<{id: string, name: string}[]>([]);
    const loading = ref(false);
    const saving = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref<Record<string, string[]>>({});
    const searchProduct = ref('');
    
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
    });

    const filters = ref<ProductFilters>({
        category: '',
        stock_status: ''
    });

    const defaultParams: ProductParams = {
        id: null,
        name: '',
        sku: '',
        description: '',
        stock: 0,
        min_stock: 10,
        reorder_point: 5,
        unit: 'KILO',
        category: '',
        brand: '',
        supplier: '',
        is_active: true,
        order: 0
    };

    const params = ref<ProductParams>({ ...defaultParams });

    // Computed
    const totalProducts = computed(() => pagination.value.total);
    const activeProducts = computed(() => productsList.value.filter(p => p.is_active).length); // Note: This only reflects the current page
    const lowStockProducts = computed(() => productsList.value.filter(p => p.stock <= p.reorder_point && p.stock > 0).length); // Note: This only reflects the current page
    const outOfStockProducts = computed(() => productsList.value.filter(p => p.stock === 0).length); // Note: This only reflects the current page

    // Methods
    const fetchProducts = async (page: number = 1) => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const queryParams = new URLSearchParams();
            queryParams.append('page', page.toString());
            queryParams.append('per_page', pagination.value.per_page.toString());
            if (searchProduct.value) queryParams.append('search', searchProduct.value);
            if (filters.value.category) queryParams.append('category', filters.value.category);
            if (filters.value.stock_status) queryParams.append('stock_status', filters.value.stock_status);

            const response = await axios.get(`/api/products?${queryParams}`);
            productsList.value = response.data.data;
            pagination.value = response.data.meta;
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

    const fetchUnits = async () => {
        try {
            const response = await axios.get('/api/products/units');
            units.value = response.data.data;
        } catch (error) {
            console.error('Error fetching units:', error);
        }
    };

    const saveProduct = async (): Promise<boolean> => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                await axios.put(`/api/products/${params.value.id}`, params.value);
                showMessage(t('products_page.alerts.update_success'));
            } else {
                await axios.post('/api/products', params.value);
                showMessage(t('products_page.alerts.save_success'));
            }
            
            await fetchProducts(pagination.value.current_page); // Refresh current page
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
                await fetchProducts(pagination.value.current_page); // Refresh current page
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
            await axios.put(`/api/products/${product.id}/toggle-status`);
            await fetchProducts(pagination.value.current_page); // Refresh current page
            
            const status = !product.is_active ? 'activado' : 'desactivado';
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
                stock: product.stock,
                min_stock: product.min_stock,
                reorder_point: product.reorder_point,
                unit: product.unit || 'UNIDAD',
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

    const importProductsCsv = async (file: File): Promise<boolean> => {
        errorMessage.value = '';
        successMessage.value = '';

        try {
            const formData = new FormData();
            formData.append('file', file);

            const response = await axios.post('/api/products/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            if (response.data.success) {
                showMessage(t('products_page.import_modal.import_success', {
                    imported: response.data.imported,
                    updated: response.data.updated,
                    failed: response.data.failed
                }), 'success');
                await fetchProducts(); // Refresh first page
                return true;
            } else {
                showMessage(response.data.message || t('products_page.import_modal.import_failed'), 'error');
                return false;
            }
        } catch (error: any) {
            console.error('Error importing products:', error);
            if (error.response && error.response.status === 422) {
                // Handle validation errors from backend (e.g., CSV format issues)
                const validationErrors = error.response.data.errors;
                let errorMsg = t('products_page.import_modal.validation_errors') + '\n';
                for (const key in validationErrors) {
                    if (Object.prototype.hasOwnProperty.call(validationErrors, key)) {
                        errorMsg += `- ${validationErrors[key].join(', ')}\n`;
                    }
                }
                showMessage(errorMsg, 'error');
            } else {
                showMessage(error.response?.data?.message || t('products_page.import_modal.import_error'), 'error');
            }
            return false;
        }
    };

    const downloadProductsCsvTemplate = async (): Promise<boolean> => {
        errorMessage.value = '';
        successMessage.value = '';
        try {
            const response = await axios.get('/api/products/template', {
                responseType: 'blob', // Important for file downloads
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'products_template.csv');
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);

            showMessage(t('products_page.import_modal.template_downloaded'), 'success');
            return true;
        } catch (error: any) {
            console.error('Error downloading template:', error);
            showMessage(error.response?.data?.message || t('products_page.import_modal.template_download_error'), 'error');
            return false;
        }
    };

    return {
        // States
        productsList,
        categories,
        units,
        loading,
        saving,
        errorMessage,
        successMessage,
        errors,
        searchProduct,
        filters,
        params,
        pagination,
        
        // Computed
        totalProducts,
        activeProducts,
        lowStockProducts,
        outOfStockProducts,
        
        // Methods
        fetchProducts,
        fetchCategories,
        fetchUnits,
        saveProduct,
        deleteProduct,
        toggleProductStatus,
        editProduct,
        resetParams,
        getStockClass,
        showMessage,
        moneyFormat,
        importProductsCsv,
        downloadProductsCsvTemplate
    };
}