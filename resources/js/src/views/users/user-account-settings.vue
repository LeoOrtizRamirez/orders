<template>
    <div>
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li class="ltr:before:mr-2 rtl:before:ml-2">
                <span>{{ $t('user_account_settings.nav.account_settings') }}</span>
            </li>
        </ul>
        <div class="pt-5">
            <div class="flex items-center justify-between mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('user_account_settings.title') }}</h5>
            </div>
            
            <!-- Loading state -->
            <div v-if="loading" class="text-center py-10">
                <div class="animate-spin border-2 border-primary border-l-transparent rounded-full w-8 h-8 mx-auto"></div>
                <p class="mt-2">Cargando datos del usuario...</p>
            </div>
            
            <TabGroup v-if="!loading">
                <TabList class="flex font-semibold border-b border-[#ebedf2] dark:border-[#191e3a] mb-5 whitespace-nowrap overflow-y-auto">
                    <Tab as="template" v-slot="{ selected }">
                        <a
                            href="javascript:;"
                            class="flex gap-2 p-4 border-b border-transparent hover:border-primary hover:text-primary !outline-none"
                            :class="{ '!border-primary text-primary': selected }"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <path
                                    opacity="0.5"
                                    d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                />
                                <path d="M12 15L12 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            {{ $t('user_account_settings.tabs.settings.title') }}
                        </a>
                    </Tab>
                    <Tab as="template" v-slot="{ selected }">
                        <a
                            href="javascript:;"
                            class="flex gap-2 p-4 border-b border-transparent hover:border-primary hover:text-primary !outline-none"
                            :class="{ '!border-primary text-primary': selected }"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                <circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5" />
                                <ellipse opacity="0.5" cx="12" cy="17" rx="7" ry="4" stroke="currentColor" stroke-width="1.5" />
                            </svg>
                            {{ $t('user_account_settings.tabs.preferences.title') }}
                        </a>
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel>
                        <div>
                            <form @submit.prevent="saveUser" class="border border-[#ebedf2] dark:border-[#191e3a] rounded-md p-4 mb-5 bg-white dark:bg-[#0e1726]">
                                <h6 class="text-lg font-bold mb-5">{{ $t('user_account_settings.tabs.settings.account.title') }}</h6>
                                <div class="flex flex-col sm:flex-row">
                                    <div class="ltr:sm:mr-4 rtl:sm:ml-4 w-full sm:w-2/12 mb-5">
                                        <img :src="userImage" alt="User avatar" class="w-20 h-20 md:w-32 md:h-32 rounded-full object-cover mx-auto" />
                                        <input
                                            type="file"
                                            ref="imageInput"
                                            name="image"
                                            accept="image/*"
                                            @change="handleImageUpload"
                                            class="hidden"
                                        />
                                        <div class="mt-3 flex flex-col gap-2">
                                            <button
                                                type="button"
                                                @click="$refs.imageInput.click()"
                                                class="btn btn-outline-primary btn-sm w-full"
                                            >
                                                {{ $t('user_account_settings.tabs.settings.account.change_image') }}
                                            </button>
                                            <button
                                                v-if="imageFile"
                                                type="button"
                                                @click="removeImage"
                                                class="btn btn-outline-danger btn-sm w-full"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                        <div v-if="errors.image" class="text-danger mt-1 text-center">{{ errors.image[0] }}</div>
                                    </div>
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label for="name">{{ $t('user_account_settings.tabs.settings.account.full_name') }}</label>
                                            <input id="name" v-model="userParams.name" type="text" :placeholder="$t('user_account_settings.tabs.settings.account.full_name')" class="form-input" />
                                            <div v-if="errors.name" class="text-danger mt-1">{{ errors.name[0] }}</div>
                                        </div>
                                        <div>
                                            <label for="phone">{{ $t('user_account_settings.tabs.settings.account.phone') }}</label>
                                            <input id="phone" v-model="userParams.phone" type="text" :placeholder="$t('user_account_settings.tabs.settings.account.phone')" class="form-input" />
                                            <div v-if="errors.phone" class="text-danger mt-1">{{ errors.phone[0] }}</div>
                                        </div>
                                        <div>
                                            <label for="email">{{ $t('user_account_settings.tabs.settings.account.email') }}</label>
                                            <input id="email" v-model="userParams.email" type="email" :placeholder="$t('user_account_settings.tabs.settings.account.email')" class="form-input" required/>
                                            <div v-if="errors.email" class="text-danger mt-1">{{ errors.email[0] }}</div>
                                        </div>
                                        <div>
                                            <label for="password">{{ $t('user_account_settings.tabs.settings.account.password') }}</label>
                                            <input id="password" v-model="userParams.password" type="password" :placeholder="$t('user_account_settings.tabs.settings.account.password')" class="form-input" />
                                            <div v-if="errors.password" class="text-danger mt-1">{{ errors.password[0] }}</div>
                                        </div>
                                        <div class="sm:col-span-2 mt-3">
                                            <button type="submit" :disabled="saving" class="btn btn-primary">
                                                <span v-if="saving" class="animate-spin border-2 border-white border-l-transparent rounded-full w-4 h-4 ltr:mr-2 rtl:ml-2 inline-block"></span>
                                                {{ $t('user_account_settings.tabs.settings.account.btn_save') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <div class="switch">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                                <div class="panel space-y-5">
                                    <h5 class="font-semibold text-lg mb-4">{{ $t('user_account_settings.tabs.preferences.choose_theme') }}</h5>
                                    <div class="flex justify-around">
                                        <label class="inline-flex cursor-pointer">
                                            <input class="form-radio ltr:mr-4 rtl:ml-4 cursor-pointer" type="radio" name="flexRadioDefault" checked />
                                            <span>
                                                <img class="ms-3" width="100" height="68" alt="settings-dark" src="/assets/images/settings-light.svg" />
                                            </span>
                                        </label>

                                        <label class="inline-flex cursor-pointer">
                                            <input class="form-radio ltr:mr-4 rtrl:ml-4 cursor-pointer" type="radio" name="flexRadioDefault" />
                                            <span>
                                                <img class="ms-3" width="100" height="68" alt="settings-light" src="/assets/images/settings-dark.svg" />
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div class="panel space-y-5">
                                    <h5 class="font-semibold text-lg mb-4">{{ $t('user_account_settings.tabs.preferences.hide_left_navigation') }}</h5>
                                    {{ $t('user_account_settings.tabs.preferences.sidebar_will_be_hidden_by_default') }}
                                    <label class="w-12 h-6 relative">
                                        <input
                                            type="checkbox"
                                            class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer"
                                            id="custom_switch_checkbox4"
                                        />
                                        <span
                                            for="custom_switch_checkbox4"
                                            class="bg-[#ebedf2] dark:bg-dark block h-full rounded-full before:absolute before:left-1 before:bg-white dark:before:bg-white-dark dark:peer-checked:before:bg-white before:bottom-1 before:w-4 before:h-4 before:rounded-full peer-checked:before:left-7 peer-checked:bg-primary before:transition-all before:duration-300"
                                        ></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </div>
</template>

<script lang="ts" setup>
    import { ref, reactive, onMounted, computed } from 'vue';
    import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
    import { useMeta } from '@/composables/use-meta';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import { useRoute } from 'vue-router';
    
    useMeta({ title: 'Account Setting' });

    const authStore = useAuthStore();
    const route = useRoute();
    const loading = ref(true);
    const saving = ref(false);
    const savingOnly = ref(false);
    const errorMessage = ref('');
    const errors = ref({});
    const userData = ref<any>(null);
    const imageInput = ref<HTMLInputElement | null>(null);
    const imageFile = ref<File | null>(null);

    // Parámetros para el usuario principal
    const userParams = reactive({
        name: '',
        email: '',
        phone: '',
        password: ''
    });

    // Parámetros para el usuario only
    const userOnlyParams = reactive({
        user_name: '',
        password: ''
    });

    // Computed para mostrar la imagen
    const userImage = computed(() => {
        if (imageFile.value) {
            return URL.createObjectURL(imageFile.value);
        }
        if (userData.value?.image) {
            if (userData.value.image.startsWith('http')) {
                return userData.value.image;
            }
            if (userData.value.image.startsWith('storage/')) {
                return `/${userData.value.image}`;
            }
            return `/storage/${userData.value.image}`;
        }
        return '/assets/images/profile-34.jpeg';
    });

    onMounted(async () => {
        await fetchUserData();
    });

    const fetchUserData = async () => {
        loading.value = true;
        errorMessage.value = '';
        
        try {
            const userId = route.params.id || authStore.user?.id;
            
            if (!userId) {
                throw new Error('No se pudo identificar el usuario');
            }

            const response = await axios.get(`/api/users/${userId}`);
            userData.value = response.data;
            
            // ✅ ACTUALIZAR AUTHSTORE SI ES EL USUARIO LOGUEADO
            if (authStore.user && authStore.user.id === parseInt(userId)) {
                authStore.setUser(response.data);
            }
            
            // Llenar los formularios con los datos obtenidos
            userParams.name = userData.value.name || '';
            userParams.email = userData.value.email || '';
            userParams.phone = userData.value.phone || '';
            userParams.password = userData.value.password || '';
            
            // Llenar datos del usuario only si existen
            if (userData.value.user_only) {
                userOnlyParams.user_name = userData.value.user_only.user_name || '';
                userOnlyParams.password = userData.value.user_only.user_password || '';
            }
            
        } catch (error: any) {
            console.error('Error al cargar datos del usuario:', error);
            errorMessage.value = 'No se pudieron cargar los datos del usuario.';
            showMessage('Error al cargar datos del usuario', 'error');
        } finally {
            loading.value = false;
        }
    };

    const handleImageUpload = (event: Event) => {
        const target = event.target as HTMLInputElement;
        if (target.files && target.files[0]) {
            const file = target.files[0];
            
            if (!file.type.startsWith('image/')) {
                showMessage('Por favor selecciona una imagen válida', 'error');
                target.value = '';
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                showMessage('La imagen no debe exceder 2MB', 'error');
                target.value = '';
                return;
            }
            
            imageFile.value = file;
        }
    };

    const saveUser = async () => {
        saving.value = true;
        errors.value = {};

        try {
            const userId = route.params.id || authStore.user?.id;
            const formData = new FormData();
            
            // Agregar campos al FormData
            formData.append('name', userParams.name);
            formData.append('email', userParams.email);
            formData.append('phone', userParams.phone);
            
            if (userParams.password) {
                formData.append('password', userParams.password);
            }
            
            // Agregar imagen si se seleccionó una nueva
            if (imageFile.value) {
                formData.append('image', imageFile.value);
            }

            const response = await axios.post(`/api/users/${userId}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            
            // Actualizar los datos locales
            userData.value = response.data;
            
            // ✅ ACTUALIZAR AUTHSTORE SI ES EL USUARIO LOGUEADO
            if (authStore.user && authStore.user.id === parseInt(userId)) {
                // Usar updateUser para actualizar los campos modificados
                authStore.updateUser({
                    name: response.data.name,
                    email: response.data.email,
                    phone: response.data.phone,
                    image: response.data.image
                    // añade otros campos que quieras mantener sincronizados
                });
            }
            
            // Limpiar archivo de imagen después de guardar
            imageFile.value = null;
            if (imageInput.value) {
                imageInput.value.value = '';
            }
            
            showMessage('Usuario actualizado satisfactoriamente!', 'success');
            
            // Recargar datos para asegurar que tenemos la información más actualizada
            await fetchUserData();
            
        } catch (error: any) {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
                errorMessage.value = 'Por favor corrija los errores de validación.';
            } else {
                console.error('Error al guardar usuario:', error);
                errorMessage.value = 'No se pudo guardar el usuario. Inténtalo de nuevo.';
                showMessage('No se pudo guardar el usuario.', 'error');
            }
        } finally {
            saving.value = false;
        }
    };

    const showMessage = (msg = '', type = 'success') => {
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

    // Función para eliminar imagen
    const removeImage = () => {
        imageFile.value = null;
        if (imageInput.value) {
            imageInput.value.value = '';
        }
    };
</script>

<style scoped>
.btn-outline-primary {
    border: 1px solid #3b82f6;
    color: #3b82f6;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background-color: #3b82f6;
    color: white;
}

.text-danger {
    color: #e7515a;
    font-size: 0.875rem;
}

.alert {
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background-color: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
</style>