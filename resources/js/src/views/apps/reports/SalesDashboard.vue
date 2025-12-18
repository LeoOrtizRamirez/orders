<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Reporte de Ventas</h1>
            <p class="text-gray-500">Análisis de productos vendidos y tendencias.</p>
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
                        <label for="category" class="font-bold text-xs mb-1 block">Filtrar por Categoría</label>
                        <select id="category" class="form-select" v-model="filters.category">
                            <option value="">Todas las Categorías</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button class="btn btn-primary" @click="fetchReportData">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path opacity="0.5" d="M14 5H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M14 8H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M21 11.5C21 16.75 16.75 21 11.5 21C6.25 21 2 16.75 2 11.5C2 6.25 6.25 2 11.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M22 22L20 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>
                            Actualizar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center">Cargando datos...</div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div v-if="reportData">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico de Ventas a lo largo del tiempo -->
                <div class="panel lg:col-span-2">
                    <h2 class="text-xl font-bold mb-4">Tendencia de Ventas (Cantidad)</h2>
                    <apexchart height="300" :options="salesChartOptions" :series="salesChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>
                </div>

                <!-- Ventas por Categoría -->
                <div class="panel">
                    <h2 class="text-xl font-bold mb-4">Ventas por Categoría</h2>
                    <div v-if="reportData.sales_by_category?.length">
                        <apexchart height="300" type="pie" :options="categoryChartOptions" :series="categoryChartSeries"></apexchart>
                    </div>
                    <div v-else class="text-center py-10 text-gray-500">No hay datos de ventas en este periodo</div>
                </div>

                <!-- Tabla de Productos más vendidos -->
                <div class="panel">
                    <h2 class="text-xl font-bold mb-4">Top 10 Productos Más Vendidos</h2>
                    <div class="table-responsive">
                        <table class="table-hover">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Total Vendido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in reportData.top_sold_products" :key="product.id">
                                    <td>{{ product.sku }}</td>
                                    <td>{{ product.name }}</td>
                                    <td>{{ product.total_sold }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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

useMeta({ title: 'Reporte de Ventas' });

const store = useAppStore();
const loading = ref(true);
const error = ref('');
const reportData = ref<any>(null);
const categories = ref<any[]>([]);

const today = new Date().toISOString().split('T')[0];
const thirtyDaysAgo = new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0];

const filters = ref({
    startDate: thirtyDaysAgo,
    endDate: today,
    category: '',
});

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/products/categories');
        categories.value = response.data.data;
    } catch (e) {
        console.error('Error fetching categories', e);
    }
};

const fetchReportData = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await axios.get('/api/reports/sales', {
            params: {
                start_date: filters.value.startDate,
                end_date: filters.value.endDate,
                category: filters.value.category,
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

onMounted(() => {
    fetchCategories();
    fetchReportData();
});

const salesChartOptions = computed(() => {
    const isDark = store.theme === 'dark' || store.isDarkMode;
    return {
        chart: { type: 'line', height: 300, toolbar: { show: false } },
        colors: ['#194C8D'],
        stroke: { width: 2, curve: 'smooth' },
        xaxis: {
            categories: reportData.value?.sales_over_time.map((d: any) => d.date) || [],
            axisBorder: { color: isDark ? '#191e3a' : '#e0e6ed' },
        },
        yaxis: { labels: { formatter: (value: number) => { return value.toFixed(0); } } },
        grid: { borderColor: isDark ? '#191e3a' : '#e0e6ed' },
        tooltip: { theme: isDark ? 'dark' : 'light' },
    };
});

const salesChartSeries = computed(() => [
    { name: 'Cantidad Vendida', data: reportData.value?.sales_over_time.map((d: any) => d.total_quantity) || [] },
]);

const categoryChartOptions = computed(() => {
    const isDark = store.theme === 'dark' || store.isDarkMode;
    return {
        chart: { type: 'pie', height: 300 },
        labels: reportData.value?.sales_by_category.map((d: any) => d.category) || [],
        colors: ['#4361ee', '#805dca', '#00ab55', '#e7515a', '#e2a03f', '#2196f3'],
        stroke: { show: true, colors: isDark ? '#0e1726' : '#fff' },
        legend: { position: 'bottom' },
        responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
    };
});

const categoryChartSeries = computed(() => {
    return reportData.value?.sales_by_category.map((d: any) => parseInt(d.total_quantity)) || [];
});
</script>