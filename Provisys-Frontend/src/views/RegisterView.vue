<template>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex flex-col items-center bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 gap-4">
                <UserRoundPlus class="top-4 left-4 text-blue-500 font-bold" size="64" />
                <h2 class="text-stone-700 text-2xl font-bold">
                    Crear una cuenta
                </h2>
                <Line class="bg-stone-200" orientation="horizontal" />
                <el-form ref="formRef" :model="form" label-position="top" v-loading="fetching" class="w-full">
                    <el-form-item label="Nombres" prop="names">
                        <el-input v-model="form.names" placeholder="Ej: Juan Antonio" />
                    </el-form-item>
                    <el-form-item label="Apellidos" prop="lastNames">
                        <el-input v-model="form.lastNames" placeholder="Ej: Pérez Gómez" />
                    </el-form-item>
                    <el-form-item label="Correo Electrónico" prop="email">
                        <el-input v-model="form.email" type="email" placeholder="Ej: juanperez@ejemplo.com" />
                    </el-form-item>
                    <el-form-item label="Teléfono" prop="phone">
                        <el-input v-model="form.phone" placeholder="Ej: +58 412 345 6789" />
                    </el-form-item>
                    <el-form-item label="Teléfono Secundario" prop="secondaryPhone">
                        <el-input v-model="form.secondaryPhone" placeholder="Ej: +58 414 987 6543" />
                    </el-form-item>
                    <el-form-item label="Dirección" prop="address">
                        <el-input v-model="form.address" type="textarea"
                            placeholder="Ej: Calle Falsa 123, Ciudad, País" />
                    </el-form-item>

                    <Line class="bg-stone-200 mb-3" orientation="horizontal" />

                    <el-form-item label="Nombre de usuario" prop="username">
                        <el-input v-model="form.username" placeholder="Ej: juanperez" />
                    </el-form-item>
                    <el-form-item label="Contraseña" prop="password">
                        <el-input v-model="form.password" :type="form.showPassword ? 'text' : 'password'"
                            placeholder="Ingresa tu contraseña" />
                    </el-form-item>
                    <el-form-item label="Confirmar Contraseña" prop="passwordConfirmation">
                        <el-input v-model="form.passwordConfirmation" :type="form.showPassword ? 'text' : 'password'"
                            placeholder="Confirma tu contraseña" />
                    </el-form-item>
                    <el-checkbox v-model="form.showPassword">
                        Mostrar Contraseña
                    </el-checkbox>
                    <el-button type="primary" class="w-full mt-4" @click="handleRegister">
                        Registrarse
                    </el-button>
                </el-form>
                <Line class="bg-stone-200" orientation="horizontal" />
                <div>
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                ¿Ya tienes una cuenta?
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <router-link to="/login"
                            class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            Inicia Sesión
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { ElNotification } from 'element-plus';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import { UserRoundPlus } from 'lucide-vue-next';
import { useRouter } from 'vue-router';

const router = useRouter();

const formRef = ref(null);

const fetching = ref(false);

const form = ref({
    names: '',
    lastNames: '',
    email: '',
    phone: '',
    secondaryPhone: '',
    address: '',
    username: '',
    password: '',
    passwordConfirmation: '',
    showPassword: false
});

const handleRegister = () => {
    fetching.value = true;

    const dataToSend = { ...form.value };
    delete dataToSend.showPassword;

    axios.post(import.meta.env.VITE_API_URL + '/register', form.value)
        .then(response => {
            ElNotification({
                title: 'Éxito',
                message: 'Registro exitoso. Por favor, inicia sesión.',
                type: 'success',
                duration: 3000,
                offset: 80,
                zIndex: 10000,
            });
            formRef.value.resetFields();
            router.push('/login');
        })
        .catch(error => handleRequestError(error)).finally(() => {
            fetching.value = false;
        });
};

onMounted(() => {
    // Si el usuario ya está autenticado, redirigir a la página principal
    const token = localStorage.getItem('token');
    if (token) {
        router.push('/');
    }
})
</script>
