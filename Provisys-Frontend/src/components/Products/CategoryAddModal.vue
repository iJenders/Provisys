<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'addingCategory',
]);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);

const category = ref({
    id: null,
    name: '',
    description: ''
});

const handleSaveCategory = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar la categoría?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        axios.post(import.meta.env.VITE_API_URL + '/categories/create', {
            name: category.value.name,
            description: category.value.description
        }, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then(response => {
            ElNotification({
                title: 'Categoría guardada',
                message: 'La categoría se ha guardado correctamente',
                type: 'success',
                offsetset: 80,
                zIndex: 10000
            });
            emit('closeModal');
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            fetchingModal.value = false;
        });
    })
};

const closeModal = () => {
    // Confirmation
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar sin guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        emit('closeModal');
    })
};

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <el-dialog title="Añadir Categoría" width="80%" @close="() => { $emit('closeModal') }"
        :fullscreen="fullScreenModals" class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="addingCategory" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="Nombre">
                        <el-input v-model="category.name" placeholder="Nombre de la categoría" />
                    </el-form-item>
                    <el-form-item label="Descripción">
                        <el-input v-model="category.description" type="textarea"
                            placeholder="Descripción de la categoría" />
                    </el-form-item>
                </el-form>
            </div>
        </Transition>

        <!-- Modal Buttons -->
        <template #footer>
            <div class="dialog-footer flex justify-end flex-wrap gap-4 gap-y-2">
                <el-button class="!ml-0" type="info" :disabled="fetchingModal" @click="closeModal">
                    Cerrar
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleSaveCategory">
                    Añadir Categoría
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<style>
.modal-out-enter-active,
.modal-out-leave-active {
    transition: opacity 0.3s ease;
}

.modal-out-enter-from,
.modal-out-leave-to {
    opacity: 0;
}
</style>