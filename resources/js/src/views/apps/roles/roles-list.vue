<template>
    <div>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl">Roles</h2>
            <div class="flex sm:flex-row flex-col sm:items-center sm:gap-3 gap-4 w-full sm:w-auto">
                <div class="flex gap-3">
                    <div>
                        <button 
                            v-if="authStore.can('create roles')"
                            type="button" 
                            class="btn btn-primary" 
                            @click="editRole()"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
                                <circle cx="10" cy="6" r="4" stroke="currentColor" stroke-width="1.5" />
                                <path
                                    opacity="0.5"
                                    d="M18 17.5C18 19.9853 18 22 10 22C2 22 2 19.9853 2 17.5C2 15.0147 5.58172 13 10 13C14.4183 13 18 15.0147 18 17.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path d="M21 10H19M19 10H17M19 10L19 8M19 10L19 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            Agregar Rol
                        </button>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-outline-primary p-2"
                            :class="{ 'bg-primary text-white': displayType === 'list' }"
                            @click="displayType = 'list'"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path d="M2 5.5L3.21429 7L7.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    opacity="0.5"
                                    d="M2 12.5L3.21429 14L7.5 10"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path d="M2 19.5L3.21429 21L7.5 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M22 19L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path opacity="0.5" d="M22 12L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M22 5L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <button
                            type="button"
                            class="btn btn-outline-primary p-2"
                            :class="{ 'bg-primary text-white': displayType === 'grid' }"
                            @click="displayType = 'grid'"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path
                                    opacity="0.5"
                                    d="M2.5 6.5C2.5 4.61438 2.5 3.67157 3.08579 3.08579C3.67157 2.5 4.61438 2.5 6.5 2.5C8.38562 2.5 9.32843 2.5 9.91421 3.08579C10.5 3.67157 10.5 4.61438 10.5 6.5C10.5 8.38562 10.5 9.32843 9.91421 9.91421C9.32843 10.5 8.38562 10.5 6.5 10.5C4.61438 10.5 3.67157 10.5 3.08579 9.91421C2.5 9.32843 2.5 8.38562 2.5 6.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    opacity="0.5"
                                    d="M13.5 17.5C13.5 15.6144 13.5 14.6716 14.0858 14.0858C14.6716 13.5 15.6144 13.5 17.5 13.5C19.3856 13.5 20.3284 13.5 20.9142 14.0858C21.5 14.6716 21.5 15.6144 21.5 17.5C21.5 19.3856 21.5 20.3284 20.9142 20.9142C20.3284 21.5 19.3856 21.5 17.5 21.5C15.6144 21.5 14.6716 21.5 14.0858 20.9142C13.5 20.3284 13.5 19.3856 13.5 17.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    d="M2.5 17.5C2.5 15.6144 2.5 14.6716 3.08579 14.0858C3.67157 13.5 4.61438 13.5 6.5 13.5C8.38562 13.5 9.32843 13.5 9.91421 14.0858C10.5 14.6716 10.5 15.6144 10.5 17.5C10.5 19.3856 10.5 20.3284 9.91421 20.9142C9.32843 21.5 8.38562 21.5 6.5 21.5C4.61438 21.5 3.67157 21.5 3.08579 20.9142C2.5 20.3284 2.5 19.3856 2.5 17.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path
                                    d="M13.5 6.5C13.5 4.61438 13.5 3.67157 14.0858 3.08579C14.6716 2.5 15.6144 2.5 17.5 2.5C19.3856 2.5 20.3284 2.5 20.9142 3.08579C21.5 3.67157 21.5 4.61438 21.5 6.5C21.5 8.38562 21.5 9.32843 20.9142 9.91421C20.3284 10.5 19.3856 10.5 17.5 10.5C15.6144 10.5 14.6716 10.5 14.0858 9.91421C13.5 9.32843 13.5 8.38562 13.5 6.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="relative">
                    <input
                        type="text"
                        placeholder="Buscar roles"
                        class="form-input py-2 ltr:pr-11 rtl:pl-11 peer"
                        v-model="searchRole"
                        @keyup="searchRoles"
                    />
                    <div class="absolute ltr:right-[11px] rtl:left-[11px] top-1/2 -translate-y-1/2 peer-focus:text-primary">
                        <svg class="mx-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5" opacity="0.5"></circle>
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <div v-if="errorMessage" class="mt-4 alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line me-2"></i>
            {{ errorMessage }}
            <button type="button" class="btn-close" @click="errorMessage = ''"></button>
        </div>

        <div v-if="successMessage" class="mt-4 alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ successMessage }}
            <button type="button" class="btn-close" @click="successMessage = ''"></button>
        </div>

        <div class="mt-5 panel p-0 border-0 overflow-hidden">
            <template v-if="displayType === 'list'">
                <div class="table-responsive">
                    <table class="table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="!text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="loading">
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <template v-for="role in filteredRolesList" :key="role.id">
                                    <tr>
                                        <td>
                                            <div class="flex items-center w-max">
                                                <div
                                                    class="grid place-content-center h-8 w-8 ltr:mr-2 rtl:ml-2 rounded-full bg-primary text-white text-sm font-semibold"
                                                >
                                                    {{ role.name ? role.name.charAt(0) : 'R' }}
                                                </div>
                                                <div class="font-semibold">{{ role.name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ role.description || 'Sin descripción' }}</td>
                                        <td>
                                            <div class="flex gap-4 items-center justify-center">
                                                <button 
                                                    v-if="authStore.can('edit roles') && !role.is_system"
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    @click="editRole(role)"
                                                >
                                                    Editar
                                                </button>

                                                <button 
                                                    v-if="authStore.can('delete roles') && !role.is_system && !role.is_default && role.users_count === 0"
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    @click="deleteRole(role)"
                                                >
                                                    Eliminar
                                                </button>

                                                <span 
                                                    v-if="role.is_system"
                                                    class="text-xs text-gray-400"
                                                >
                                                    Sistema
                                                </span>
                                                <span 
                                                    v-if="role.is_default"
                                                    class="text-xs text-gray-400"
                                                >
                                                    Por defecto
                                                </span>
                                                <span 
                                                    v-if="role.users_count > 0"
                                                    class="text-xs text-gray-400"
                                                >
                                                    Con usuarios
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>
        <template v-if="displayType === 'grid'">
            <div class="grid 2xl:grid-cols-4 xl:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 w-full">
                <template v-if="loading">
                    <div v-for="i in 4" :key="'skeleton-'+i" class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative h-64">
                        <div class="animate-pulse h-full flex flex-col">
                            <div class="bg-gray-300 h-32 rounded-t-md"></div>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="h-4 bg-gray-300 rounded"></div>
                                    <div class="h-3 bg-gray-300 rounded"></div>
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <div class="h-8 bg-gray-300 rounded flex-1"></div>
                                    <div class="h-8 bg-gray-300 rounded flex-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <template v-for="role in filteredRolesList" :key="role.id">
                        <div class="bg-white dark:bg-[#1c232f] rounded-md overflow-hidden text-center shadow relative">
                            <div class="bg-white/40 rounded-t-md bg-[url('/assets/images/notification-bg.png')] bg-center bg-cover p-6 pb-0">
                                <div class="grid place-content-center h-20 w-20 mx-auto rounded-full bg-primary text-white text-2xl font-semibold">
                                    {{ role.name ? role.name.charAt(0) : 'R' }}
                                </div>
                            </div>
                            <div class="px-6 pb-24 -mt-10 relative">
                                <div class="shadow-md bg-white dark:bg-gray-900 rounded-md px-2 py-4">
                                    <div class="text-xl font-semibold">{{ role.name }}</div>
                                    <div class="text-white-dark">{{ role.description || 'Sin descripción' }}</div>
                                    <div class="mt-2">
                                        <span class="badge" :class="{
                                            'bg-outline-warning': role.is_system,
                                            'bg-outline-success': !role.is_system && role.is_default,
                                            'bg-outline-secondary': !role.is_system && !role.is_default
                                        }">
                                            {{ role.is_system ? 'Sistema' : role.is_default ? 'Por defecto' : 'Personalizado' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-6 grid grid-cols-1 gap-4 ltr:text-left rtl:text-right">
                                    <div class="flex items-center">
                                        <div class="flex-none ltr:mr-2 rtl:ml-2">Usuarios:</div>
                                        <div class="badge bg-outline-info">{{ role.users_count }} usuarios</div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="flex-none ltr:mr-2 rtl:ml-2">Permisos:</div>
                                        <div class="text-white-dark">{{ role.permissions?.length || 0 }} permisos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-4 absolute bottom-0 w-full ltr:left-0 rtl:right-0 p-6">
                                <button 
                                    v-if="authStore.can('edit roles') && !role.is_system"
                                    type="button" 
                                    class="btn btn-outline-primary w-1/2" 
                                    @click="editRole(role)"
                                >
                                    Editar
                                </button>
                                <button 
                                    v-if="authStore.can('delete roles') && !role.is_system && !role.is_default && role.users_count === 0"
                                    type="button" 
                                    class="btn btn-outline-danger w-1/2" 
                                    @click="deleteRole(role)"
                                >
                                    Eliminar
                                </button>
                                <div 
                                    v-if="role.is_system || role.is_default || role.users_count > 0"
                                    class="w-full text-center text-xs text-gray-400"
                                >
                                    {{
                                        role.is_system ? 'Rol de sistema' :
                                        role.is_default ? 'Rol por defecto' :
                                        'Tiene usuarios'
                                    }}
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </div>
        </template>

        <!-- add role modal -->
        <TransitionRoot appear :show="addRoleModal" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-[51]">
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
                                    @click="closeModal"
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
                                    {{ params.id ? 'Editar Rol' : 'Crear Rol' }}
                                </div>
                                <div class="p-5">
                                    <form @submit.prevent="saveRole">
                                        <div class="mb-5">
                                            <label for="name">Nombre *</label>
                                            <input id="name" type="text" placeholder="Ingrese nombre" class="form-input" v-model="params.name" required />
                                            <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="description">Descripción</label>
                                            <textarea id="description" placeholder="Ingrese descripción" class="form-input" v-model="params.description" rows="3"></textarea>
                                            <div v-if="errors.description" class="text-danger mt-1">{{ errors.description[0] }}</div>
                                        </div>
                                        <div class="mb-5">
                                            <label for="permissions">Permisos</label>
                                            <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto p-2 border rounded">
                                                <label v-for="permission in availablePermissions" :key="permission.id" class="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        :value="permission.id"
                                                        v-model="params.permissions"
                                                        class="form-checkbox"
                                                    />
                                                    <span class="ml-2 text-sm">{{ permission.name }}</span>
                                                    <span v-if="permission.group" class="ml-2 text-xs text-gray-400">({{ permission.group }})</span>
                                                </label>
                                            </div>
                                            <div v-if="errors.permissions" class="text-danger mt-1">{{ errors.permissions[0] }}</div>
                                        </div>
                                        <div class="flex justify-end items-center mt-8">
                                            <button type="button" class="btn btn-outline-danger" @click="closeModal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" :disabled="saving">
                                                <span v-if="saving" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                                {{ params.id ? 'Actualizar' : 'Crear' }}
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
    import { ref, onMounted, computed } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import Swal from 'sweetalert2';
    import { useMeta } from '@/composables/use-meta';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    
    useMeta({ title: 'Administración de Roles' });

    const authStore = useAuthStore();

    const displayType = ref('list');
    const addRoleModal = ref(false);
    const loading = ref(false);
    const saving = ref(false);
    const errorMessage = ref('');
    const successMessage = ref('');
    const errors = ref({});
    const searchRole = ref('');
    const availablePermissions = ref([]);
    
    const defaultParams = ref({
        id: null,
        name: '',
        description: '',
        permissions: [] as number[],
    });
    
    const params = ref({...defaultParams.value});
    const rolesList = ref([]);

    // Computed property for filtered roles
    const filteredRolesList = computed(() => {
        if (!searchRole.value) {
            return rolesList.value;
        }
        return rolesList.value.filter((role) => 
            role.name.toLowerCase().includes(searchRole.value.toLowerCase()) ||
            (role.description && role.description.toLowerCase().includes(searchRole.value.toLowerCase()))
        );
    });

    onMounted(() => {
        fetchRoles();
        fetchPermissions();
    });

    const fetchRoles = async () => {
        loading.value = true;
        errorMessage.value = '';
        try {
            const response = await axios.get('/api/admin/roles');
            rolesList.value = response.data.data;
        } catch (error) {
            console.error('Error fetching roles:', error);
            errorMessage.value = 'Error al cargar los roles. Por favor, intente nuevamente.';
            showMessage('Error al cargar los roles.', 'error');
        } finally {
            loading.value = false;
        }
    };

    const fetchPermissions = async () => {
        try {
            const response = await axios.get('/api/admin/permissions');
            availablePermissions.value = response.data.data;
        } catch (error) {
            console.error('Error fetching permissions:', error);
        }
    };

    const searchRoles = () => {
        // Search is handled by the computed property
    };

    const editRole = (role: any = null) => {
        errors.value = {};
        if (role) {
            params.value = {
                id: role.id,
                name: role.name,
                description: role.description || '',
                permissions: role.permissions ? role.permissions.map((p: any) => p.id) : []
            };
        } else {
            params.value = {...defaultParams.value};
        }
        addRoleModal.value = true;
    };

    const saveRole = async () => {
        saving.value = true;
        errors.value = {};
        errorMessage.value = '';
        successMessage.value = '';

        try {
            if (params.value.id) {
                // Update role
                const { data } = await axios.put(`/api/admin/roles/${params.value.id}`, {
                    name: params.value.name,
                    description: params.value.description,
                    permissions: params.value.permissions
                });
                const index = rolesList.value.findIndex(role => role.id === params.value.id);
                if (index !== -1) {
                    rolesList.value.splice(index, 1, data.data);
                }
                showMessage('Rol actualizado exitosamente.');
            } else {
                // Create role
                const { data } = await axios.post('/api/admin/roles', {
                    name: params.value.name,
                    description: params.value.description,
                    permissions: params.value.permissions
                });
                rolesList.value.unshift(data.data);
                showMessage('Rol creado exitosamente.');
            }
            addRoleModal.value = false;
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = 'Por favor, corrija los errores de validación.';
            } else {
                console.error('Error saving role:', error);
                errorMessage.value = 'Error al guardar el rol. Por favor, intente nuevamente.';
                showMessage('Error al guardar el rol.', 'error');
            }
        } finally {
            saving.value = false;
        }
    };

    const deleteRole = async (role: any) => {
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
            try {
                await axios.delete(`/api/admin/roles/${role.id}`);
                rolesList.value = rolesList.value.filter(r => r.id !== role.id);
                showMessage('Rol eliminado exitosamente.');
            } catch (error: any) {
                console.error('Error deleting role:', error);
                let errorMessage = 'Error al eliminar el rol.';
                if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                }
                showMessage(errorMessage, 'error');
            }
        }
    };

    const closeModal = () => {
        addRoleModal.value = false;
        params.value = {...defaultParams.value};
        errors.value = {};
    };

    const showMessage = (msg = '', type = 'success') => {
        if (type === 'success') {
            successMessage.value = msg;
        } else {
            errorMessage.value = msg;
        }
        
        const toast: any = Swal.mixin({
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
</script>