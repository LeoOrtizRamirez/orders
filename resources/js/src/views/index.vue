<template>
    <div>
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">{{ $t('dashboard_page.dashboard') }}</a>
            </li>
            <li class="before:content-['/'] before:mr-2 rtl:before:ml-2">
                <span>{{ $t('dashboard_page.analytics') }}</span>
            </li>
        </ul>
        <div class="pt-5">

            <!-- Filtros -->
            <div class="panel mb-5">
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Selector de vista (solo para admin) -->
                    <div v-if="authStore.isAdmin()">
                        <label class="block text-sm font-medium mb-1">{{ $t('dashboard_page.view') }}:</label>
                        <select v-model="filters.view" @change="onViewChange" class="form-select">
                            <option value="personal">{{ $t('dashboard_page.my_analytics') }}</option>
                            <option value="user">{{ $t('dashboard_page.specific_user') }}</option>
                            <option value="global">{{ $t('dashboard_page.global_view') }}</option>
                        </select>
                    </div>

                    <!-- Selector de usuario (solo para admin en vista específica) -->
                    <div v-if="authStore.isAdmin() && filters.view === 'user'">
                        <label class="block text-sm font-medium mb-1">{{ $t('dashboard_page.user') }}:</label>
                        <select v-model="filters.selectedUserId" @change="onUserChange" class="form-select">
                            <option value="">{{ $t('dashboard_page.select_user') }}</option>
                            <option v-for="user in usersList" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                    </div>

                    <!-- Selector de período -->
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ $t('dashboard_page.period') }}:</label>
                        <select v-model="filters.period" @change="onPeriodChange" class="form-select">
                            <option value="monthly">{{ $t('dashboard_page.monthly') }}</option>
                            <option value="yearly">{{ $t('dashboard_page.yearly') }}</option>
                        </select>
                    </div>

                    <!-- Selector de año -->
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ $t('dashboard_page.year') }}:</label>
                        <select v-model="filters.year" @change="fetchMetrics" class="form-select">
                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>

                    <!-- Selector de mes -->
                    <div v-if="filters.period === 'monthly'">
                        <label class="block text-sm font-medium mb-1">{{ $t('dashboard_page.month') }}:</label>
                        <select v-model="filters.month" @change="fetchMetrics" class="form-select">
                            <option value="">{{ $t('dashboard_page.entire_year') }}</option>
                            <option v-for="(month, index) in months" :key="index" :value="index + 1">
                                {{ $t(`dashboard_page.months.${month.key}`) }}
                            </option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="ml-auto flex gap-2">
                        <button @click="fetchMetrics" class="btn btn-primary mt-6">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ $t('dashboard_page.update') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información del usuario seleccionado -->
            <div v-if="selectedUserInfo && filters.view === 'user'" class="panel mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ selectedUserInfo.name }}</h3>
                        <p class="text-gray-600">{{ selectedUserInfo.email }}</p>
                        <p class="text-sm text-gray-500">{{ $t('dashboard_page.member_since') }}: {{ selectedUserInfo.member_since }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-primary">${{ selectedUserInfo.total_ganancias?.toLocaleString() }}</p>
                        <p class="text-sm text-gray-500">{{ $t('dashboard_page.total_earnings') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-1 gap-6 mb-6">
                <div class="panel h-full p-0 lg:col-span-2">
                    <div class="flex items-start justify-between dark:text-white-light mb-5 p-5 border-b border-[#e0e6ed] dark:border-[#1b2e4b]">
                        <h5 class="font-semibold text-lg">{{ chartTitle }}</h5>
                        <div class="dropdown">
                            <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-start' : 'bottom-end'" offsetDistance="0" class="align-middle">
                                <button type="button">
                                    <svg class="w-5 h-5 text-black/70 dark:text-white/70 hover:!text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="5" cy="12" r="2" stroke="currentColor" stroke-width="1.5" />
                                        <circle opacity="0.5" cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.5" />
                                        <circle cx="19" cy="12" r="2" stroke="currentColor" stroke-width="1.5" />
                                    </svg>
                                </button>
                                <template #content="{ close }">
                                    <ul @click="close()">
                                        <!-- <li><a href="javascript:;" @click="exportData">{{ $t('dashboard_page.export_data') }}</a></li> -->
                                        <li><a href="javascript:;" @click="fetchMetrics">{{ $t('dashboard_page.refresh') }}</a></li>
                                    </ul>
                                </template>
                            </Popper>
                        </div>
                    </div>

                    <!-- Loader -->
                    <div v-if="loading" class="min-h-[360px] grid place-content-center bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08]">
                        <span class="animate-spin border-2 border-black dark:border-white !border-l-transparent rounded-full w-5 h-5 inline-flex"></span>
                    </div>

                    <!-- Gráfico -->
                    <apexchart 
                        v-else-if="metricsData && metricsData.has_data" 
                        height="360" 
                        :options="chartOptions" 
                        :series="chartSeries" 
                        class="overflow-hidden"
                    ></apexchart>

                    <!-- Sin datos -->
                    <div v-else class="min-h-[360px] grid place-content-center bg-white-light/30 dark:bg-dark dark:bg-opacity-[0.08]">
                        <div class="text-center">
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">{{ $t('dashboard_page.no_data_available') }}</p>
                            <button @click="fetchMetrics" class="btn btn-primary">{{ $t('dashboard_page.refresh_data') }}</button>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div v-if="metricsData && !loading" class="p-5 border-t border-[#e0e6ed] dark:border-[#1b2e4b]">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">{{ $t('dashboard_page.total_visits') }}</p>
                                <p class="text-xl font-semibold">{{ (metricsData.summary.total_visitas || 0).toLocaleString() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">{{ $t('dashboard_page.earnings') }}</p>
                                <p class="text-xl font-semibold">${{ (metricsData.summary.total_ganancias || 0).toLocaleString() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">{{ $t('dashboard_page.new_subscriptions') }}</p>
                                <p class="text-xl font-semibold">{{ (metricsData.summary.total_nuevas_suscripciones || 0).toLocaleString() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500" v-if="metricsData.scope === 'global'">{{ $t('dashboard_page.active_users') }}</p>
                                <p class="text-sm text-gray-500" v-else>{{ $t('dashboard_page.renewals') }}</p>
                                <p class="text-xl font-semibold">
                                    {{ metricsData.scope === 'global' ? 
                                       (metricsData.summary.total_usuarios_activos || 0).toLocaleString() : 
                                       (metricsData.summary.total_renovaciones || 0).toLocaleString() }}
                                </p>
                            </div>
                        </div>
                        <!-- Estadísticas adicionales para vista global -->
                        <div v-if="metricsData.scope === 'global'" class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">{{ $t('dashboard_page.avg_visits_per_user') }}</p>
                                <p class="text-lg font-semibold">{{ (metricsData.summary.avg_visitas_por_usuario || 0).toFixed(1) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">{{ $t('dashboard_page.avg_earnings_per_user') }}</p>
                                <p class="text-lg font-semibold">${{ (metricsData.summary.avg_ganancias_por_usuario || 0).toFixed(2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { ref, computed, onMounted } from 'vue';
    import apexchart from 'vue3-apexcharts';
    import { useAppStore } from '@/stores/index';
    import { useMeta } from '@/composables/use-meta';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    useMeta({ title: 'Analytics Admin' });
    const store = useAppStore();
    const loading = ref(true);
    const metricsData = ref<any>(null);
    const usersList = ref<any[]>([]);
    const authStore = useAuthStore();

    // Filtros
    const filters = ref({
        view: 'personal',
        selectedUserId: null as number | null,
        period: 'monthly',
        year: new Date().getFullYear(),
        month: null as number | null
    });

    // Datos disponibles
    const availableYears = computed(() => {
        const currentYear = new Date().getFullYear();
        return Array.from({ length: 5 }, (_, i) => currentYear - i);
    });

    // Meses con claves para traducción
    const months = [
        { key: 'january', name: 'Enero' },
        { key: 'february', name: 'Febrero' },
        { key: 'march', name: 'Marzo' },
        { key: 'april', name: 'Abril' },
        { key: 'may', name: 'Mayo' },
        { key: 'june', name: 'Junio' },
        { key: 'july', name: 'Julio' },
        { key: 'august', name: 'Agosto' },
        { key: 'september', name: 'Septiembre' },
        { key: 'october', name: 'Octubre' },
        { key: 'november', name: 'Noviembre' },
        { key: 'december', name: 'Diciembre' }
    ];

    // Información del usuario seleccionado
    const selectedUserInfo = computed(() => {
        if (filters.value.view === 'user' && filters.value.selectedUserId) {
            return usersList.value.find(u => u.id === filters.value.selectedUserId);
        }
        return null;
    });

    // Título del gráfico con traducciones
    const chartTitle = computed(() => {
        let title = '';
        
        if (filters.value.view === 'global') {
            title = t('dashboard_page.global_metrics') + ' - ';
        } else if (filters.value.view === 'user' && selectedUserInfo.value) {
            title = t('dashboard_page.user_metrics', { name: selectedUserInfo.value.name }) + ' - ';
        } else {
            title = t('dashboard_page.my_metrics') + ' - ';
        }

        if (filters.value.period === 'yearly') {
            title += t('dashboard_page.yearly_view', { 
                startYear: filters.value.year - 4, 
                endYear: filters.value.year 
            });
        } else if (filters.value.month) {
            const monthName = t(`dashboard_page.months.${months[filters.value.month - 1].key}`);
            title += t('dashboard_page.monthly_view', { 
                month: monthName, 
                year: filters.value.year 
            });
        } else {
            title += t('dashboard_page.monthly_view_year', { year: filters.value.year });
        }

        return title;
    });

    // Cargar lista de usuarios (solo para admin)
    const loadUsersList = async () => {
        if (!authStore.isAdmin()) return;

        try {
            const response = await axios.get('/api/profile-metrics/users');
            usersList.value = response.data.data;
        } catch (error) {
            console.error('Error loading users list:', error);
        }
    };

    // Fetch datos
    const fetchMetrics = async () => {
        try {
            loading.value = true;
            
            let url = '/api/profile-metrics';
            let params: any = {
                period: filters.value.period,
                year: filters.value.year,
                month: filters.value.month
            };

            if (filters.value.view === 'global') {
                url = '/api/profile-metrics/global';
            } else if (filters.value.view === 'user' && filters.value.selectedUserId) {
                params.user_id = filters.value.selectedUserId;
            }

            const response = await axios.get(url, { params });
            metricsData.value = response.data.data;
            
        } catch (error) {
            console.error('Error fetching metrics:', error);
        } finally {
            loading.value = false;
        }
    };

    // Cambio de vista
    const onViewChange = () => {
        if (filters.value.view === 'user') {
            loadUsersList();
        } else {
            filters.value.selectedUserId = null;
        }
        fetchMetrics();
    };

    // Cambio de usuario
    const onUserChange = () => {
        fetchMetrics();
    };

    // Cambio de período
    const onPeriodChange = () => {
        if (filters.value.period === 'yearly') {
            filters.value.month = null;
        }
        fetchMetrics();
    };

    // Exportar datos
    const exportData = () => {
        console.log('Exportar datos:', metricsData.value);
    };

    // Opciones del gráfico
    const chartOptions = computed(() => {
        const isDark: boolean = store.theme === 'dark' || store.isDarkMode;
        const isRtl = store.rtlClass === 'rtl';
        
        return {
            chart: {
                height: 360,
                type: filters.value.period === 'yearly' ? 'line' : 'bar',
                fontFamily: 'Nunito, sans-serif',
                toolbar: { show: false },
            },
            dataLabels: { enabled: false },
            stroke: {
                width: filters.value.period === 'yearly' ? 3 : 2,
                colors: filters.value.period === 'yearly' ? ['#5c1ac3', '#ffbb44', '#00ab55', '#e7515a'] : ['transparent'],
            },
            colors: ['#5c1ac3', '#ffbb44', '#00ab55', '#e7515a', '#8b5cf6'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 10,
                },
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                itemMargin: { horizontal: 8, vertical: 8 },
            },
            grid: {
                borderColor: isDark ? '#191e3a' : '#e0e6ed',
                padding: { left: 20, right: 20 },
            },
            xaxis: {
                categories: metricsData.value?.labels || [],
                axisBorder: {
                    show: true,
                    color: isDark ? '#3b3f5c' : '#e0e6ed',
                },
            },
            yaxis: {
                tickAmount: 6,
                opposite: isRtl,
                labels: { offsetX: isRtl ? -10 : 0 },
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: isDark ? 'dark' : 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 0.8,
                    stops: [0, 100],
                },
            },
            tooltip: {
                marker: { show: true },
                y: { formatter: (val: any) => val?.toLocaleString() || '0' },
            },
        };
    });

    // Series del gráfico con traducciones
    const chartSeries = computed(() => {
        if (!metricsData.value) return [];

        const series = [
            {
                name: t('dashboard_page.profile_visits'),
                data: metricsData.value.datasets?.visitas || [],
            },
            {
                name: t('dashboard_page.new_subscriptions'),
                data: metricsData.value.datasets?.nuevas_suscripciones || [],
            }
        ];

        if (metricsData.value.scope === 'global') {
            series.push(
                {
                    name: t('dashboard_page.earnings'),
                    data: metricsData.value.datasets?.ganancias || [],
                },
                {
                    name: t('dashboard_page.renewals'),
                    data: metricsData.value.datasets?.renovaciones || [],
                },
                {
                    name: t('dashboard_page.active_users'),
                    data: metricsData.value.datasets?.usuarios_activos || [],
                }
            );
        } else {
            series.push(
                {
                    name: t('dashboard_page.earnings'),
                    data: metricsData.value.datasets?.ganancias || [],
                },
                {
                    name: t('dashboard_page.renewals'),
                    data: metricsData.value.datasets?.renovaciones || [],
                }
            );
        }

        return series;
    });

    onMounted(async () => {
        if (authStore.isAdmin()) {
            await loadUsersList();
        }
        await fetchMetrics();
    });
</script>