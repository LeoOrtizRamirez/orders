import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Swal from 'sweetalert2';
import type { Supplier } from '@/types/suppliers';

interface SupplierFilters {
    active: string;
}

interface SupplierParams {
    id: number | null;
    name: string;
    contact_person: string;
    email: string;
    phone: string;
    address: string;
    city: string;
    country: string;
    tax_id: string;
    payment_terms: string;
    notes: string;
    is_active: boolean;
}

export function useSuppliers() {
    const { t } = useI18n();

    // States
    const suppliersList = ref<Supplier[]>([]);
    const loading = ref(false);
    const saving = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref<Record<string, string[]>>({});
    const searchSupplier = ref('');
    
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
    });
    
    const filters = ref<SupplierFilters>({
        active: ''
    });

    const defaultParams: SupplierParams = {
        id: null,
        name: '',
        contact_person: '',
        email: '',
        phone: '',
        address: '',
        city: '',
        country: '',
        tax_id: '',
        payment_terms: '',
        notes: '',
        is_active: true
    };

    const params = ref<SupplierParams>({ ...defaultParams });

    // Computed
    const totalSuppliers = computed(() => pagination.value.total);
    const activeSuppliers = computed(() => suppliersList.value.filter(s => s.is_active).length); // This only reflects the current page
    const inactiveSuppliers = computed(() => suppliersList.value.filter(s => !s.is_active).length); // This only reflects the current page
    const suppliersWithPurchaseOrders = computed(() => suppliersList.value.filter(s => s.purchase_orders_count > 0).length); // This only reflects the current page

    // Methods
    const fetchSuppliers = async (page: number = 1) => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const queryParams = new URLSearchParams();
            queryParams.append('page', page.toString());
            queryParams.append('per_page', pagination.value.per_page.toString());
            if (searchSupplier.value) queryParams.append('search', searchSupplier.value);
            if (filters.value.active) queryParams.append('active', filters.value.active);

            const response = await axios.get(`/api/suppliers?${queryParams}`);
            suppliersList.value = response.data.data;
            pagination.value = response.data.meta;
        } catch (error) {
            console.error('Error fetching suppliers:', error);
            errorMessage.value = t('suppliers_page.alerts.loading_error');
            showMessage(t('suppliers_page.alerts.loading_error'), 'error');
        } finally {
            loading.value = false;
        }
    };

    const saveSupplier = async (): Promise<boolean> => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                await axios.put(`/api/suppliers/${params.value.id}`, params.value);
                showMessage(t('suppliers_page.alerts.update_success'));
            } else {
                await axios.post('/api/suppliers', params.value);
                showMessage(t('suppliers_page.alerts.save_success'));
            }
            await fetchSuppliers(pagination.value.current_page);
            return true;
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = t('suppliers_page.alerts.validation_errors');
            } else {
                console.error('Error al guardar proveedor:', error);
                errorMessage.value = t('suppliers_page.alerts.save_error');
                showMessage(t('suppliers_page.alerts.save_error'), 'error');
            }
            return false;
        } finally {
            saving.value = false;
        }
    };

    const deleteSupplier = async (supplier: Supplier): Promise<boolean> => {
        if (supplier.purchase_orders_count > 0) {
            showMessage(t('suppliers_page.alerts.cannot_delete'), 'error');
            return false;
        }

        const result = await Swal.fire({
            title: t('suppliers_page.delete_confirm.title'),
            text: t('suppliers_page.delete_confirm.text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: t('suppliers_page.delete_confirm.confirm'),
            cancelButtonText: t('suppliers_page.delete_confirm.cancel')
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/suppliers/${supplier.id}`);
                await fetchSuppliers(pagination.value.current_page);
                showMessage(t('suppliers_page.alerts.delete_success'));
                return true;
            } catch (error: any) {
                console.error('Error eliminando proveedor:', error);
                if (error.response?.status === 400) {
                    showMessage(error.response.data.message, 'error');
                } else {
                    showMessage(t('suppliers_page.alerts.delete_error'), 'error');
                }
                return false;
            }
        }
        return false;
    };

    const toggleSupplierStatus = async (supplier: Supplier): Promise<boolean> => {
        try {
            await axios.put(`/api/suppliers/${supplier.id}/toggle-status`);
            await fetchSuppliers(pagination.value.current_page);
            
            const status = !supplier.is_active ? 'activado' : 'desactivado';
            showMessage(t('suppliers_page.alerts.status_toggle_success', { status }));
            return true;
        } catch (error) {
            console.error('Error cambiando estado del proveedor:', error);
            showMessage(t('suppliers_page.alerts.status_error'), 'error');
            return false;
        }
    };

    const editSupplier = (supplier: Supplier | null = null) => {
        errors.value = {};
        if (supplier) {
            params.value = {
                id: supplier.id,
                name: supplier.name,
                contact_person: supplier.contact_person || '',
                email: supplier.email || '',
                phone: supplier.phone || '',
                address: supplier.address || '',
                city: supplier.city || '',
                country: supplier.country || '',
                tax_id: supplier.tax_id || '',
                payment_terms: supplier.payment_terms || '',
                notes: supplier.notes || '',
                is_active: supplier.is_active
            };
        } else {
            params.value = { ...defaultParams };
        }
    };

    const resetParams = () => {
        params.value = { ...defaultParams };
        errors.value = {};
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

    return {
        // States
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
        
        // Computed
        totalSuppliers,
        activeSuppliers,
        inactiveSuppliers,
        suppliersWithPurchaseOrders,
        
        // Methods
        fetchSuppliers,
        saveSupplier,
        deleteSupplier,
        toggleSupplierStatus,
        editSupplier,
        resetParams,
        showMessage
    };
}