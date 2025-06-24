<template>
    <div>
        <!-- Header and Search -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-4 flex-wrap">
                <h2 class="text-xl font-semibold">Roles</h2>
                <el-input v-model="searchQuery" placeholder="Buscar roles..." class="w-64" @input="handleSearch">
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
                Nuevo Rol
            </el-button>
        </div>

        <!-- Roles Table -->
        <el-table :data="roles"  border style="width: 100%" :loading="loading">
            <el-table-column prop="name" label="Nombre" width="200" />
            <el-table-column prop="description" label="Descripción" min-width="200"/>
            <el-table-column label="Acciones" min-width="400">
                <template #default="scope">
                    <div>
                        <el-button type="primary" link size="small" @click="openEditDialog(scope.row)">
                            <el-icon>
                                <Edit />
                            </el-icon>
                            Editar
                        </el-button>
                        <el-button v-if="scope.row.disabled" type="danger" link size="small"
                            @click="handleDelete(scope.row)">
                            <el-icon>
                                <Delete />
                            </el-icon>
                            Eliminar
                        </el-button>
                        <el-button v-else type="success" link size="small" @click="handleDelete(scope.row)">
                            <el-icon>
                                <Delete />
                            </el-icon>
                            Restaurar
                        </el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <!-- Pagination -->
        <div class="mt-4">
            <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize" :total="totalRoles"
                layout="total, prev, pager, next" @current-change="handlePageChange" />
        </div>

        <!-- Add/Edit Role Dialog -->
        <el-dialog v-model="dialogVisible" :title="roleForm.id ? 'Editar Rol' : 'Nuevo Rol'" width="60%">
            <el-form ref="roleFormRef" :model="roleForm" :rules="rules" label-width="120px">
                <el-form-item label="Nombre" prop="name">
                    <el-input v-model="roleForm.name" placeholder="Nombre del rol" />
                </el-form-item>
                <el-form-item label="Descripción" prop="description">
                    <el-input v-model="roleForm.description" type="textarea" :rows="3"
                        placeholder="Descripción del rol" />
                </el-form-item>
                
                <!-- Permissions Tree -->
                <el-form-item v-if="roleForm.id" label="Permisos">
                    <el-tree
                        ref="permissionsTree"
                        :data="allPermissions"
                        show-checkbox
                        node-key="id"
                        :default-checked-keys="selectedPermissions"
                        :props="treeProps"
                        style="width: 100%;"
                    />
                </el-form-item>
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
import { ref, onMounted, nextTick } from 'vue'
import { Search, Edit, Delete, Plus } from 'lucide-vue-next'
import { ElMessage, ElMessageBox } from 'element-plus'
import axios from 'axios'
import { handleRequestError } from '@/utils/fetchNotificationsHandlers'

const roles = ref([])
const allPermissions = ref([])
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const pageSize = ref(10)
const totalRoles = ref(0)
const dialogVisible = ref(false)
const saving = ref(false)
const roleForm = ref({
    id: null,
    name: '',
    description: ''
})
const selectedPermissions = ref([])

const rules = {
    name: [
        { required: true, message: 'Por favor, ingrese el nombre del rol', trigger: 'blur' }
    ],
    description: [
        { required: true, message: 'Por favor, ingrese la descripción del rol', trigger: 'blur' }
    ]
}

const roleFormRef = ref(null)
const permissionsTree = ref(null)

const treeProps = {
    label: 'name',
    children: 'children'
}

const openAddDialog = () => {
    roleForm.value = {
        id: null,
        name: '',
        description: ''
    }
    selectedPermissions.value = []
    dialogVisible.value = true
}

const openEditDialog = async (role) => {
    roleForm.value = { ...role }
    dialogVisible.value = true
    await getRolePermissions(role.id)
    // Use nextTick to ensure the tree is rendered before setting checked keys
    await nextTick()
    if (permissionsTree.value) {
        permissionsTree.value.setCheckedKeys(selectedPermissions.value, false)
    }
}

const getRolePermissions = async (roleId) => {
    try {
        const response = await axios.post(import.meta.env.VITE_API_URL + '/roles/permissions', { id: roleId }, {
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
        })
        selectedPermissions.value = response.data.response.permissions
    } catch (error) {
        handleRequestError(error)
        selectedPermissions.value = []
    }
}

const handleSave = async () => {
    if (!roleFormRef.value) return

    try {
        await roleFormRef.value.validate()
        saving.value = true

        const headers = {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }

        // Guardar detalles del rol
        const response = roleForm.value.id
            ? await axios.post(import.meta.env.VITE_API_URL + '/roles/edit', roleForm.value, { headers })
            : await axios.post(import.meta.env.VITE_API_URL + '/roles/create', roleForm.value, { headers })

        // Si es un rol existente, actualizar permisos
        if (roleForm.value.id && permissionsTree.value) {   
            const checkedKeys = permissionsTree.value.getCheckedKeys()
            await axios.post(import.meta.env.VITE_API_URL + '/roles/permissions/update', {
                id: roleForm.value.id,
                permissions: checkedKeys
            }, { headers })
        }

        ElMessage({
            message: roleForm.value.id ? 'Rol actualizado correctamente' : 'Rol creado correctamente',
            type: 'success'
        })

        dialogVisible.value = false
        await getRoles()
    } catch (error) {
        handleRequestError(error)
    } finally {
        saving.value = false
    }
}

const handleDelete = async (role) => {
    try {
        await ElMessageBox.confirm(
            '¿Está seguro que desea eliminar este rol?',
            'Advertencia',
            {
                type: 'warning',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }
        )

        await axios.post(import.meta.env.VITE_API_URL + '/roles/delete', { id: role.id }, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        ElMessage({
            message: 'Rol eliminado correctamente',
            type: 'success'
        })
        await getRoles()
    } catch (error) {
        if (error !== 'cancel') {
            handleRequestError(error)
        }
    }
}

const handleSearch = () => {
    currentPage.value = 1
    getRoles()
}

const handlePageChange = () => {
    getRoles()
}

const getRoles = async () => {
    loading.value = true
    try {
        const response = await axios.post(import.meta.env.VITE_API_URL + '/roles', {
            page: currentPage.value,
            search: searchQuery.value
        }, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        roles.value = response.data.response.roles
        totalRoles.value = response.data.response.count
    } catch (error) {
        handleRequestError(error)
    } finally {
        loading.value = false
    }
}

const getAllPermissions = async () => {
    try {
        const response = await axios.post(import.meta.env.VITE_API_URL + '/permissions/tree', {}, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        allPermissions.value = response.data.response
    } catch (error) {
        handleRequestError(error)
    }
}

onMounted(async () => {
    await getRoles()
    await getAllPermissions()
})
</script>
