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
                    <el-button type="danger" link size="small" @click="handleDelete(scope.row)" :disabled="scope.row.status">
                        <el-icon>
                            <Delete />
                        </el-icon>
                        Eliminar
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
                        <el-form-item label="Rol" prop="roleId">
                            <el-select v-model="employeeForm.roleId" placeholder="Seleccionar rol">
                                <el-option v-for="role in roles" :key="role.id" :label="role.name" :value="role.id" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" :sm="12">
                        <el-form-item label="Estado" prop="status">
                            <el-switch v-model="employeeForm.status" active-text="Activo" inactive-text="Inactivo" />
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
    names: '',
    lastNames: '',
    email: '',
    phone: '',
    roleId: null,
    status: true
})

const rules = {
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
        names: '',
        lastNames: '',
        email: '',
        phone: '',
        roleId: null,
        status: true
    }
    dialogVisible.value = true
}

const openEditDialog = (employee) => {
    employeeForm.value = { ...employee }
    dialogVisible.value = true
}

const handleSave = async () => {
    if (!employeeFormRef.value) return
    
    try {
        await employeeFormRef.value.validate()
        saving.value = true

        const response = employeeForm.value.id
            ? await axios.put(`/api/employees/${employeeForm.value.id}`, employeeForm.value)
            : await axios.post('/api/employees', employeeForm.value)

        ElMessage({
            message: employeeForm.value.id ? 'Empleado actualizado correctamente' : 'Empleado creado correctamente',
            type: 'success'
        })

        dialogVisible.value = false
        await getEmployees()
    } catch (error) {
        ElMessage({
            message: error.response?.data?.message || 'Error al guardar el empleado',
            type: 'error'
        })
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

        await axios.delete(`/api/employees/${employee.id}`)
        ElMessage({
            message: 'Empleado eliminado correctamente',
            type: 'success'
        })
        await getEmployees()
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage({
                message: error.response?.data?.message || 'Error al eliminar el empleado',
                type: 'error'
            })
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
        const response = await axios.get('/api/employees', {
            params: {
                page: currentPage.value,
                limit: pageSize.value,
                search: searchQuery.value
            }
        })
        employees.value = response.data.data
        totalEmployees.value = response.data.total
    } catch (error) {
        ElMessage({
            message: error.response?.data?.message || 'Error al cargar empleados',
            type: 'error'
        })
    } finally {
        loading.value = false
    }
}

const getRoles = async () => {
    try {
        const response = await axios.get('/api/roles')
        roles.value = response.data
    } catch (error) {
        ElMessage({
            message: error.response?.data?.message || 'Error al cargar roles',
            type: 'error'
        })
    }
}

onMounted(async () => {
    await Promise.all([getEmployees(), getRoles()])
})
</script>
