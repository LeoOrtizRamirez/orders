import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Swal from 'sweetalert2';
import type { 
    PurchaseOrder, 
    PurchaseOrderFilters, 
    PurchaseOrderParams,
    PurchaseOrderItemParams,
    Supplier,
    UserNote 
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
    const orderNotes = ref<UserNote[] | null>(null); // New reactive state for order notes
    
    const filters = ref<PurchaseOrderFilters>({
        status: '',
        supplier_id: ''
    });

    const defaultParams: PurchaseOrderParams = {
        id: null,
        supplier_id: null,
        expected_delivery_date: null,
        notes: '', // For the new note input
        items: []
    };

    const params = ref<PurchaseOrderParams>({ ...defaultParams });

    // Computed
    const filteredOrdersList = computed(() => {
        let filtered = ordersList.value;

        if (searchOrder.value) {
            const search = searchOrder.value.toLowerCase();
            filtered = filtered.filter((order) => 
                order.order_number?.toLowerCase().includes(search) ||
                order.supplier?.name?.toLowerCase().includes(search)
            );
        }

        if (filters.value.status) {
            filtered = filtered.filter(order => order.status === filters.value.status);
        }

        if (filters.value.supplier_id) {
            filtered = filtered.filter(order => order.supplier_id === parseInt(filters.value.supplier_id));
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
            
            // Manejar diferentes estructuras de respuesta
            if (Array.isArray(response.data)) {
                ordersList.value = response.data;
            } else if (response.data && Array.isArray(response.data.data)) {
                ordersList.value = response.data.data;
            } else if (response.data && Array.isArray(response.data.orders)) {
                ordersList.value = response.data.orders;
            } else {
                console.warn('Estructura de respuesta inesperada:', response.data);
                ordersList.value = [];
            }
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
            if (Array.isArray(response.data)) {
                suppliers.value = response.data;
            } else if (response.data && Array.isArray(response.data.data)) {
                suppliers.value = response.data.data;
            } else {
                console.warn('Estructura de respuesta inesperada para suppliers:', response.data);
                suppliers.value = [];
            }
        } catch (error) {
            console.error('Error fetching suppliers:', error);
            suppliers.value = [];
        }
    };

    const fetchProducts = async () => {
        try {
            const response = await axios.get('/api/products/active/list');
            if (Array.isArray(response.data)) {
                products.value = response.data;
            } else if (response.data && Array.isArray(response.data.data)) {
                products.value = response.data.data;
            } else {
                console.warn('Estructura de respuesta inesperada para products:', response.data);
                products.value = [];
            }
        } catch (error) {
            console.error('Error fetching products:', error);
            products.value = [];
        }
    };

    // Función para obtener información del producto
    const getProductInfo = (productId: number | null) => {
        if (!productId) return null;
        return products.value.find(product => product.id === productId) || null;
    };

    // Función para validar cantidad contra stock
    const validateQuantity = (productId: number | null, quantity: number): { valid: boolean; message: string } => {
        if (!productId) {
            return { valid: false, message: 'Producto no seleccionado' };
        }
        
        const product = getProductInfo(productId);
        if (!product) {
            return { valid: false, message: 'Producto no encontrado' };
        }
        
        const currentStock = product.current_stock || 0;
        if (quantity > currentStock) {
            return { 
                valid: false, 
                message: `Stock insuficiente. Stock disponible: ${currentStock}` 
            };
        }
        
        return { valid: true, message: '' };
    };

    const saveOrder = async (): Promise<boolean> => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            let response;
            if (params.value.id) {
                response = await axios.put(`/api/purchase-orders/${params.value.id}`, params.value);
            } else {
                response = await axios.post('/api/purchase-orders', params.value);
            }

            // Manejar diferentes estructuras de respuesta
            const savedOrder = response.data.data || response.data;

            if (params.value.id) {
                const index = ordersList.value.findIndex(order => order.id === params.value.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, savedOrder);
                }
                showMessage(t('purchase_orders_page.alerts.update_success'));
            } else {
                ordersList.value.unshift(savedOrder);
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
        if (!order.can_be_deleted) {
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

    // Métodos de estado
    const submitOrder = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            const response = await axios.post(`/api/purchase-orders/${order.id}/submit`);
            
            if (response.data.success) {
                const index = ordersList.value.findIndex(o => o.id === order.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, response.data.order);
                }
                showMessage(t('purchase_orders_page.alerts.submit_success'));
                return true;
            } else {
                showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                return false;
            }
        } catch (error: any) {
            console.error('Error submitting order:', error);
            const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
            showMessage(message, 'error');
            return false;
        }
    };

    const approveOrder = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            const response = await axios.post(`/api/purchase-orders/${order.id}/approve`);
            
            if (response.data.success) {
                const index = ordersList.value.findIndex(o => o.id === order.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, response.data.order);
                }
                showMessage(t('purchase_orders_page.alerts.approve_success'));
                return true;
            } else {
                showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                return false;
            }
        } catch (error: any) {
            console.error('Error approving order:', error);
            const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
            showMessage(message, 'error');
            return false;
        }
    };

    const rejectOrder = async (order: PurchaseOrder): Promise<boolean> => {
        const { value: reason } = await Swal.fire({
            title: t('purchase_orders_page.reject_confirm.title'),
            input: 'textarea',
            inputLabel: t('purchase_orders_page.reject_confirm.input_label'),
            inputPlaceholder: t('purchase_orders_page.reject_confirm.input_placeholder'),
            inputAttributes: {
                'aria-label': t('purchase_orders_page.reject_confirm.input_label')
            },
            showCancelButton: true,
            confirmButtonText: t('purchase_orders_page.reject_confirm.confirm'),
            cancelButtonText: t('purchase_orders_page.reject_confirm.cancel'),
            inputValidator: (value) => {
                if (!value) {
                    return t('purchase_orders_page.reject_confirm.input_required');
                }
                return null;
            }
        });

        if (reason) {
            try {
                const response = await axios.post(`/api/purchase-orders/${order.id}/reject`, {
                    rejection_reason: reason
                });
                
                if (response.data.success) {
                    const index = ordersList.value.findIndex(o => o.id === order.id);
                    if (index !== -1) {
                        ordersList.value.splice(index, 1, response.data.order);
                    }
                    showMessage(t('purchase_orders_page.alerts.reject_success'));
                    return true;
                } else {
                    showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                    return false;
                }
            } catch (error: any) {
                console.error('Error rejecting order:', error);
                const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
                showMessage(message, 'error');
                return false;
            }
        }
        return false;
    };

    const markAsOrdered = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            const response = await axios.post(`/api/purchase-orders/${order.id}/mark-ordered`);
            
            if (response.data.success) {
                const index = ordersList.value.findIndex(o => o.id === order.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, response.data.order);
                }
                showMessage(t('purchase_orders_page.alerts.mark_ordered_success'));
                return true;
            } else {
                showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                return false;
            }
        } catch (error: any) {
            console.error('Error marking order as ordered:', error);
            const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
            showMessage(message, 'error');
            return false;
        }
    };

    const receiveOrder = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            const response = await axios.post(`/api/purchase-orders/${order.id}/receive`);
            
            if (response.data.success) {
                const index = ordersList.value.findIndex(o => o.id === order.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, response.data.order);
                }
                showMessage(t('purchase_orders_page.alerts.receive_success'));
                return true;
            } else {
                showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                return false;
            }
        } catch (error: any) {
            console.error('Error receiving order:', error);
            const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
            showMessage(message, 'error');
            return false;
        }
    };

    const cancelOrder = async (order: PurchaseOrder): Promise<boolean> => {
        const { value: reason } = await Swal.fire({
            title: t('purchase_orders_page.cancel_confirm.title'),
            input: 'textarea',
            inputLabel: t('purchase_orders_page.cancel_confirm.input_label'),
            inputPlaceholder: t('purchase_orders_page.cancel_confirm.input_placeholder'),
            showCancelButton: true,
            confirmButtonText: t('purchase_orders_page.cancel_confirm.confirm'),
            cancelButtonText: t('purchase_orders_page.cancel_confirm.cancel')
        });

        if (reason !== undefined) {
            try {
                const response = await axios.post(`/api/purchase-orders/${order.id}/cancel`, {
                    reason: reason || null
                });
                
                if (response.data.success) {
                    const index = ordersList.value.findIndex(o => o.id === order.id);
                    if (index !== -1) {
                        ordersList.value.splice(index, 1, response.data.order);
                    }
                    showMessage(t('purchase_orders_page.alerts.cancel_success'));
                    return true;
                } else {
                    showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                    return false;
                }
            } catch (error: any) {
                console.error('Error canceling order:', error);
                const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
                showMessage(message, 'error');
                return false;
            }
        }
        return false;
    };

    const reopenOrder = async (order: PurchaseOrder): Promise<boolean> => {
        try {
            const response = await axios.post(`/api/purchase-orders/${order.id}/reopen`);
            
            if (response.data.success) {
                const index = ordersList.value.findIndex(o => o.id === order.id);
                if (index !== -1) {
                    ordersList.value.splice(index, 1, response.data.order);
                }
                showMessage(t('purchase_orders_page.alerts.reopen_success'));
                return true;
            } else {
                showMessage(response.data.message || t('purchase_orders_page.alerts.save_error'), 'error');
                return false;
            }
        } catch (error: any) {
            console.error('Error reopening order:', error);
            const message = error.response?.data?.message || t('purchase_orders_page.alerts.save_error');
            showMessage(message, 'error');
            return false;
        }
    };

    const splitOrder = async (orderId: number, splitData: { items: { item_id: number; quantity: number; notes?: string }[], expected_delivery_date: string | null, notes: string | null }): Promise<boolean> => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            const response = await axios.post(`/api/purchase-orders/${orderId}/split`, splitData);
            
            if (response.data) {
                // Assuming the backend returns the updated parent order and the new sub-order
                // We need to refresh the entire list to reflect changes in both.
                await fetchOrders(); 
                showMessage(t('purchase_orders_page.alerts.split_success'));
                return true;
            } else {
                showMessage(response.data.message || t('purchase_orders_page.alerts.split_error'), 'error');
                return false;
            }
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = t('purchase_orders_page.alerts.validation_errors');
            } else {
                console.error('Error splitting order:', error);
                errorMessage.value = t('purchase_orders_page.alerts.split_error');
                showMessage(t('purchase_orders_page.alerts.split_error'), 'error');
            }
            return false;
        } finally {
            saving.value = false;
        }
    };

    // Helper to fetch order details without opening view modal
    const fetchOrderDetails = async (orderId: number): Promise<PurchaseOrder | null> => {
        loading.value = true;
        try {
            const response = await axios.get(`/api/purchase-orders/${orderId}`);
            return response.data;
        } catch (error) {
            console.error('Error fetching order details:', error);
            showMessage(t('purchase_orders_page.alerts.loading_error'), 'error');
            return null;
        } finally {
            loading.value = false;
        }
    };

    // Métodos helpers
    const editOrder = async (order: PurchaseOrder | null = null) => {
        errors.value = {};
        if (order) {
            // Use internal helper so we don't trigger the view modal
            const fullOrder = await fetchOrderDetails(order.id);

            if (!fullOrder) {
                return;
            }

            // Set orderNotes for the main order
            orderNotes.value = fullOrder.notes; 
            
            // Formatear la fecha a YYYY-MM-DD para el input type="date"
            const formattedDate = fullOrder.expected_delivery_date 
                ? new Date(fullOrder.expected_delivery_date).toISOString().split('T')[0] 
                : null;

            params.value = {
                id: fullOrder.id,
                supplier_id: fullOrder.supplier_id,
                expected_delivery_date: formattedDate,
                items: fullOrder.items?.map(item => ({
                    id: item.id,
                    product_id: item.product_id,
                    quantity: item.quantity,
                    // Ensure itemNotes are included
                    itemNotes: item.item_notes || [],
                    notes: '' // Initialize notes
                })) || []
            };
        } else {
            orderNotes.value = null; // Clear orderNotes when creating a new order
            params.value = { ...defaultParams, items: [createEmptyItem()] };
        }
    };

    const resetParams = () => {
        params.value = { ...defaultParams };
        orderNotes.value = null; // Also clear orderNotes on reset
        errors.value = {};
    };


    const createEmptyItem = (): PurchaseOrderItemParams => ({
        product_id: null,
        quantity: 1,
        itemNotes: [] // Initialize with empty array
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
        });
        toast.fire({
            icon: type,
            title: msg,
            padding: '10px 20px',
        });
    };

    const formatDate = (dateString: string | null): string => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('es-ES');
    };

    const viewOrder = async (orderId: number) => { // Accept ID
        viewModal.value = {
            show: true,
            data: null // Clear previous data while loading
        };
        const data = await fetchOrderDetails(orderId);
        if (data) {
            viewModal.value.data = data;
        } else {
            viewModal.value.show = false; // Close if failed
        }
    };

    const refreshViewOrderData = async (orderId: number) => {
        const data = await fetchOrderDetails(orderId);
        if (data) {
            viewModal.value.data = data;
        }
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
        orderNotes, // Include orderNotes in the returned object
        
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
        getProductInfo,
        validateQuantity,
        saveOrder,
        deleteOrder,
        submitOrder,
        approveOrder,
        rejectOrder,
        markAsOrdered,
        receiveOrder,
        cancelOrder,
        reopenOrder,
        editOrder,
        resetParams,
        addItem,
        removeItem,
        getStatusClass,
        showMessage,
        formatDate,
        viewOrder,
        refreshViewOrderData, // Expose new method
        closeViewModal,
        splitOrder // Add new splitOrder method
    };
}