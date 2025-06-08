<template>
    <el-dialog title="Añadir Almacén" width="500px" :before-close="closeModal">
        <div class="w-full flex flex-col gap-4">
            <el-form :model="storageForm" label-position="top">
                <el-form-item label="Nombre">
                    <el-input v-model="storageForm.name" placeholder="Nombre del almacén" />
                </el-form-item>
                <el-form-item label="Descripción">
                    <el-input type="textarea" v-model="storageForm.description" placeholder="Descripción del almacén" />
                </el-form-item>
                <el-form-item>
                    <el-checkbox v-model="storageForm.vehicle" label="Almacén Vehicular" />
                </el-form-item>
            </el-form>
        </div>
        <template #footer>
            <div class="flex justify-end gap-2">
                <el-button @click="closeModal">Cancelar</el-button>
                <el-button type="primary" :loading="saving" @click="handleSubmit">Guardar</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { handleRequestError, handleRequestSuccess } from '@/utils/fetchNotificationsHandlers';
import { ElMessageBox } from 'element-plus';

const emit = defineEmits(['closeModal', 'storage-added']);

const saving = ref(false);
const storageForm = ref({
    name: '',
    description: '',
    vehicle: false
});

const closeModal = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar este modal?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {

        emit('closeModal', false);
        storageForm.value = {
            name: '',
            description: '',
            vehicle: false
        };
    })
};

const handleSubmit = () => {
    saving.value = true;

    let data = storageForm.value;
    data.vehicle = data.vehicle ? 1 : 0;

    axios.post(import.meta.env.VITE_API_URL + '/storages/create', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        handleRequestSuccess('Almacén creado exitosamente');
        emit('storage-added');
        closeModal();
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        saving.value = false;
    });
};
</script>
