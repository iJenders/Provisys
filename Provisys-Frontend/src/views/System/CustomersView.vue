<template>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Header and Search -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-4 flex-wrap">
                <h1 class="text-2xl font-bold">Gestión de Clientes</h1>
                <el-input v-model="searchQuery" placeholder="Buscar clientes..." class="w-64" @input="handleSearch">
                    <template #prefix>
                        <el-icon>
                            <Search />
                        </el-icon>
                    </template>
                </el-input>
            </div>
        </div>



        <!-- Customers Table -->
        <el-table :data="customers" stripe style="width: 100%" :loading="loading" class="border border-gray-200">
            <el-table-column prop="fullName" label="Nombre Completo" width="250">
                <template #default="scope">
                    {{ scope.row.names }} {{ scope.row.lastNames }}
                </template>
            </el-table-column>
            <el-table-column prop="email" label="Email" min-width="250" />
            <el-table-column prop="phone" label="Teléfono" min-width="180" />
            <el-table-column prop="secondaryPhone" label="Teléfono Secundario" min-width="180">
                <template #default="scope">
                    {{ scope.row.secondaryPhone || 'N/A' }}
                </template>
            </el-table-column>
            <el-table-column prop="address" label="Dirección" min-width="160px" />
            <el-table-column prop="verified" label="Estado" min-width="130">
                <template #default="scope">
                    <el-tag :type="scope.row.verified ? 'success' : 'info'">
                        {{ scope.row.verified ? 'Verificado' : 'No Verificado' }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Acciones" width="150">
                <template #default="scope">
                    <el-button type="primary" link size="small" @click="openEditDialog(scope.row)">
                        <el-icon>
                            <Edit />
                        </el-icon>
                        Editar
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- Pagination -->
        <div class="mt-4">
            <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize" :total="totalCustomers"
                layout="total, prev, pager, next" @current-change="handlePageChange" />
        </div>

        <!-- View/Edit Customer Modal -->
        <el-dialog v-model="dialogVisible" :title="customerForm.id ? 'Editar Cliente' : 'Ver Cliente'" width="80%"
            :fullscreen="fullScreenModals">
            <!-- Not editable details-->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center justify-start gap-2">
                    <p class="font-bold text-base">Nombre de usuario:</p>
                    <p>{{ customerForm.username }}</p>
                </div>
                <div class="flex items-center justify-start gap-2">
                    <p class="font-bold text-base">Fecha de registro:</p>
                    <p>{{ customerForm.registerDate.split(' ')[0] }}</p>
                </div>
                <div class="flex items-center justify-start gap-2">
                    <p class="font-bold text-base">Rol:</p>
                    <p>{{ customerForm.roleId === 1 ? 'Otro...' : 'Cliente' }}</p>
                </div>
            </div>

            <Line orientation="horizontal" class="bg-gray-200 w-full my-4" />

            <!-- Editable details -->
            <el-form ref="customerFormRef" :model="customerForm" :rules="rules" label-width="120px">
                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Nombre" prop="names">
                            <el-input v-model="customerForm.names" placeholder="Nombre" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Apellido" prop="lastNames">
                            <el-input v-model="customerForm.lastNames" placeholder="Apellido" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Email" prop="email">
                            <el-input v-model="customerForm.email" placeholder="Email" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Teléfono" prop="phone">
                            <el-input v-model="customerForm.phone" placeholder="Teléfono" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Teléfono Sec." prop="secondaryPhone">
                            <el-input v-model="customerForm.secondaryPhone" placeholder="Teléfono Secundario" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Dirección" prop="address">
                            <el-input v-model="customerForm.address" placeholder="Dirección" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Estado" prop="verified">
                            <el-select v-model="customerForm.verified" placeholder="Estado">
                                <el-option label="Verificado" :value="1" />
                                <el-option label="No Verificado" :value="0" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">Cancelar</el-button>
                    <el-button type="primary" :loading="saving" @click="handleSave">
                        Guardar
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>

</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { Search, Edit } from 'lucide-vue-next'
import { ElMessage, ElMessageBox } from 'element-plus'
import axios from 'axios'
import { handleRequestError } from '@/utils/fetchNotificationsHandlers'
import { useFullScreenModals } from '@/composables/fullScreenModals'
import Line from '@/components/Line.vue'
import { successNotification } from '@/utils/feedback'

const { fullScreenModals } = useFullScreenModals()

// Form validation rules
const rules = {
    nombres: [
        { required: true, message: 'Por favor, ingrese su nombre', trigger: 'blur' }
    ],
    apellidos: [
        { required: true, message: 'Por favor, ingrese su apellido', trigger: 'blur' }
    ],
    correo: [
        { required: true, message: 'Por favor, ingrese su correo electrónico', trigger: 'blur' },
        { type: 'email', message: 'Por favor, ingrese una dirección de correo electrónico válida', trigger: 'blur' }
    ],
    telefono: [
        { required: true, message: 'Por favor, ingrese su número de teléfono', trigger: 'blur' }
    ],
    verificado: [
        { required: true, message: 'Por favor, seleccione un estado', trigger: 'change' }
    ]
}

// Dialog state
const dialogVisible = ref(false)
const saving = ref(false)
const customerForm = ref({
    username: "vanvan",
    registerDate: "2025-06-18 17:23:50",
    names: "Van",
    lastNames: "De Abarca",
    email: "van@van.van",
    phone: "+584123456789",
    secondaryPhone: "",
    address: "aasd",
    roleId: "2",
    verified: 1
})
const customerFormRef = ref(null)

// Table state
const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const pageSize = ref(10)
const totalCustomers = ref(0)
const customers = ref([])

// Methods
const openEditDialog = (customer) => {
    customerForm.value = JSON.parse(JSON.stringify(customer));
    dialogVisible.value = true
}

const handleSave = () => {
    ElMessageBox.confirm('¿Estás seguro de guardar los cambios?', 'Aviso', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        customerFormRef.value.validate((valid) => {
            if (valid) {
                saving.value = true

                axios.post(import.meta.env.VITE_API_URL + '/clients/update', customerForm.value, {
                    headers: {
                        'Authorization': localStorage.getItem('token')
                    }
                }).then(response => {
                    successNotification('Cliente guardado exitosamente');
                    getClients()
                    dialogVisible.value = false
                }).catch(error => {
                    handleRequestError(error)
                }).finally(() => {
                    saving.value = false
                });
            }
        })
    }).catch(() => {
        // Do nothing
    })
}

const getClients = () => {
    loading.value = true

    const data = {
        search: searchQuery.value || '',
        filters: {
            active: statusFilter.value === 'active' ? 1 : statusFilter.value === 'inactive' ? 0 : undefined
        },
        offset: currentPage.value,
        range: {
            limit: pageSize.value
        }
    }

    axios.post(import.meta.env.VITE_API_URL + '/clients', data, {
        headers: {
            Authorization: `${localStorage.getItem('token')}`
        }
    }).then((res) => {
        customers.value = res.data.response.users
        totalCustomers.value = res.data.response.count
    }).catch((err) => {
        handleRequestError(err)
    }).finally(() => {
        loading.value = false
    })
}

const handleSearch = () => {
    currentPage.value = 1
    getClients()
}

const handlePageChange = () => {
    getClients()
}

onMounted(() => {
    getClients()
})

// Watch for search and filter changes
watch([searchQuery, statusFilter], () => {
    getClients()
})
</script>