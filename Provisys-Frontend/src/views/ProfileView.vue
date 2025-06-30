<template>
    <div class="bg-white p-6 rounded-lg shadow-md m-6">
        <h1 class="text-2xl font-bold mb-4">Mi Perfil</h1>

        <el-form ref="profileFormRef" :model="profileForm" :rules="rules" label-width="150px" label-position="top">
            <el-tabs v-model="activeTab">
                <el-tab-pane label="Información Personal" name="personal">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="font-bold text-base">Nombre de usuario:</p>
                            <p>{{ profileForm.username }}</p>
                        </div>
                        <div>
                            <p class="font-bold text-base">Fecha de registro:</p>
                            <p>{{ profileForm.registerDate ? profileForm.registerDate.split(' ')[0] : '' }}</p>
                        </div>
                    </div>

                    <Line orientation="horizontal" class="bg-gray-200 w-full my-6" />

                    <el-row :gutter="20">
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Nombres" prop="names">
                                <el-input v-model="profileForm.names" placeholder="Nombres" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Apellidos" prop="lastNames">
                                <el-input v-model="profileForm.lastNames" placeholder="Apellidos" />
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Email" prop="email">
                                <el-input v-model="profileForm.email" placeholder="Email" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Teléfono" prop="phone">
                                <el-input v-model="profileForm.phone" placeholder="Teléfono" />
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Teléfono Secundario" prop="secondaryPhone">
                                <el-input v-model="profileForm.secondaryPhone" placeholder="Teléfono Secundario" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Dirección" prop="address">
                                <el-input v-model="profileForm.address" placeholder="Dirección" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-tab-pane>

                <el-tab-pane label="Cambiar Contraseña" name="password">
                    <el-row :gutter="20" class="mt-4">
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Nueva Contraseña" prop="password">
                                <el-input type="password" v-model="profileForm.password" placeholder="Nueva Contraseña" show-password />
                            </el-form-item>
                        </el-col>
                        <el-col :span="24" :sm="12">
                            <el-form-item label="Confirmar Contraseña" prop="passwordConfirmation">
                                <el-input type="password" v-model="profileForm.passwordConfirmation" placeholder="Confirmar Contraseña" show-password />
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-tab-pane>
            </el-tabs>
        </el-form>

        <div class="mt-6 flex justify-end">
            <el-button type="primary" :loading="saving" @click="handleSave">
                Guardar Cambios
            </el-button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import axios from 'axios'
import { handleRequestError } from '@/utils/fetchNotificationsHandlers'
import { successNotification } from '@/utils/feedback'
import Line from '@/components/Line.vue'

const activeTab = ref('personal')
const profileFormRef = ref(null)
const saving = ref(false)
const profileForm = ref({
    username: '',
    registerDate: '',
    names: '',
    lastNames: '',
    email: '',
    phone: '',
    secondaryPhone: '',
    address: '',
    password: '',
    passwordConfirmation: ''
})

const passwordConfirmationRule = (rule, value, callback) => {
    if (profileForm.value.password && value !== profileForm.value.password) {
        callback(new Error('Las contraseñas no coinciden'))
    } else {
        callback()
    }
}

const rules = {
    names: [{ required: true, message: 'Por favor, ingrese sus nombres', trigger: 'blur' }],
    lastNames: [{ required: true, message: 'Por favor, ingrese sus apellidos', trigger: 'blur' }],
    email: [
        { required: true, message: 'Por favor, ingrese su correo electrónico', trigger: 'blur' },
        { type: 'email', message: 'Por favor, ingrese un correo electrónico válido', trigger: 'blur' }
    ],
    phone: [{ required: true, message: 'Por favor, ingrese su número de teléfono', trigger: 'blur' }],
    address: [{ required: true, message: 'Por favor, ingrese su dirección', trigger: 'blur' }],
    password: [
        { min: 8, message: 'La contraseña debe tener al menos 8 caracteres', trigger: 'blur' }
    ],
    passwordConfirmation: [
        { validator: passwordConfirmationRule, trigger: 'blur' }
    ]
}

const getUserData = () => {
    axios.post(import.meta.env.VITE_API_URL + '/auth/user', {}, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        profileForm.value = { ...response.data.response.user, password: '', passwordConfirmation: '' }
    }).catch(error => {
        handleRequestError(error)
    })
}

const handleSave = () => {
    profileFormRef.value.validate((valid) => {
        if (valid) {
            ElMessageBox.confirm('¿Estás seguro de que deseas guardar los cambios?', 'Confirmación', {
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                type: 'warning'
            }).then(() => {
                saving.value = true
                const dataToSave = { ...profileForm.value }
                if (!dataToSave.password) {
                    delete dataToSave.password
                    delete dataToSave.passwordConfirmation
                }

                axios.post(import.meta.env.VITE_API_URL + '/profile/update', dataToSave, {
                    headers: {
                        'Authorization': localStorage.getItem('token')
                    }
                }).then(() => {
                    successNotification('Perfil actualizado correctamente')
                    getUserData() // Refresh data
                }).catch(error => {
                    handleRequestError(error)
                }).finally(() => {
                    saving.value = false
                })
            }).catch(() => {
                // User cancelled
            })
        }
    })
}

onMounted(() => {
    getUserData()
})
</script>