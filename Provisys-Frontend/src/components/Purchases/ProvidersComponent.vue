<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import { Building2, Plus, Edit, Trash2, Search, Undo2 } from 'lucide-vue-next';
import Line from '../Line.vue';
import axios from 'axios';
import { handleRequestError, handleRequestSuccess } from '@/utils/fetchNotificationsHandlers';
import { ElMessageBox } from 'element-plus';

const providers = ref([]);

const idTypes = ref([
    'V-',
    'E-',
    'G-',
    'J-',
]);

const showAddModal = ref(false);
const showEditModal = ref(false);
const newProvider = ref({
    id: '',
    idType: idTypes.value[0],
    name: '',
    phone: '',
    secondaryPhone: '',
    email: '',
    address: '',
    disabled: true
});
const editProvider = ref({
    id: '',
    idType: idTypes.value[0],
    name: '',
    phone: '',
    secondaryPhone: '',
    email: '',
    address: '',
    disabled: true
})

const gettingProviders = ref(false);
const addingProvider = ref(false);
const editingProvider = ref(false);

const showDisabled = ref(false);
const search = ref('');

const getProviders = () => {
    gettingProviders.value = true;

    let data = {
        filters: {
            deleted: 0
        },
        search: search.value
    }
    if (showDisabled.value) {
        delete data.filters.deleted;
    }

    axios.post(import.meta.env.VITE_API_URL + '/providers', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        providers.value = response.data.response.providers.map(provider => {
            return {
                id: provider.id,
                idType: provider.id.substring(2, provider.id.length - 1),
                name: provider.name,
                phone: provider.phone,
                secondaryPhone: provider.secondaryPhone,
                email: provider.email,
                address: provider.address,
                disabled: provider.deleted
            }
        });
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        gettingProviders.value = false;
    });
}

const addProvider = () => {
    addingProvider.value = true;

    let data = {
        id: newProvider.value.idType + newProvider.value.id,
        name: newProvider.value.name,
        phone: newProvider.value.phone,
        secondaryPhone: newProvider.value.secondaryPhone,
        email: newProvider.value.email,
        address: newProvider.value.address
    }

    axios.post(import.meta.env.VITE_API_URL + '/providers/create', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        handleRequestSuccess("Proveedor agregado correctamente");
        getProviders();
        showAddModal.value = false;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        addingProvider.value = false;
    });
};

const updateProvider = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas actualizar este proveedor?', 'Actualizar Proveedor', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        editingProvider.value = true;

        let data = {
            id: editProvider.value.id,
            name: editProvider.value.name,
            phone: editProvider.value.phone,
            secondaryPhone: editProvider.value.secondaryPhone,
            email: editProvider.value.email,
            address: editProvider.value.address
        }

        axios.post(import.meta.env.VITE_API_URL + '/providers/update', data, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            handleRequestSuccess("Proveedor actualizado correctamente");
            getProviders();
            showEditModal.value = false;
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            editingProvider.value = false;
        });
    })
}

const deleteProvider = (id) => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este proveedor?', 'Eliminar Proveedor', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        gettingProviders.value = true;

        let data = {
            id: id,
        }

        axios.post(import.meta.env.VITE_API_URL + '/providers/delete', data, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            handleRequestSuccess("Proveedor eliminado correctamente");
            getProviders();
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            gettingProviders.value = false;
        });
    })
};

const handleCloseAddModal = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar este modal?', 'Cerrar Modal', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        showAddModal.value = false;
        newProvider.value = {
            id: '',
            idType: idTypes.value[0],
            name: '',
            phone: '',
            secondaryPhone: '',
            email: '',
            address: '',
            disabled: true
        }
    })
}

const handleOpenEditProvider = (provider) => {
    editProvider.value = {
        id: provider.id,
        idType: provider.idType,
        name: provider.name,
        phone: provider.phone,
        secondaryPhone: provider.secondaryPhone,
        email: provider.email,
        address: provider.address,
        disabled: provider.disabled
    }
    showEditModal.value = true;
}

const handleCloseEditModal = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar este modal?', 'Cerrar Modal', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        editProvider.value = {
            id: '',
            idType: idTypes.value[0],
            name: '',
            phone: '',
            secondaryPhone: '',
            email: '',
            address: '',
            disabled: true
        }
        showEditModal.value = false;
    })
}

