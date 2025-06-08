<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'selectedProvider',
    'isSelectedProvider',
]);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);

const handleEditProvider = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let data = {
            id: props.selectedProvider.id,
            name: props.selectedProvider.name,
            phone: props.selectedProvider.phone,
            secondaryPhone: props.selectedProvider.secondaryPhone,
            email: props.selectedProvider.email,
            address: props.selectedProvider.address,
        }

        axios.post(import.meta.env.VITE_API_URL + '/manufacturers/update', data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then((response) => {
            ElNotification({
                title: 'Éxito',
                message: 'Fabricante actualizado exitosamente',
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

const handleDeleteProvider = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este fabricante?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let $data = {
            id: props.selectedProvider.id,
        }

        axios.post(import.meta.env.VITE_API_URL + '/manufacturers/delete', $data, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then((response) => {
            ElNotification.success({
                title: 'Éxito',
                message: 'Fabricante eliminado correctamente.',
                duration: 3000,
                zIndex: 10000,
                offset: 80
            });
        }).catch((error) => {
            handleRequestError(error);
        }).finally(() => {
            fetchingModal.value = false;
            emit('closeModal');
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
    <el-dialog title="Detalles del Fabricante" width="80%" :before-close="closeModal" :fullscreen="fullScreenModals"
        class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="selectedProvider" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="Cédula o RIF">
                        <el-input v-model="selectedProvider.id" disabled />
                    </el-form-item>
                    <el-form-item label="Nombre">
                        <el-input v-model="selectedProvider.name" />
                    </el-form-item>
                    <el-form-item label="Teléfono">
                        <el-input v-model="selectedProvider.phone" />
                    </el-form-item>
                    <el-form-item label="Teléfono Secundario">
                        <el-input v-model="selectedProvider.secondaryPhone" />
                    </el-form-item>
                    <el-form-item label="Correo Electrónico">
                        <el-input v-model="selectedProvider.email" />
                    </el-form-item>
                    <el-form-item label="Dirección">
                        <el-input v-model="selectedProvider.address" />
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
                <el-button class="!ml-0" :type="selectedProvider.deleted ? 'primary' : 'danger'"
                    :disabled="fetchingModal" @click="handleDeleteProvider">
                    {{ selectedProvider.deleted ? 'Restaurar' : 'Eliminar' }} Fabricante
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleEditProvider">
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