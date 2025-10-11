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
    const filteredSuppliersList = computed(() => {
        let filtered = suppliersList.value;

        if (searchSupplier.value) {
            const search = searchSupplier.value.toLowerCase();
            filtered = filtered.filter((supplier) => 
                supplier.name.toLowerCase().includes(search) ||
                supplier.contact_person?.toLowerCase().includes(search) ||
                supplier.email?.toLowerCase().includes(search) ||
                supplier.phone?.toLowerCase().includes(search)
            );
        }

        return filtered;
    });

    const totalSuppliers = computed(() => suppliersList.value.length);
    const activeSuppliers = computed(() => suppliersList.value.filter(s => s.is_active).length);
    const inactiveSuppliers = computed(() => suppliersList.value.filter(s => !s.is_active).length);
    const suppliersWithPurchaseOrders = computed(() => suppliersList.value.filter(s => s.purchase_orders_count > 0).length);

    // Methods
    const fetchSuppliers = async () => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const queryParams = new URLSearchParams();
            if (filters.value.active) queryParams.append('active', filters.value.active);

            const response = await axios.get(`/api/suppliers?${queryParams}`);
            suppliersList.value = response.data.data;
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
                const { data } = await axios.put(`/api/suppliers/${params.value.id}`, params.value);
                const index = suppliersList.value.findIndex(supplier => supplier.id === params.value.id);
                if (index !== -1) {
                    suppliersList.value.splice(index, 1, data);
                }
                showMessage(t('suppliers_page.alerts.update_success'));
            } else {
                const { data } = await axios.post('/api/suppliers', params.value);
                suppliersList.value.unshift(data);
                showMessage(t('suppliers_page.alerts.save_success'));
            }
            
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
                suppliersList.value = suppliersList.value.filter(s => s.id !== supplier.id);
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
            const { data } = await axios.put(`/api/suppliers/${supplier.id}/toggle-status`);
            const index = suppliersList.value.findIndex(s => s.id === supplier.id);
            if (index !== -1) {
                suppliersList.value[index].is_active = data.is_active;
            }
            
            const status = data.is_active ? 'activado' : 'desactivado';
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
        
        // Computed
        filteredSuppliersList,
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