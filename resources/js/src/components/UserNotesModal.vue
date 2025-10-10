<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-[52]">
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
                        <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-2xl text-black dark:text-white-dark">
                            <button
                                type="button"
                                class="absolute top-4 ltr:right-4 rtl:left-4 text-gray-400 hover:text-gray-800 dark:hover:text-gray-600 outline-none"
                                @click="closeModal"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            <div class="text-lg font-medium bg-[#fbfbfb] dark:bg-[#121c2c] ltr:pl-5 rtl:pr-5 py-3 ltr:pr-[50px] rtl:pl-[50px]">
                                {{ $t('user_notes.modal_title', { name: user?.name }) }}
                            </div>
                            <div class="p-5">
                                <!-- Formulario para agregar nota -->
                                <form @submit.prevent="addNote" class="mb-6">
                                    <div class="flex gap-3">
                                        <div class="flex-1">
                                            <textarea 
                                                v-model="newNote" 
                                                :placeholder="$t('user_notes.placeholder')" 
                                                class="form-textarea min-h-[80px]"
                                                maxlength="1000"
                                                :disabled="loading"
                                            ></textarea>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ newNote.length }}/1000 {{ $t('user_notes.characters') }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <button 
                                                type="submit" 
                                                class="btn btn-primary"
                                                :disabled="!newNote.trim() || loading"
                                            >
                                                <span v-if="loading" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 inline-block"></span>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                            <label class="flex items-center cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    v-model="isImportant" 
                                                    class="form-checkbox" 
                                                    :disabled="loading"
                                                />
                                                <span class="text-xs ml-2">{{ $t('user_notes.important') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </form>

                                <!-- Lista de notas -->
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    <div 
                                        v-for="note in notes" 
                                        :key="note.id"
                                        class="p-4 border rounded-lg transition-colors duration-200"
                                        :class="{
                                            'border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20': note.is_important,
                                            'border-gray-200 dark:border-gray-700': !note.is_important
                                        }"
                                    >
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $t('user_notes.by_author', { 
                                                    author: note.author?.name, 
                                                    date: formatDate(note.created_at) 
                                                }) }}
                                            </div>
                                            <button 
                                                v-if="authStore.can('edit users')"
                                                @click="deleteNote(note.id)"
                                                class="text-red-500 hover:text-red-700 transition-colors"
                                                :disabled="loading"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ note.note }}</p>
                                    </div>
                                    
                                    <div v-if="notes.length === 0 && !loading" class="text-center text-gray-500 py-8">
                                        {{ $t('user_notes.no_notes') }}
                                    </div>

                                    <div v-if="loading" class="text-center py-8">
                                        <div class="animate-spin border-2 border-primary border-t-transparent rounded-full w-6 h-6 mx-auto"></div>
                                        <p class="text-gray-500 mt-2">{{ $t('user_notes.loading') }}</p>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script lang="ts" setup>
    import { ref, watch, computed } from 'vue';
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
    import Swal from 'sweetalert2';
    import { useAuthStore } from '@/stores/auth';
    import axios from 'axios';

    interface User {
        id: number;
        name: string;
        email: string;
    }

    interface Note {
        id: number;
        note: string;
        is_important: boolean;
        created_at: string;
        author: {
            id: number;
            name: string;
        };
    }

    interface Props {
        isOpen: boolean;
        user: User | null;
    }

    interface Emits {
        (e: 'update:isOpen', value: boolean): void;
        (e: 'note-added'): void;
        (e: 'note-deleted'): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const authStore = useAuthStore();
    const notes = ref<Note[]>([]);
    const newNote = ref('');
    const isImportant = ref(false);
    const loading = ref(false);
    const notesLoading = ref(false);

    // Computed para controlar la visibilidad del modal
    const modalOpen = computed({
        get: () => props.isOpen,
        set: (value) => emit('update:isOpen', value)
    });

    // Watcher para cuando se abre el modal
    watch(() => props.isOpen, async (newVal) => {
        if (newVal && props.user) {
            await fetchNotes();
        } else {
            resetForm();
        }
    });

    // Métodos
    const closeModal = () => {
        modalOpen.value = false;
    };

    const resetForm = () => {
        newNote.value = '';
        isImportant.value = false;
        notes.value = [];
    };

    const fetchNotes = async () => {
        if (!props.user) return;

        notesLoading.value = true;
        try {
            const response = await axios.get(`/api/user-notes/user/${props.user.id}`);
            notes.value = response.data.data;
        } catch (error) {
            console.error('Error fetching notes:', error);
            showMessage('Error al cargar las notas', 'error');
        } finally {
            notesLoading.value = false;
        }
    };

    const addNote = async () => {
        if (!newNote.value.trim() || !props.user) return;

        loading.value = true;
        try {
            const response = await axios.post(`/api/user-notes/user/${props.user.id}`, {
                note: newNote.value,
                is_important: isImportant.value
            });

            if (response.data.success) {
                notes.value.unshift(response.data.data);
                newNote.value = '';
                isImportant.value = false;
                showMessage('Nota agregada correctamente');
                emit('note-added');
            }
        } catch (error) {
            console.error('Error adding note:', error);
            showMessage('Error al agregar la nota', 'error');
        } finally {
            loading.value = false;
        }
    };

    const deleteNote = async (noteId: number) => {
        if (!props.user) return;

        const result = await Swal.fire({
            title: '¿Eliminar nota?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            loading.value = true;
            try {
                const response = await axios.delete(`/api/user-notes/user/${props.user.id}/${noteId}`);
                
                if (response.data.success) {
                    notes.value = notes.value.filter(note => note.id !== noteId);
                    showMessage('Nota eliminada correctamente');
                    emit('note-deleted');
                }
            } catch (error) {
                console.error('Error deleting note:', error);
                showMessage('Error al eliminar la nota', 'error');
            } finally {
                loading.value = false;
            }
        }
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const showMessage = (msg: string, type: 'success' | 'error' = 'success') => {
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
</script>