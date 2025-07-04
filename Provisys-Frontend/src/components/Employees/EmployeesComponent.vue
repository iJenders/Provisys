<template>
    <div>
        <!-- Header and Search -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-4 flex-wrap">
                <h2 class="text-xl font-semibold">Empleados</h2>
                <el-input v-model="searchQuery" placeholder="Buscar empleados..." class="w-64" @input="handleSearch">
                    <template #prefix>
                        <el-icon>
                            <Search />
                        </el-icon>
                    </template>
                </el-input>
            </div>
            <el-button type="primary" @click="openAddDialog">
                <el-icon>
                    <Plus />
                </el-icon>
                Nuevo Empleado
            </el-button>
        </div>

        <!-- Employees Table -->
        <el-table :data="employees" stripe style="width: 100%" :loading="loading">
            <el-table-column prop="username" label="Usuario" width="150" />
            <el-table-column prop="fullName" label="Nombre Completo" width="200">
                <template #default="scope">
                    {{ scope.row.names }} {{ scope.row.lastNames }}
                </template>
            </el-table-column>
            <el-table-column prop="email" label="Email" width="200" />
            <el-table-column prop="role" label="Rol" width="150">
                <template #default="scope">
                    {{ scope.row.role.name }}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="Estado" width="120">
                <template #default="scope">
                    <el-tag :type="scope.row.status ? 'success' : 'info'">
                        {{ scope.row.status ? 'Activo' : 'Inactivo' }}
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
            <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize" :total="totalEmployees"
                layout="total, prev, pager, next" @current-change="handlePageChange" />
        </div>

        <!-- Add/Edit Employee Dialog -->
        <el-dialog v-model="dialogVisible" :title="employeeForm.id ? 'Editar Empleado' : 'Nuevo Empleado'" width="80%">
            <el-form ref="employeeFormRef" :model="employeeForm" :rules="rules" label-width="120px">
                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Usuario" prop="username">
                            <el-input v-model="employeeForm.username" placeholder="Nombre de usuario"
                                :disabled="!!employeeForm.id" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12" v-if="!employeeForm.id">
                        <el-form-item label="Contraseña" prop="password">
                            <el-input v-model="employeeForm.password" type="password" placeholder="Contraseña"
                                show-password />
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Nombre" prop="names">
                            <el-input v-model="employeeForm.names" placeholder="Nombre" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Apellido" prop="lastNames">
                            <el-input v-model="employeeForm.lastNames" placeholder="Apellido" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Email" prop="email">
                            <el-input v-model="employeeForm.email" placeholder="Email" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Teléfono" prop="phone">
                            <el-input v-model="employeeForm.phone" placeholder="Teléfono" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Teléfono Secundario" prop="secondaryPhone">
                            <el-input v-model="employeeForm.secondaryPhone" placeholder="Teléfono secundario" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Dirección" prop="address">
                            <el-input v-model="employeeForm.address" placeholder="Dirección" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Rol" prop="roleId">
                            <el-select v-model="employeeForm.roleId" placeholder="Seleccionar rol">
                                <el-option v-for="role in roles" :key="role.id" :label="role.name" :value="role.id" />
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
import { ref, onMounted } from 'vue'
import { Search, Edit, Delete, Plus } from 'lucide-vue-next'
import { ElMessage, ElMessageBox } from 'element-plus'
import axios from 'axios'
import { handleRequestError } from '@/utils/fetchNotificationsHandlers'
import { successNotification } from '@/utils/feedback'

const employees = ref([])
const roles = ref([])
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const pageSize = ref(10)
const totalEmployees = ref(0)
const dialogVisible = ref(false)
const saving = ref(false)
const employeeForm = ref({
    id: null,
    username: '',
    password: '',
    names: '',
    lastNames: '',
    email: '',
    phone: '',
    secondaryPhone: '',
    address: '',
    roleId: null
})

const rules = {
    username: [
        { required: true, message: 'Por favor, ingrese el nombre de usuario', trigger: 'blur' }
    ],
    password: [
        { required: true, message: 'Por favor, ingrese la contraseña', trigger: 'blur' }
    ],
    names: [
        { required: true, message: 'Por favor, ingrese el nombre', trigger: 'blur' }
    ],
    lastNames: [
        { required: true, message: 'Por favor, ingrese el apellido', trigger: 'blur' }
    ],
    email: [
        { required: true, message: 'Por favor, ingrese el email', trigger: 'blur' },
        { type: 'email', message: 'Por favor, ingrese un email válido', trigger: 'blur' }
    ],
    phone: [
        { required: true, message: 'Por favor, ingrese el teléfono', trigger: 'blur' }
    ],
    roleId: [
        { required: true, message: 'Por favor, seleccione un rol', trigger: 'change' }
    ]
}

const employeeFormRef = ref(null)

const openAddDialog = () => {
    employeeForm.value = {
        id: null,
        username: '',
        password: '',
        names: '',
        lastNames: '',
        email: '',
        phone: '',
        secondaryPhone: '',
        address: '',
        roleId: null
    }
    dialogVisible.value = true
}

const openEditDialog = (employee) => {
    employeeForm.value = { ...employee, id: employee.username, password: '' }
    dialogVisible.value = true
}

const handleSave = async () => {
    if (!employeeFormRef.value) return

    try {
        await employeeFormRef.value.validate()
        saving.value = true

        if (employeeForm.value.id) {
            await axios.post(import.meta.env.VITE_API_URL + '/employees/update', employeeForm.value, {
                headers: {
                    'Authorization': localStorage.getItem('token')
                }
            }).then(() => {
                successNotification('Empleado actualizado correctamente')

                dialogVisible.value = false
                getEmployees()
            }).catch((error) => {
                handleRequestError(error)
            })
        } else {
            await axios.post(import.meta.env.VITE_API_URL + '/employees/create', employeeForm.value, {
                headers: {
                    'Authorization': localStorage.getItem('token')
                }
            }).then(() => {
                successNotification('Empleado creado correctamente')

                dialogVisible.value = false
                getEmployees()
            }).catch((error) => {
                handleRequestError(error)
            })
        }
    } catch (error) {
        handleRequestError(error)
    } finally {
        saving.value = false
    }
}

const handleDelete = async (employee) => {
    try {
        await ElMessageBox.confirm(
            '¿Está seguro que desea eliminar este empleado?',
            'Advertencia',
            {
                type: 'warning',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }
        )

        await axios.post(import.meta.env.VITE_API_URL + '/employees/delete', { username: employee.username }, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        })
        successNotification('Empleado eliminado correctamente')
        await getEmployees()
    } catch (error) {
        if (error !== 'cancel') {
            handleRequestError(error)
        }
    }
}

const handleSearch = () => {
    currentPage.value = 1
    getEmployees()
}

const handlePageChange = () => {
    getEmployees()
}

const getEmployees = async () => {
    loading.value = true
    try {
        const response = await axios.post(import.meta.env.VITE_API_URL + '/employees', {
            page: currentPage.value,
            limit: pageSize.value,
            search: searchQuery.value
        }, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        })
        employees.value = response.data.response.employees
        totalEmployees.value = response.data.response.count
    } catch (error) {
        handleRequestError(error)
    } finally {
        loading.value = false
    }
}

const getRoles = async () => {
    try {
        const response = await axios.post(import.meta.env.VITE_API_URL + '/roles/all', {}, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        })
        roles.value = response.data.response.roles
    } catch (error) {
        handleRequestError(error)
    }
}

onMounted(async () => {
    await getEmployees()
    await getRoles()
})
</script>
