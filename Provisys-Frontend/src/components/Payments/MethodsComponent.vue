<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import { CreditCard, Plus, Edit, Trash2, Search, Undo2 } from 'lucide-vue-next';
import Line from '../Line.vue';
import axios from 'axios';
import { handleRequestError, handleRequestSuccess } from '@/utils/fetchNotificationsHandlers';
import { ElMessageBox, ElNotification } from 'element-plus';

const paymentMethods = ref([]);

const showAddModal = ref(false);
const showEditModal = ref(false);
const newMethod = ref({
    name: '',
    description: '',
    disabled: true
});
const editMethod = ref({
    id: '',
    name: '',
    description: '',
    disabled: true
})

const gettingMethods = ref(false);
const addingMethod = ref(false);
const editingMethod = ref(false);

const showDisabled = ref(false);
const search = ref('');

const getMethods = () => {
    gettingMethods.value = true;

    let data = {
        filters: {
            deleted: 0
        },
        search: search.value
    }
    if (showDisabled.value) {
        delete data.filters.deleted;
    }

    axios.post(import.meta.env.VITE_API_URL + '/paymentMethods', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        paymentMethods.value = response.data.response.paymentMethods;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        gettingMethods.value = false;
    });
}

const addMethod = () => {
    addingMethod.value = true;

    let data = {
        name: newMethod.value.name,
        description: newMethod.value.description,
    }

    axios.post(import.meta.env.VITE_API_URL + '/paymentMethods/create', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        handleRequestSuccess("Método de pago agregado correctamente");
        getMethods();
        showAddModal.value = false;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        addingMethod.value = false;
    });
};

const updateMethod = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas actualizar este método de pago?', 'Actualizar Método de Pago', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        editingMethod.value = true;

        let data = {
            id: editMethod.value.id,
            name: editMethod.value.name,
            description: editMethod.value.description,
        }

        axios.post(import.meta.env.VITE_API_URL + '/paymentMethods/update', data, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            handleRequestSuccess("Método de pago actualizado correctamente");
            getMethods();
            showEditModal.value = false;
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            editingMethod.value = false;
        });
    })
}

const deleteMethod = (id) => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este método de pago?', 'Eliminar Método de Pago', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        gettingMethods.value = true;

        let data = {
            id: id,
        }

        axios.post(import.meta.env.VITE_API_URL + '/paymentMethods/delete', data, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            handleRequestSuccess("Método de pago eliminado correctamente");
            getMethods();
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            gettingMethods.value = false;
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
        newMethod.value = {
            name: '',
            description: '',
            disabled: true
        }
    })
}
const handleOpenEditMethod = (method) => {
    editMethod.value = {
        id: method.id,
        name: method.name,
        description: method.description,
        disabled: method.disabled
    }
    showEditModal.value = true;
}
const handleCloseEditModal = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar este modal?', 'Cerrar Modal', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        editMethod.value = {
            id: '',
            name: '',
            description: '',
            disabled: true
        }
        showEditModal.value = false;
    })
}

onMounted(() => {
    getMethods();
})

const { fullScreenModals } = useFullScreenModals();
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <h2 class="text-2xl font-bold text-gray-800">Métodos de Pago</h2>

            <!-- Show Disabled -->
            <div class="flex items-center justify-end gap-4 flex-wrap">
                <el-checkbox v-model="showDisabled" label="Ver Deshabilitados" />
                <el-button type="success" @click="showAddModal = true" :loading="addingMethod">
                    <Plus class="w-4 h-4 mr-2" />
                    Agregar Método
                </el-button>
            </div>
        </div>

        <!-- Search -->
        <form class="flex items-center justify-end gap-4" @submit.prevent="getMethods">
            <el-input class="w-full" placeholder="Buscar..." v-model="search" />
            <el-button type="success" @click="getMethods" :loading="gettingMethods">
                <Search class="w-4 h-4 mr-2" />
                Aplicar Búsqueda
            </el-button>
        </form>

        <!-- Payment Methods -->
        <div class="grid gap-4" v-loading="gettingMethods">
            <div v-for="method in paymentMethods" :key="method.id"
                class="flex flex-col sm:flex-row items-center justify-between p-4 border rounded-lg"
                :class="method.deleted ? 'border-red-200 bg-red-100' : 'border-green-200 bg-green-100'">

                <!-- Method Details -->
                <div class="flex items-center gap-4">
                    <CreditCard class="w-8 h-8 text-emerald-600" />
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ method.name }}</h3>
                        <p class="text-sm text-gray-600">{{ method.description }}</p>
                    </div>
                </div>

                <!-- Method Actions -->
                <div class="flex items-center gap-2">
                    <button class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded cursor-pointer transition"
                        @click="handleOpenEditMethod(method)">
                        <Edit class="w-4 h-4" />
                    </button>

                    <button v-if="!method.deleted" @click="deleteMethod(method.id)"
                        class="p-2 text-red-600 bg-red-50 hover:bg-red-100 rounded cursor-pointer">
                        <Trash2 class="w-4 h-4" />
                    </button>
                    <button v-else @click="deleteMethod(method.id)"
                        class="p-2 text-green-600 bg-green-50 hover:bg-green-100 rounded cursor-pointer transition">
                        <Undo2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
            <div v-if="paymentMethods.length === 0" class="border-l-4 border-red-400  bg-red-100 rounded-lg p-4">
                <p class="text-gray-600 text-center font-medium">No hay métodos de pago registrados.</p>
            </div>
        </div>

        <!-- Add Method Modal -->
        <el-dialog v-model="showAddModal" title="Agregar Nuevo Método de Pago" :before-close="handleCloseAddModal"
            :fullscreen="fullScreenModals">
            <Line orientation="horizontal" class="bg-gray-200" />
            <el-form :model="newMethod" class="p-4" v-loading="addingMethod">
                <el-form-item label="Nombre del método">
                    <el-input v-model="newMethod.name" placeholder="Ej: Tarjeta de Crédito" />
                </el-form-item>

                <el-form-item label="Descripción">
                    <el-input v-model="newMethod.description" type="textarea"
                        placeholder="Ingrese una descripción del método de pago" :rows="3" />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <el-button @click="handleCloseAddModal">Cancelar</el-button>
                    <el-button type="primary" @click="addMethod">Guardar</el-button>
                </div>
            </template>

        </el-dialog>

        <!-- Edit Method Modal -->
        <el-dialog v-model="showEditModal" title="Editar Método de Pago" :before-close="handleCloseEditModal"
            :fullscreen="fullScreenModals">
            <Line orientation="horizontal" class="bg-gray-200" />
            <el-form :model="editMethod" class="p-4" v-loading="editingMethod">
                <el-form-item label="Nombre del método">
                    <el-input v-model="editMethod.name" placeholder="Ej: Tarjeta de Crédito" />
                </el-form-item>

                <el-form-item label="Descripción">
                    <el-input v-model="editMethod.description" type="textarea"
                        placeholder="Ingrese una descripción del método de pago" :rows="3" />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <el-button @click="handleCloseEditModal">Cancelar</el-button>
                    <el-button type="primary" @click="updateMethod">Guardar</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>