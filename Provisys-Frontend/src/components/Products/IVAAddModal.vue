<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'addingIVA',
]);

const emit = defineEmits([
    'closeModal',
]);

const fetchingModal = ref(false);

const iva = ref({
    id: null,
    name: '',
    value: ''
});

const handleSaveIVA = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar el IVA?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        axios.post(import.meta.env.VITE_API_URL + '/ivas/create', {
            name: iva.value.name,
            value: iva.value.value
        }, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        }).then(response => {
            ElNotification({
                title: 'IVA guardado',
                message: 'El IVA se ha guardado correctamente',
                type: 'success',
                offset: 80,
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
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar sin guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        iva.value = {
            id: null,
            name: '',
            value: ''
        }
        emit('closeModal');
    })
};

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <el-dialog title="Añadir IVA" width="80%" :before-close="closeModal" :fullscreen="fullScreenModals" class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div v-if="addingIVA" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="Nombre">
                        <el-input v-model="iva.name" placeholder="Nombre del IVA" />
                    </el-form-item>
                    <el-form-item label="Porcentaje">
                        <el-input v-model="iva.value" type="number" placeholder="Valor del IVA" />
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
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleSaveIVA">
                    Añadir IVA
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
