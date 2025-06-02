<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';

const props = defineProps([
    'selectedCategory',
    'isSelectedCategory',
]);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);

const handleEditCategory = () => {
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
                message: 'La funcionalidad de eliminar categorías aún no está disponible.',
                duration: 3000,
                zIndex: 10000,
                offset: 80
            });

        }, 1000);
    })
};

const handleDeleteCategory = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar esta categoría?', 'Confirmación', {
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
                message: 'La funcionalidad de eliminar categorías aún no está disponible.',
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
    <el-dialog title="Detalles de Categoría" width="80%" @close="() => { $emit('closeModal') }"
        :fullscreen="fullScreenModals" class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="selectedCategory" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="Id">
                        <el-input v-model="selectedCategory.id" disabled />
                    </el-form-item>
                    <el-form-item label="Nombre">
                        <el-input v-model="selectedCategory.name" />
                    </el-form-item>
                    <el-form-item label="Descripción">
                        <el-input v-model="selectedCategory.description" type="textarea" />
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
                <el-button class="!ml-0" type="danger" :disabled="fetchingModal" @click="handleDeleteCategory">
                    Eliminar Producto
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleEditCategory">
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