<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { ref, watch } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'selectedStorage',
    'isSelectedStorage',
]);

const show = ref(false);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);

const handleEditStorage = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let data = {
            id: props.selectedStorage.id,
            name: props.selectedStorage.name,
            description: props.selectedStorage.description,
            vehicle: props.selectedStorage.vehicle ? 1 : 0,
        }

        axios.post(import.meta.env.VITE_API_URL + '/storages/update', data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': localStorage.getItem('token')
            }
        }).then((response) => {
            ElNotification({
                title: 'Éxito',
                message: 'Almacén actualizado exitosamente',
                type: 'success',
                offset: 80,
                zIndex: 10000,
            });
            emit('closeModal');
        }).catch((error) => {
            handleRequestError(error);
        }).finally(() => {
            fetchingModal.value = false;
        });
    })
};

const handleDeleteStorage = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este almacén?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let data = {
            id: props.selectedStorage.id,
        }

        axios.post(import.meta.env.VITE_API_URL + '/storages/delete', data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': localStorage.getItem('token')
            }
        }).then((response) => {
            ElNotification.success({
                title: 'Éxito',
                message: 'Almacén eliminado correctamente.',
                duration: 3000,
                zIndex: 10000,
                offset: 80
            });
            emit('closeModal');
        }).catch((error) => {
            handleRequestError(error);
        }).finally(() => {
            fetchingModal.value = false;
        });
    })
};

const closeModal = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar sin guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        show.value = false;
        emit('closeModal');
    })
};

watch(props, () => {
    show.value = props.isSelectedStorage;
})

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <el-dialog v-model="show" title="Detalles del Almacén" width="80%" :before-close="closeModal"
        :fullscreen="fullScreenModals" class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="selectedStorage" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="ID">
                        <el-input v-model="selectedStorage.id" disabled />
                    </el-form-item>
                    <el-form-item label="Nombre">
                        <el-input v-model="selectedStorage.name" />
                    </el-form-item>
                    <el-form-item label="Descripción">
                        <el-input v-model="selectedStorage.description" type="textarea" />
                    </el-form-item>
                    <el-form-item label="Vehicular">
                        <el-switch v-model="selectedStorage.vehicle" />
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
                <el-button class="!ml-0" :type="selectedStorage.deleted ? 'primary' : 'danger'"
                    :disabled="fetchingModal" @click="handleDeleteStorage">
                    {{ selectedStorage.deleted ? 'Restaurar' : 'Eliminar' }} Almacén
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleEditStorage">
                    Guardar Cambios
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
