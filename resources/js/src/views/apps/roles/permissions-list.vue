<template>
    <div>
        <div class="panel px-0 pb-1.5 border-[#e0e6ed] dark:border-[#1b2e4b]">
            <!-- Header con búsqueda y botón de agregar -->
            <div class="mb-4.5 px-5 flex md:items-center md:flex-row flex-col gap-5">
                <div>
                    <h2 class="text-xl font-semibold">Gestión de Permisos</h2>
                </div>
                <div class="ltr:ml-auto rtl:mr-auto flex items-center gap-3">
                    <input v-model="search" type="text" class="form-input" placeholder="Buscar permisos..." />
                    <button 
                        v-if="authStore.can('create permisos')"
                        type="button" 
                        class="btn btn-primary" 
                        @click="editPermission()"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        Agregar Permiso
                    </button>
                </div>
            </div>

            <!-- Tabla de permisos -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nombre
                            </th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Grupo
                            </th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Descripción
                            </th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="permission in permissions" :key="permission.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-success flex items-center justify-center text-white font-bold mr-3">
                                        {{ permission.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ permission.name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Creado: {{ formatDate(permission.created_at) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="permission.group" class="badge bg-outline-info">{{ permission.group }}</span>
                                <span v-else class="text-gray-400 text-sm">Sin grupo</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ permission.description || 'Sin descripción' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <button 
                                        v-if="authStore.can('edit permisos')"
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        @click="editPermission(permission)"
                                        :disabled="loadingStates[`edit-${permission.id}`]"
                                    >
                                        <span v-if="loadingStates[`edit-${permission.id}`]" class="animate-spin border-2 border-primary border-t-transparent rounded-full w-4 h-4 ltr:mr-1 rtl:ml-1 inline-block"></span>
                                        Editar
                                    </button>
                                    <button 
                                        v-if="authStore.can('delete permisos') && (!permission.roles_count || permission.roles_count === 0)"
                                        type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="deletePermission(permission)"
                                        :disabled="loadingStates[`delete-${permission.id}`]"
                                    >
                                        <span v-if="loadingStates[`delete-${permission.id}`]" class="animate-spin border-2 border-danger border-t-transparent rounded-full w-4 h-4 ltr:mr-1 rtl:ml-1 inline-block"></span>
                                        Eliminar
                                    </button>
                                    <span 
                                        v-if="permission.roles_count > 0"
                                        class="text-xs text-gray-400 py-2"
                                    >
                                        En uso
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación y Selector -->
            <div class="mt-6 flex flex-col md:flex-row justify-between items-center px-5 pb-5" v-if="pagination.last_page > 1 || pagination.total > 0">
                <div class="flex items-center gap-2 mb-4 md:mb-0">
                    <span class="text-sm">{{ $t('pagination.show') }}:</span>
                    <select class="form-select w-20 h-9" :value="pagination.per_page" @change="handlePerPageChange(parseInt(($event.target as HTMLSelectElement).value))">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <ul class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                    <li>
                        <button
                            type="button"
                            class="flex justify-center font-semibold px-3.5 py-2 rounded transition text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light"
                            :disabled="pagination.current_page === 1"
                            @click="changePage(pagination.current_page - 1)"
                        >
                            {{ $t('pagination.prev') }}
                        </button>
                    </li>
                    <li v-for="(page, index) in displayedPages" :key="index">
                        <button
                            v-if="page !== '...'"
                            type="button"
                            class="flex justify-center font-semibold px-3.5 py-2 rounded transition"
                            :class="{'text-primary border-2 border-primary dark:border-primary dark:text-white-light': pagination.current_page === page, 'text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light': pagination.current_page !== page}"
                            @click="changePage(page as number)"
                        >
                            {{ page }}
                        </button>
                        <span v-else class="px-3.5 py-2">...</span>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="flex justify-center font-semibold px-3.5 py-2 rounded transition text-dark hover:text-primary border-2 border-[#e0e6ed] dark:border-[#191e3a] hover:border-primary dark:hover:border-primary dark:text-white-light"
                            :disabled="pagination.current_page === pagination.last_page"
                            @click="changePage(pagination.current_page + 1)"
                        >
                            {{ $t('pagination.next') }}
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-10">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && permissions.length === 0" class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay permisos</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando algunos permisos para gestionar el acceso.</p>
            </div>
        </div>

        <!-- Modal para agregar/editar permiso -->
        <TransitionRoot appear :show="showPermissionModal" as="template">
            <Dialog as="div" @close="closePermissionModal" class="relative z-[51]">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <DialogOverlay class="fixed inset-0 bg-[black]/60" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center px-4 py-8">
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-lg text-black dark:text-white-dark">
                                <button
                                    type="button"
                                    class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none"
                                    @click="closePermissionModal"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24px"
                                        height="24px"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="w-6 h-6"
                                    >
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                                <div class="text-lg font-medium bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                    {{ permissionForm.id ? 'Editar Permiso' : 'Crear Permiso' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="savePermission">
                                        <div class="mb-5">
                                            <label for="name">Nombre *</label>
                                            <input 
                                                id="name" 
                                                type="text" 
                                                placeholder="Ingrese nombre (ej: view users)" 
                                                class="form-input" 
                                                v-model="permissionForm.name" 
                                                required 
                                            />
                                            <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="group">Grupo</label>
                                            <input 
                                                id="group" 
                                                type="text" 
                                                placeholder="Ingrese grupo (ej: users)" 
                                                class="form-input" 
                                                v-model="permissionForm.group" 
                                            />
                                            <div v-if="errors.group" class="text-danger mt-1">{{ errors.group[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="description">Descripción</label>
                                            <textarea 
                                                id="description" 
                                                placeholder="Ingrese descripción del permiso" 
                                                class="form-input" 
                                                v-model="permissionForm.description" 
                                                rows="3"
                                            ></textarea>
                                            <div v-if="errors.description" class="text-danger mt-1">{{ errors.description[0] }}</div>
                                        </div>
                                        <div class="flex justify-end items-center mt-8">
                                            <button type="button" class="btn btn-outline-danger" @click="closePermissionModal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="saving">
                                                <span v-if="saving" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                                {{ permissionForm.id ? 'Actualizar' : 'Crear' }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script lang="ts" setup>
    import { ref, onMounted, computed, watch } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    import Swal from 'sweetalert2';

    const authStore = useAuthStore();
    const search = ref('');
    const loading = ref(false);
    const saving = ref(false);
    const showPermissionModal = ref(false);
    const errors = ref<Record<string, string[]>>({});
    const loadingStates = ref<Record<string, boolean>>({});

    // Datos
    const permissions = ref<any[]>([]);
    
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
    });

    // Formulario de permiso
    const permissionForm = ref({
        id: null as number | null,
        name: '',
        group: '',
        description: ''
    });

    // Cargar datos
    const fetchPermissions = async (page = 1) => {
        loading.value = true;
        try {
            const response = await axios.get('/api/admin/permissions', {
                params: {
                    page: page,
                    per_page: pagination.value.per_page,
                    search: search.value
                }
            });
            permissions.value = response.data.data;
            pagination.value = response.data.meta;
        } catch (error) {
            console.error('Error fetching permissions:', error);
            Swal.fire('Error', 'No se pudieron cargar los permisos', 'error');
        } finally {
            loading.value = false;
        }
    };

    let searchTimer: any;
    watch(search, () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            fetchPermissions(1);
        }, 300);
    });

    const changePage = (page: number) => {
        if (page > 0 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
            fetchPermissions(page);
        }
    };

    const handlePerPageChange = (perPage: number) => {
        pagination.value.per_page = perPage;
        fetchPermissions(1);
    };

    const displayedPages = computed(() => {
        const currentPage = pagination.value.current_page;
        const lastPage = pagination.value.last_page;
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

    // Formatear fecha
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString();
    };

    // Abrir modal para editar/crear permiso
    const editPermission = (permission: any = null) => {
        errors.value = {};
        if (permission) {
            permissionForm.value = {
                id: permission.id,
                name: permission.name,
                group: permission.group || '',
                description: permission.description || ''
            };
        } else {
            permissionForm.value = {
                id: null,
                name: '',
                group: '',
                description: ''
            };
        }
        showPermissionModal.value = true;
    };

    // Guardar permiso
    const savePermission = async () => {
        saving.value = true;
        errors.value = {};

        try {
            if (permissionForm.value.id) {
                // Actualizar permiso
                const response = await axios.put(`/api/admin/permissions/${permissionForm.value.id}`, permissionForm.value);
                const index = permissions.value.findIndex(p => p.id === permissionForm.value.id);
                if (index !== -1) {
                    permissions.value[index] = response.data.data;
                }
                Swal.fire('Éxito', 'Permiso actualizado correctamente', 'success');
            } else {
                // Crear permiso
                await axios.post('/api/admin/permissions', permissionForm.value);
                Swal.fire('Éxito', 'Permiso creado correctamente', 'success');
            }
            showPermissionModal.value = false;
            fetchPermissions(permissionForm.value.id ? pagination.value.current_page : 1);
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            } else {
                console.error('Error saving permission:', error);
                Swal.fire('Error', 'No se pudo guardar el permiso', 'error');
            }
        } finally {
            saving.value = false;
        }
    };

    // Eliminar permiso
    const deletePermission = async (permission: any) => {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esta acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            loadingStates.value[`delete-${permission.id}`] = true;
            
            try {
                await axios.delete(`/api/admin/permissions/${permission.id}`);
                Swal.fire('Eliminado', 'El permiso ha sido eliminado', 'success');
                fetchPermissions(pagination.value.current_page);
            } catch (error: any) {
                console.error('Error deleting permission:', error);
                let errorMessage = 'No se pudo eliminar el permiso';
                if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                }
                Swal.fire('Error', errorMessage, 'error');
            } finally {
                loadingStates.value[`delete-${permission.id}`] = false;
            }
        }
    };

    // Cerrar modal
    const closePermissionModal = () => {
        showPermissionModal.value = false;
        permissionForm.value = {
            id: null,
            name: '',
            group: '',
            description: ''
        };
        errors.value = {};
    };

    onMounted(() => {
        fetchPermissions();
    });
</script>
