<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Reporte de Operaciones</h1>
            <p class="text-gray-500">Métricas de eficiencia, proveedores y equipo.</p>
        </div>

        <!-- Filtros -->
        <div class="panel mb-6">
            <div class="flex flex-wrap items-end gap-4 justify-between">
                <!-- Grupo Fechas -->
                <div class="flex flex-wrap gap-4 flex-1">
                    <div>
                        <label class="font-bold text-xs mb-1 block">Rango de Fechas</label>
                        <div class="flex gap-2">
                            <input type="date" class="form-input w-40" v-model="filters.startDate" title="Fecha Inicio" />
                            <input type="date" class="form-input w-40" v-model="filters.endDate" title="Fecha Fin" />
                        </div>
                    </div>
                </div>

                <!-- Grupo Filtros -->
                <div class="flex flex-wrap gap-4 flex-1 justify-end">
                    <div class="w-48">
                        <label for="status" class="font-bold text-xs mb-1 block">Estado Orden</label>
                        <select id="status" class="form-select" v-model="filters.status">
                            <option value="">Todos los Estados</option>
                            <option v-for="(label, key) in orderStatuses" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button class="btn btn-primary" @click="fetchReportData">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path opacity="0.5" d="M14 5H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M14 8H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M21 11.5C21 16.75 16.75 21 11.5 21C6.25 21 2 16.75 2 11.5C2 6.25 6.25 2 11.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M22 22L20 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center">Cargando datos...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="reportData" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Estado de Órdenes -->
            <div class="panel">
                <h2 class="text-xl font-bold mb-4">Estado de Órdenes</h2>
                <div v-if="reportData.orders_by_status?.length">
                    <apexchart height="300" type="pie" :options="statusChartOptions" :series="statusChartSeries"></apexchart>
                </div>
                <div v-else class="text-center py-10 text-gray-500">No hay órdenes en este periodo</div>
            </div>

            <!-- Top Clientes (Suppliers) -->
            <div class="panel">
                <h2 class="text-xl font-bold mb-4">Top 5 Clientes (Órdenes)</h2>
                <div v-if="reportData.top_suppliers?.length">
                    <apexchart height="300" type="bar" :options="suppliersChartOptions" :series="suppliersChartSeries"></apexchart>
                </div>
                <div v-else class="text-center py-10 text-gray-500">No hay datos de clientes</div>
            </div>

            <!-- Ranking Usuarios -->
            <div class="panel lg:col-span-2">
                <h2 class="text-xl font-bold mb-4">Órdenes Creadas por Usuario</h2>
                <div v-if="reportData.top_creators?.length">
                    <apexchart height="300" type="bar" :options="creatorsChartOptions" :series="creatorsChartSeries"></apexchart>
                </div>
                <div v-else class="text-center py-10 text-gray-500">No hay datos de usuarios</div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue';
import { useMeta } from '@/composables/use-meta';
import axios from 'axios';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';

useMeta({ title: 'Reporte de Operaciones' });

const store = useAppStore();
const loading = ref(true);
const error = ref('');
const reportData = ref<any>(null);

const today = new Date().toISOString().split('T')[0];
const thirtyDaysAgo = new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0];

const filters = ref({
    startDate: thirtyDaysAgo,
    endDate: today,
    status: '',
});

const orderStatuses = {
    'nuevo pedido': 'Nuevo Pedido',
    'disponibilidad': 'Disponibilidad',
    'preparar pedido': 'Preparar Pedido',
    'en preparación': 'En Preparación',
    'facturación': 'Facturación',
    'en despacho': 'En Despacho',
    'en ruta': 'En Ruta',
    'entregado': 'Entregado',
    'cancelado': 'Cancelado'
};

const fetchReportData = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.get('/api/reports/sales', {
            params: {
                start_date: filters.value.startDate,
                end_date: filters.value.endDate,
                order_status: filters.value.status,
                // No enviamos categoría aquí ya que es ops
            }
        });
        reportData.value = response.data;
    } catch (err) {
        error.value = 'No se pudieron cargar los datos del reporte.';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

// --- Options for Orders Status Pie Chart ---
const statusChartOptions = computed(() => {
    const isDark = store.theme === 'dark' || store.isDarkMode;
    return {
        chart: {
            type: 'pie',
            height: 300,
        },
        labels: reportData.value?.orders_by_status.map((d: any) => d.status.charAt(0).toUpperCase() + d.status.slice(1)) || [], 
        colors: ['#2196f3', '#00ab55', '#e2a03f', '#e7515a', '#805dca', '#4361ee'],
        stroke: { show: true, colors: isDark ? '#0e1726' : '#fff' },
        legend: { position: 'bottom' },
        responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
    };
});

const statusChartSeries = computed(() => {
    return reportData.value?.orders_by_status.map((d: any) => d.total) || [];
});

// --- Options for Top Suppliers (Bar Chart) ---
const suppliersChartOptions = computed(() => {
    const isDark = store.theme === 'dark' || store.isDarkMode;
    return {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        colors: ['#00ab55'],
        xaxis: {
            categories: reportData.value?.top_suppliers.map((d: any) => d.name) || [],
            axisBorder: { color: isDark ? '#191e3a' : '#e0e6ed' },
        },
        grid: { borderColor: isDark ? '#191e3a' : '#e0e6ed' },
        tooltip: { theme: isDark ? 'dark' : 'light' },
    };
});

const suppliersChartSeries = computed(() => [{
    name: 'Órdenes',
    data: reportData.value?.top_suppliers.map((d: any) => d.total_orders) || []
}]);

// --- Options for Top Creators (Bar Chart) ---
const creatorsChartOptions = computed(() => {
    const isDark = store.theme === 'dark' || store.isDarkMode;
    return {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true } },
        colors: ['#805dca'],
        xaxis: {
            categories: reportData.value?.top_creators.map((d: any) => d.name) || [],
            axisBorder: { color: isDark ? '#191e3a' : '#e0e6ed' },
        },
        grid: { borderColor: isDark ? '#191e3a' : '#e0e6ed' },
        tooltip: { theme: isDark ? 'dark' : 'light' },
    };
});

const creatorsChartSeries = computed(() => [{
    name: 'Órdenes Creadas',
    data: reportData.value?.top_creators.map((d: any) => d.total_orders) || []
}]);

onMounted(() => {
    fetchReportData();
});
</script>