<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';

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

        // Simular petición
        setTimeout(() => {
            fetchingModal.value = false;

            ElNotification.warning({
                title: 'Advertencia',
                message: 'La funcionalidad de eliminar proveedores aún no está disponible.',
                duration: 3000,
                zIndex: 10000,
                offset: 80
            });

        }, 1000);
    })
};

const handleDeleteProvider = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este proveedo?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        // Simular petición
        setTimeout(() => {
            fetchingModal.value = false;

            ElNotification.warning({
                title: 'Advertencia',
                message: 'La funcionalidad de eliminar proveedores aún no está disponible.',
                duration: 3000,
                zIndex: 10000,
                offset: 80
            });

        }, 1000);
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
    <el-dialog title="Detalles del Proveedor" width="80%" @close="() => { $emit('closeModal') }"
        :fullscreen="fullScreenModals" class="!p-6">
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
                <el-button class="!ml-0" type="danger" :disabled="fetchingModal" @click="handleDeleteProvider">
                    Eliminar Proveedor
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