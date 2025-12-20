<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Reporte de Inventario</h1>
            <p class="text-gray-500">Gestión de stock y disponibilidad de productos.</p>
        </div>

        <div v-if="loading" class="text-center">Cargando datos...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="reportData">
            <!-- Estadísticas Generales de Inventario -->
            <ProductStats
                :total-products="reportData.product_stats?.total || 0"
                :active-products="reportData.product_stats?.active || 0"
                :low-stock-products="reportData.product_stats?.low_stock || 0"
                :out-of-stock-products="reportData.product_stats?.out_of_stock || 0"
                class="mb-6"
            />

            <!-- Lista de Productos (Paginada y Filtrable) -->
            <div class="panel">
                <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-4">
                    <div>
                        <h2 class="text-xl font-bold">Inventario de Productos</h2>
                        <div class="text-sm text-gray-500" v-if="productsPagination.total > 0">
                            Total: {{ productsPagination.total }} productos
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Filtro Categoría -->
                        <div class="flex items-center gap-2">
                            <label for="category" class="font-bold text-xs whitespace-nowrap">Categoría:</label>
                            <select id="category" class="form-select w-40" v-model="filters.category" @change="fetchProductsList(1)">
                                <option value="">Todas</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <!-- Filtro Unidad -->
                        <div class="flex items-center gap-2">
                            <label for="unit" class="font-bold text-xs whitespace-nowrap">Unidad:</label>
                            <select id="unit" class="form-select w-32" v-model="filters.unit" @change="fetchProductsList(1)">
                                <option value="">Todas</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                        </div>

                        <!-- Filtro Stock -->
                        <div class="flex items-center gap-2">
                            <label for="stockStatus" class="font-bold text-xs whitespace-nowrap">Estado Stock:</label>
                            <select id="stockStatus" class="form-select w-40" v-model="filters.stockStatus" @change="fetchProductsList(1)">
                                <option value="">Todos</option>
                                <option value="low">Bajo Stock</option>
                                <option value="out">Sin Stock</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive mb-4">
                    <table class="table-striped table-hover">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th class="text-center">
                                    <div class="flex items-center justify-center gap-1 cursor-help" 
                                         @mouseenter="showTooltip($event, 'Cantidad real existente en bodega.')" 
                                         @mouseleave="hideTooltip">
                                        <span class="border-b border-dotted border-gray-400">Físico</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="flex items-center justify-center gap-1 cursor-help"
                                         @mouseenter="showTooltip($event, 'Suma de productos en órdenes activas (pendientes de facturar).')" 
                                         @mouseleave="hideTooltip">
                                        <span class="border-b border-dotted border-gray-400">Requerido</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="flex items-center justify-center gap-1 cursor-help"
                                         @mouseenter="showTooltip($event, 'Stock Físico menos Requerido. Si es negativo, falta mercancía.')" 
                                         @mouseleave="hideTooltip">
                                        <span class="border-b border-dotted border-gray-400">Disponible</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    </div>
                                </th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="productsLoading">
                                <td colspan="7" class="text-center py-4">Cargando inventario...</td>
                            </tr>
                            <tr v-else-if="productsList.length === 0">
                                <td colspan="7" class="text-center py-4">No se encontraron productos.</td>
                            </tr>
                            <tr v-else v-for="product in productsList" :key="product.id">
                                <td>{{ product.sku }}</td>
                                <td>
                                    <div class="font-semibold">{{ product.name }}</div>
                                    <div class="text-xs text-gray-500">{{ product.unit }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary/10 text-primary">{{ product.category }}</span>
                                </td>
                                <td class="text-center">
                                    <span :class="getStockClass(product)" class="font-bold">{{ product.stock }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-gray-600 font-semibold">
                                        {{ product.committed_stock || 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span 
                                        class="font-bold" 
                                        :class="[
                                            getNetAvailabilityClass(product), 
                                            calculateNetStock(product) < 0 ? 'cursor-pointer hover:underline' : ''
                                        ]"
                                        @click="calculateNetStock(product) < 0 ? openPendingOrdersModal(product) : null"
                                        :title="calculateNetStock(product) < 0 ? 'Ver órdenes pendientes' : ''"
                                    >
                                        {{ calculateNetStock(product) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" :class="product.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'">
                                        {{ product.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación y Selector -->
                <div class="flex flex-col md:flex-row justify-between items-center mt-4" v-if="productsPagination.last_page > 1 || productsPagination.total > 0">
                    <div class="flex items-center gap-2 mb-4 md:mb-0">
                        <span class="text-sm">Mostrar:</span>
                        <select class="form-select w-20 h-9" :value="productsPagination.per_page" @change="updatePerPage">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                        <li>
                            <button type="button" 
                                class="flex justify-center font-semibold px-3.5 py-2 rounded transition text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light"
                                :disabled="productsPagination.current_page === 1"
                                @click="changeProductPage(productsPagination.current_page - 1)">
                                Prev
                            </button>
                        </li>
                        <li v-for="(page, index) in displayedPages" :key="index">
                            <button
                                v-if="page !== '...'"
                                type="button"
                                class="flex justify-center font-semibold px-3.5 py-2 rounded transition"
                                :class="{'text-primary border-2 border-primary dark:border-primary dark:text-white-light': productsPagination.current_page === page, 'text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light': productsPagination.current_page !== page}"
                                @click="changeProductPage(page as number)"
                            >
                                {{ page }}
                            </button>
                            <span v-else class="px-3.5 py-2">...</span>
                        </li>
                        <li>
                            <button type="button" 
                                class="flex justify-center font-semibold px-3.5 py-2 rounded transition text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light"
                                :disabled="productsPagination.current_page === productsPagination.last_page"
                                @click="changeProductPage(productsPagination.current_page + 1)">
                                Next
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tabla de Stock Completa (Solo lectura rápida si se necesita) -->
            <!-- Nota: La lista paginada reemplaza la antigua tabla de "Stock de Productos" -->
        </div>

        <!-- Tooltip Flotante Global -->
        <div v-if="tooltipState.visible" 
             class="fixed z-[9999] px-3 py-2 text-xs font-medium text-white bg-black rounded shadow-lg pointer-events-none transform -translate-x-1/2 -translate-y-full mb-2 whitespace-normal max-w-xs text-center"
             :style="{ top: tooltipState.y + 'px', left: tooltipState.x + 'px' }">
            {{ tooltipState.text }}
            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-2 h-2 bg-black rotate-45"></div>
        </div>

        <!-- Modal de Órdenes Pendientes -->
        <div v-if="showOrdersModal" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeOrdersModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-[#1b2e4b] rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-[#1b2e4b] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ltr:ml-4 sm:rtl:mr-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white-light" id="modal-title">
                                    Órdenes Pendientes: {{ currentProduct?.name }}
                                </h3>
                                <div class="mt-4">
                                    <div v-if="ordersLoading" class="text-center py-4">
                                        <span class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 inline-block"></span>
                                    </div>
                                    <div v-else-if="pendingOrdersList.length === 0" class="text-center py-4 text-gray-500">
                                        No hay órdenes pendientes encontradas.
                                    </div>
                                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700 max-h-60 overflow-y-auto">
                                        <li v-for="order in pendingOrdersList" :key="order.id" class="py-3 flex justify-between items-center">
                                            <div>
                                                <button 
                                                    type="button" 
                                                    class="text-sm font-medium text-primary hover:underline focus:outline-none"
                                                    @click="goToOrder(order.id)"
                                                    title="Ver orden en Pipeline"
                                                >
                                                    #{{ order.order_number }}
                                                </button>
                                                <p class="text-xs text-gray-500">{{ order.supplier }} - {{ order.date }}</p>
                                                <span class="badge bg-info/10 text-info mt-1">{{ order.status }}</span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white-light">{{ order.quantity_requested }} {{ currentProduct?.unit }}</p>
                                                <p class="text-xs text-gray-500">Solicitado</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-[#121c2c] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeOrdersModal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, reactive, computed } from 'vue';
import { useMeta } from '@/composables/use-meta';
import { useRouter } from 'vue-router';
import axios from 'axios';
import ProductStats from '../products/components/ProductStats.vue'; 

useMeta({ title: 'Reporte de Inventario' });

const router = useRouter();
const loading = ref(true);
const error = ref('');
const reportData = ref<any>(null);
const categories = ref<any[]>([]);
const units = ref<any[]>([]); 

// Modal State
const showOrdersModal = ref(false);
const ordersLoading = ref(false);
const pendingOrdersList = ref<any[]>([]);
const currentProduct = ref<any>(null);

// Estado del Tooltip
const tooltipState = reactive({
    visible: false,
    text: '',
    x: 0,
    y: 0
});

const showTooltip = (event: MouseEvent, text: string) => {
    const target = event.currentTarget as HTMLElement;
    const rect = target.getBoundingClientRect();
    
    // Posicionar arriba del elemento centrado
    tooltipState.x = rect.left + rect.width / 2;
    tooltipState.y = rect.top - 5; // Un poco arriba del elemento
    tooltipState.text = text;
    tooltipState.visible = true;
};

const hideTooltip = () => {
    tooltipState.visible = false;
};

// Open Modal Logic
const openPendingOrdersModal = async (product: any) => {
    currentProduct.value = product;
    showOrdersModal.value = true;
    ordersLoading.value = true;
    pendingOrdersList.value = [];

    try {
        const response = await axios.get(`/api/products/${product.id}/pending-orders`);
        pendingOrdersList.value = response.data.data;
    } catch (e) {
        console.error('Error fetching pending orders', e);
    } finally {
        ordersLoading.value = false;
    }
};

const closeOrdersModal = () => {
    showOrdersModal.value = false;
    currentProduct.value = null;
};

const goToOrder = (orderId: number) => {
    closeOrdersModal();
    router.push({ 
        name: 'purchase-orders-kanban', 
        params: { orderId: orderId.toString() } 
    });
};

// Estados para la lista de productos
const productsList = ref<any[]>([]);
const productsLoading = ref(false);
const productsPagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 15
});

const displayedPages = computed(() => {
    const currentPage = productsPagination.value.current_page;
    const lastPage = productsPagination.value.last_page;
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

const filters = ref({
    category: '',
    unit: '', // Nuevo filtro
    stockStatus: '', 
});

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/products/categories');
        categories.value = response.data.data;
    } catch (e) {
        console.error('Error fetching categories', e);
    }
};

const fetchUnits = async () => {
    try {
        const response = await axios.get('/api/products/units');
        units.value = response.data.data;
    } catch (e) {
        console.error('Error fetching units', e);
    }
};

const fetchReportData = async () => {
    // Usamos el endpoint general solo para obtener los KPIs globales (ProductStats)
    // No necesitamos fechas aquí, solo el estado actual
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.get('/api/reports/sales');
        reportData.value = response.data;
    } catch (err) {
        error.value = 'No se pudieron cargar los datos del reporte.';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const fetchProductsList = async (page = 1) => {
    productsLoading.value = true;
    try {
        const params: any = {
            page: page,
            per_page: productsPagination.value.per_page,
            category: filters.value.category,
            unit: filters.value.unit, // Nuevo parámetro
            stock_status: filters.value.stockStatus,
        };
        
        const response = await axios.get('/api/products', { params });
        productsList.value = response.data.data;
        productsPagination.value = {
            current_page: response.data.meta.current_page,
            last_page: response.data.meta.last_page,
            total: response.data.meta.total,
            per_page: response.data.meta.per_page,
        };
    } catch (e) {
        console.error('Error fetching products list', e);
    } finally {
        productsLoading.value = false;
    }
};

const changeProductPage = (page: number) => {
    if (page > 0 && page <= productsPagination.value.last_page && page !== productsPagination.value.current_page) {
        fetchProductsList(page);
    }
};

const updatePerPage = (e: Event) => {
    const target = e.target as HTMLSelectElement;
    productsPagination.value.per_page = parseInt(target.value);
    fetchProductsList(1);
};

const getStockClass = (product: any): string => {
    if (product.stock === 0) return 'text-danger';
    if (product.stock <= product.reorder_point) return 'text-warning';
    return 'text-success';
};

const calculateNetStock = (product: any): number => {
    const committed = parseInt(product.committed_stock || 0);
    return product.stock - committed;
};

const getNetAvailabilityClass = (product: any): string => {
    const net = calculateNetStock(product);
    if (net < 0) return 'text-danger bg-danger/10 px-2 py-1 rounded'; // Déficit crítico
    if (net === 0) return 'text-warning bg-warning/10 px-2 py-1 rounded'; // Al límite
    return 'text-success';
};

onMounted(() => {
    fetchCategories();
    fetchUnits(); // Llamar a unidades
    fetchReportData(); // Para KPIs
    fetchProductsList(1); // Para Tabla
});
</script>