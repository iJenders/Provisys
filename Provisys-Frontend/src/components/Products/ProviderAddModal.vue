<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import Line from '@/components/Line.vue';
import { ElMessageBox, ElNotification } from 'element-plus';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const props = defineProps([
    'addingProvider',
]);

const emit = defineEmits([
    'closeModal',
]);

const idTypes = ref(['V-', 'E-', 'J-', 'G-'])

const provider = ref({
    id: null,
    idType: idTypes.value[0],
    name: '',
    phone: '',
    secondaryPhone: '',
    email: '',
    address: ''
});

const fetchingModal = ref(false);

const handleSaveProvider = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        let data = {
            id: provider.value.idType + provider.value.id,
            name: provider.value.name,
            phone: provider.value.phone,
            secondaryPhone: provider.value.secondaryPhone,
            email: provider.value.email,
            address: provider.value.address
        }
        axios.post(import.meta.env.VITE_API_URL + '/manufacturers/create', data, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        }).then(response => {
            ElNotification({
                title: 'Éxito',
                message: 'El fabricante se ha guardado correctamente',
                type: 'success',
                offset: 80,
                zIndex: 10000
            });
            emit('closeModal');
        }).catch(error => {
            handleRequestError(error)
        }).finally(() => {
            fetchingModal.value = false;
        });
    });
}

const closeModal = () => {
    // Confirmation
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar sin guardar los cambios?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        provider.value = {
            id: null,
            idType: idTypes.value[0],
            name: '',
            phone: '',
            secondaryPhone: '',
            email: '',
            address: ''
        }
        emit('closeModal');
    })
};

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <el-dialog title="Nuevo Fabricante" width="80%" :before-close="closeModal" :fullscreen="fullScreenModals"
        class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <Transition name="modal-out">
            <div class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
                <el-form label-position="top" class="w-full">
                    <el-form-item label="Cédula o RIF">
                        <el-col :xs="6" :sm="4" :md="3">
                            <el-select v-model="provider.idType" placeholder="V-">
                                <el-option v-for="idType in idTypes" :key="idType" :label="idType" :value="idType">
                                    {{ idType }}
                                </el-option>
                            </el-select>
                        </el-col>
                        <el-col :xs="2" :sm="1">
                        </el-col>
                        <el-col :xs="16" :sm="19" :md="20">
                            <el-input v-model="provider.id" placeholder="301234567" />
                        </el-col>
                    </el-form-item>
                    <el-form-item label="Nombre">
                        <el-input v-model="provider.name" placeholder="Coca-Cola Femsa / etc." />
                    </el-form-item>
                    <el-form-item label="Teléfono">
                        <el-input v-model="provider.phone" placeholder="+58412345678" />
                    </el-form-item>
                    <el-form-item label="Teléfono Secundario">
                        <el-input v-model="provider.secondaryPhone" placeholder="+58412345678" />
                    </el-form-item>
                    <el-form-item label="Correo Electrónico">
                        <el-input v-model="provider.email" placeholder="pedro@correo.com" />
                    </el-form-item>
                    <el-form-item label="Dirección">
                        <el-input v-model="provider.address" placeholder="Calle 123, Barquisimeto" />
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
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleSaveProvider">
                    Añadir Fabricante
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