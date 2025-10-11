import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Swal from 'sweetalert2';
import type { 
    PurchaseOrder, 
    PurchaseOrderFilters, 
    PurchaseOrderParams,
    PurchaseOrderItemParams,
    Supplier 
} from '@/types/purchase-orders';

export function usePurchaseOrders() {
    const { t } = useI18n();

    // States
    const ordersList = ref<PurchaseOrder[]>([]);
    const suppliers = ref<Supplier[]>([]);
    const products = ref<any[]>([]);
    const loading = ref(false);
    const saving = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref<Record<string, string[]>>({});
    const searchOrder = ref('');
    const viewModal = ref({
        show: false,
        data: null as PurchaseOrder | null
    });
    
    const filters = ref<PurchaseOrderFilters>({
        status: '',
        supplier_id: ''
    });

    const defaultParams: PurchaseOrderParams = {
        id: null,
        supplier_id: null,
        expected_delivery_date: null,
        notes: '',
        tax: 0,
        shipping: 0,
        items: []
    };

    const params = ref<PurchaseOrderParams>({ ...defaultParams });

    // Computed
    const filteredOrdersList = computed(() => {
        let filtered = ordersList.value;

        if (searchOrder.value) {
            const search = searchOrder.value.toLowerCase();
            filtered = filtered.filter((order) => 
                order.order_number.toLowerCase().includes(search) ||
                order.supplier.name.toLowerCase().includes(search)
            );
        }

        return filtered;
    });

    const totalOrders = computed(() => ordersList.value.length);
    const pendingOrders = computed(() => ordersList.value.filter(o => o.status === 'pending').length);
    const approvedOrders = computed(() => ordersList.value.filter(o => o.status === 'approved').length);
    const orderedOrders = computed(() => ordersList.value.filter(o => o.status === 'ordered').length);
    const receivedOrders = computed(() => ordersList.value.filter(o => o.status === 'received').length);

    // Methods
    const fetchOrders = async () => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const queryParams = new URLSearchParams();
            if (filters.value.status) queryParams.append('status', filters.value.status);
            if (filters.value.supplier_id) queryParams.append('supplier_id', filters.value.supplier_id);

            const response = await axios.get(`/api/purchase-orders?${queryParams}`);
            ordersList.value = response.data.data;
        } catch (error) {
            console.error('Error fetching orders:', error);
            errorMessage.value = t('purchase_orders_page.alerts.loading_error');
            showMessage(t('purchase_orders_page.alerts.loading_error'), 'error');
        } finally {
            loading.value = false;
        }
    };

    const fetchSuppliers = async () => {
        try {
            const response = await axios.get('/api/suppliers/for-select');
            suppliers.value = response.data.data;
        } catch (error) {
            console.error('Error fetching suppliers:', error);
        }
    };

    const fetchProducts = async () => {
        try {
            const response = await axios.get('/api/products/active/list');
            products.value = response.data.data;
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    };

    const saveOrder = async (): Promise<boolean> => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                const { data } = await axios.put(`/api/purchase-orders/${params.value.id}`, params.value);
                const index = ordersList.value.findIndex(order => order.id === params.value.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, data);
                }
                showMessage(t('purchase_orders_page.alerts.update_success'));
            } else {
                const { data } = await axios.post('/api/purchase-orders', params.value);
                ordersList.value.unshift(data);
                showMessage(t('purchase_orders_page.alerts.save_success'));
            }
            return true;
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = t('purchase_orders_page.alerts.validation_errors');
            } else {
                console.error('Error saving order:', error);
                errorMessage.value = t('purchase_orders_page.alerts.save_error');
                showMessage(t('purchase_orders_page.alerts.save_error'), 'error');
            }
            return false;
        } finally {
            saving.value = false;
        }
    };

    const deleteOrder = async (order: PurchaseOrder): Promise<boolean> => {
        if (!order.can_be_edited) {
            showMessage(t('purchase_orders_page.alerts.cannot_delete'), 'error');
            return false;
        }

        const result = await Swal.fire({
            title: t('purchase_orders_page.delete_confirm.title'),
            text: t('purchase_orders_page.delete_confirm.text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: t('purchase_orders_page.delete_confirm.confirm'),
            cancelButtonText: t('purchase_orders_page.delete_confirm.cancel')
        });

        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/purchase-orders/${order.id}`);
                ordersList.value = ordersList.value.filter(o => o.id !== order.id);
                showMessage(t('purchase_orders_page.alerts.delete_success'));
                return true;
            } catch (error: any) {
                console.error('Error deleting order:', error);
                if (error.response?.status === 400) {
                    showMessage(error.response.data.message, 'error');
                } else {
                    showMessage(t('purchase_orders_page.alerts.delete_error'), 'error');
                }
                return false;
            }
        }
        return false;
    };

    const approveOrder = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            await axios.put(`/api/purchase-orders/${order.id}/approve`);
            await fetchOrders(); // Refresh the list
            showMessage(t('purchase_orders_page.alerts.approve_success'));
            return true;
        } catch (error) {
            console.error('Error approving order:', error);
            showMessage(t('purchase_orders_page.alerts.save_error'), 'error');
            return false;
        }
    };

    const rejectOrder = async (order: PurchaseOrder, reason: string): Promise<boolean> => {
        try {
            await axios.put(`/api/purchase-orders/${order.id}/reject`, { reason });
            await fetchOrders(); // Refresh the list
            showMessage(t('purchase_orders_page.alerts.reject_success'));
            return true;
        } catch (error) {
            console.error('Error rejecting order:', error);
            showMessage(t('purchase_orders_page.alerts.save_error'), 'error');
            return false;
        }
    };

    const markAsOrdered = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            await axios.put(`/api/purchase-orders/${order.id}/mark-ordered`);
            await fetchOrders(); // Refresh the list
            showMessage(t('purchase_orders_page.alerts.mark_ordered_success'));
            return true;
        } catch (error) {
            console.error('Error marking order as ordered:', error);
            showMessage(t('purchase_orders_page.alerts.save_error'), 'error');
            return false;
        }
    };

    const receiveOrder = async (order: PurchaseOrder, receivedItems: any[]): Promise<boolean> => {
        try {
            await axios.put(`/api/purchase-orders/${order.id}/receive`, { items: receivedItems });
            await fetchOrders(); // Refresh the list
            showMessage(t('purchase_orders_page.alerts.receive_success'));
            return true;
        } catch (error) {
            console.error('Error receiving order:', error);
            showMessage(t('purchase_orders_page.alerts.save_error'), 'error');
            return false;
        }
    };

    const editOrder = (order: PurchaseOrder | null = null) => {
        errors.value = {};
        if (order) {
            params.value = {
                id: order.id,
                supplier_id: order.supplier_id,
                expected_delivery_date: order.expected_delivery_date,
                notes: order.notes || '',
                tax: parseFloat(order.tax.toString()),
                shipping: parseFloat(order.shipping.toString()),
                items: order.items.map(item => ({
                    id: item.id,
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: parseFloat(item.unit_price.toString()),
                    notes: item.notes || ''
                }))
            };
        } else {
            params.value = { ...defaultParams };
            params.value.items = [createEmptyItem()];
        }
    };

    const resetParams = () => {
        params.value = { ...defaultParams };
        errors.value = {};
    };

    const createEmptyItem = (): PurchaseOrderItemParams => ({
        product_id: null,
        quantity: 1,
        unit_price: 0,
        notes: ''
    });

    const addItem = () => {
        params.value.items.push(createEmptyItem());
    };

    const removeItem = (index: number) => {
        params.value.items.splice(index, 1);
        if (params.value.items.length === 0) {
            addItem();
        }
    };

    const calculateItemTotal = (item: PurchaseOrderItemParams): number => {
        return (item.quantity || 0) * (item.unit_price || 0);
    };

    const calculateOrderTotal = (): number => {
        const itemsTotal = params.value.items.reduce((total, item) => total + calculateItemTotal(item), 0);
        return itemsTotal + (params.value.tax || 0) + (params.value.shipping || 0);
    };

    const getStatusClass = (status: string): string => {
        const classes: Record<string, string> = {
            draft: 'bg-gray-100 text-gray-800',
            pending: 'bg-warning/10 text-warning',
            approved: 'bg-success/10 text-success',
            rejected: 'bg-danger/10 text-danger',
            ordered: 'bg-info/10 text-info',
            received: 'bg-secondary/10 text-secondary',
            cancelled: 'bg-danger/10 text-danger'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
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

    const formatDate = (dateString: string | null): string => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('es-ES');
    };

    const viewOrder = (order: PurchaseOrder) => {
        viewModal.value = {
            show: true,
            data: order
        };
    };

    const closeViewModal = () => {
        viewModal.value = {
            show: false,
            data: null
        };
    };

    

    return {
        // States
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
        viewModal,
        
        // Computed
        filteredOrdersList,
        totalOrders,
        pendingOrders,
        approvedOrders,
        orderedOrders,
        receivedOrders,
        
        // Methods
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
        calculateItemTotal,
        calculateOrderTotal,
        getStatusClass,
        showMessage,
        moneyFormat,
        formatDate,
        viewOrder,
        closeViewModal
    };
}