<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'selectedIVA',
    'isSelectedIVA',
]);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);

const handleEditIVA = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let data = {
            id: props.selectedIVA.id,
            name: props.selectedIVA.name,
            value: props.selectedIVA.value,
        }

        axios.post(import.meta.env.VITE_API_URL + '/ivas/update', data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then((response) => {
            ElNotification({
                title: 'Éxito',
                message: 'IVA actualizado exitosamente',
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

const handleDeleteIVA = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este IVA?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let $data = {
            id: props.selectedIVA.id,
        }

        axios.post(import.meta.env.VITE_API_URL + '/ivas/delete', $data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then((response) => {
            ElNotification.success({
                title: 'Éxito',
                message: 'IVA eliminado correctamente.',
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
        emit('closeModal');
    })
};

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <el-dialog title="Detalles del IVA" width="80%" :before-close="closeModal" :fullscreen="fullScreenModals"
        class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="selectedIVA" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="ID">
                        <el-input v-model="selectedIVA.id" disabled />
                    </el-form-item>
                    <el-form-item label="Nombre">
                        <el-input v-model="selectedIVA.name" />
                    </el-form-item>
                    <el-form-item label="Porcentaje">
                        <el-input v-model="selectedIVA.value" type="number" />
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
                <el-button class="!ml-0" :type="selectedIVA.deleted ? 'primary' : 'danger'" :disabled="fetchingModal"
                    @click="handleDeleteIVA">
                    {{ selectedIVA.deleted ? 'Restaurar' : 'Eliminar' }} IVA
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleEditIVA">
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