onMounted(() => {
    getProviders();
})

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <h2 class="text-2xl font-bold text-gray-800">Proveedores</h2>

            <!-- Show Disabled -->
            <div class="flex items-center justify-end gap-4 flex-wrap">
                <el-checkbox v-model="showDisabled" label="Ver Deshabilitados" />
                <el-button type="success" @click="showAddModal = true" :loading="addingProvider">
                    <Plus class="w-4 h-4 mr-2" />
                    Agregar Proveedor
                </el-button>
            </div>
        </div>

        <!-- Search -->
        <form class="flex items-center justify-end gap-4" @submit.prevent="getProviders">
            <el-input class="w-full" placeholder="Buscar..." v-model="search" />
            <el-button type="success" @click="getProviders" :loading="gettingProviders">
                <Search class="w-4 h-4 mr-2" />
                Aplicar Búsqueda
            </el-button>
        </form>

        <!-- Providers List -->
        <div class="grid gap-4" v-loading="gettingProviders">
            <div v-for="provider in providers" :key="provider.id"
                class="flex flex-col sm:flex-row items-center justify-between p-4 border rounded-lg"
                :class="provider.disabled ? 'border-red-200 bg-red-100' : 'border-green-200 bg-green-100'">

                <!-- Provider Details -->
                <div class="flex items-center gap-4">
                    <Building2 class="w-8 h-8 text-emerald-600" />
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ provider.name }}</h3>
                        <p class="text-sm text-gray-600">{{ provider.email }} | {{ provider.phone }}</p>
                        <p class="text-sm text-gray-600">{{ provider.address }}</p>
                    </div>
                </div>

                <!-- Provider Actions -->
                <div class="flex items-center gap-2">
                    <button class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded cursor-pointer transition"
                        @click="handleOpenEditProvider(provider)">
                        <Edit class="w-4 h-4" />
                    </button>

                    <button v-if="!provider.disabled" @click="deleteProvider(provider.id)"
                        class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded cursor-pointer">
                        <Trash2 class="w-4 h-4" />
                    </button>
                    <button v-else @click="deleteProvider(provider.id)"
                        class="p-2 text-green-600 bg-green-50 hover:bg-green-100 rounded cursor-pointer transition">
                        <Undo2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <div v-if="providers.length === 0" class="border-l-4 border-red-400 bg-red-100 rounded-lg p-4">
                <p class="text-gray-600 text-center font-medium">No hay proveedores registrados.</p>
            </div>
        </div>

        <!-- Add Provider Modal -->
        <el-dialog v-model="showAddModal" title="Agregar Nuevo Proveedor" :before-close="handleCloseAddModal"
            :fullscreen="fullScreenModals">
            <Line orientation="horizontal" class="bg-gray-200" />
            <el-form :model="newProvider" class="p-4" v-loading="addingProvider">
                <el-form-item label="CI o RIF del proveedor">
                    <el-select v-model="newProvider.idType" placeholder="V-">
                        <el-option v-for="idType in idTypes" :label="idType" :value="idType" />
                    </el-select>
                    <el-input v-model="newProvider.id" placeholder="Ej: 1234567890" />
                </el-form-item>

                <el-form-item label="Nombre del proveedor">
                    <el-input v-model="newProvider.name" placeholder="Ej: Distribuidora XYZ" />
                </el-form-item>

                <el-form-item label="Teléfono">
                    <el-input v-model="newProvider.phone" placeholder="Ej: +1234567890" />
                </el-form-item>

                <el-form-item label="Teléfono secundario">
                    <el-input v-model="newProvider.secondaryPhone" placeholder="Ej: +1234567890" />
                </el-form-item>

                <el-form-item label="Correo electrónico">
                    <el-input v-model="newProvider.email" placeholder="Ej: contacto@empresa.com" />
                </el-form-item>

                <el-form-item label="Dirección">
                    <el-input v-model="newProvider.address" type="textarea"
                        placeholder="Ingrese la dirección del proveedor" :rows="3" />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <el-button @click="handleCloseAddModal">Cancelar</el-button>
                    <el-button type="primary" @click="addProvider">Guardar</el-button>
                </div>
            </template>
        </el-dialog>

        <!-- Edit Provider Modal -->
        <el-dialog v-model="showEditModal" title="Editar Proveedor" :before-close="handleCloseEditModal"
            :fullscreen="fullScreenModals">
            <Line orientation="horizontal" class="bg-gray-200" />
            <el-form :model="editProvider" class="p-4" v-loading="editingProvider">
                <el-form-item label="Nombre del proveedor">
                    <el-input v-model="editProvider.name" placeholder="Ej: Distribuidora XYZ" />
                </el-form-item>

                <el-form-item label="Teléfono">
                    <el-input v-model="editProvider.phone" placeholder="Ej: +1234567890" />
                </el-form-item>

                <el-form-item label="Teléfono secundario">
                    <el-input v-model="editProvider.secondaryPhone" placeholder="Ej: +1234567890" />
                </el-form-item>

                <el-form-item label="Correo electrónico">
                    <el-input v-model="editProvider.email" placeholder="Ej: contacto@empresa.com" />
                </el-form-item>

                <el-form-item label="Dirección">
                    <el-input v-model="editProvider.address" type="textarea"
                        placeholder="Ingrese la dirección del proveedor" :rows="3" />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <el-button @click="handleCloseEditModal">Cancelar</el-button>
                    <el-button type="primary" @click="updateProvider">Guardar</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>
