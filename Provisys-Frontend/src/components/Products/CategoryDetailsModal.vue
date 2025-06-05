<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref, watch } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'selectedCategory',
    'isSelectedCategory',
]);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);
const category = ref({
    id: null,
    name: '',
    description: ''
})

const handleEditCategory = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        axios.post(import.meta.env.VITE_API_URL + '/categories/edit', {
            id: category.value.id,
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
        }).catch(error => { handleRequestError(error) }).finally(() => {
            fetchingModal.value = false;
        })
    })
};

const handleDeleteCategory = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar esta categoría?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        axios.post(import.meta.env.VITE_API_URL + '/categories/delete', {
            id: category.value.id
        }, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then(response => {
            ElNotification({
                title: 'Categoría eliminada',
                message: 'La categoría se ha eliminado correctamente',
                type: 'success',
                offsetset: 80,
                zIndex: 10000
            });
            emit('closeModal');
        }).catch(error => { handleRequestError(error) }).finally(() => {
            fetchingModal.value = false;
        })
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

watch(props, () => {
    if (props.isSelectedCategory) {
        category.value = { ...props.selectedCategory };
    }
})

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <el-dialog title="Detalles de Categoría" width="80%" @close="() => { $emit('closeModal') }"
        :fullscreen="fullScreenModals" class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="category" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="Id">
                        <el-input v-model="category.id" disabled />
                    </el-form-item>
                    <el-form-item label="Nombre">
                        <el-input v-model="category.name" />
                    </el-form-item>
                    <el-form-item label="Descripción">
                        <el-input v-model="category.description" type="textarea" />
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