<template>
    <div class="w-full p-6 h-screen flex items-center justify-center">
        <div
            class="w-full md:w-[400px] flex flex-col items-center justify-center gap-4 bg-white border border-stone-200 rounded-lg p-6 shadow-md relative">
            <h2 class="text-stone-700 text-2xl font-bold">Iniciar Sesión</h2>
            <Line class="bg-stone-200" orientation="horizontal" />
            <el-form label-position="top" class="w-full" label-width="100px" v-loading="loading">
                <el-form-item label="Usuario">
                    <el-input v-model="user.username" :prefix-icon="User" />
                </el-form-item>
                <el-form-item label="Contraseña">
                    <el-input v-model="user.password" :type="showPassword ? 'text' : 'password'"
                        :prefix-icon="KeyRound" />
                    <el-checkbox v-model="showPassword">{{ showPassword ? 'Ocultar' : 'Mostrar' }}
                        Contraseña</el-checkbox>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" class="w-full" @click="handleLogin">Iniciar Sesión</el-button>
                </el-form-item>
            </el-form>
            <div class="w-full flex flex-col items-center gap-2">
                <el-text class="text-stone-500 text-sm">
                    ¿No tienes una cuenta? <router-link to="/register" class="text-blue-500">Regístrate
                        aquí</router-link>
                </el-text>
                <el-text class="text-stone-500 text-sm">
                    ¿Olvidaste tu contraseña? <router-link to="/reset-password" class="text-blue-500">Recupérala
                        aquí</router-link>
                </el-text>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Line from '@/components/Line.vue';
import { User, KeyRound } from 'lucide-vue-next'
import { ElNotification } from 'element-plus';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();
const router = useRouter();

const user = ref({
    username: '',
    password: ''
});

const showPassword = ref(false);

const loading = ref(false);

const handleLogin = () => {
    loading.value = true;

    // Petición de inicio de sesión

    axios.post(import.meta.env.VITE_API_URL + '/login', user.value)
        .then(response => {
            loading.value = false;

            ElNotification({
                title: 'Éxito',
                message: 'Inicio de sesión exitoso',
                type: 'success',
                duration: 3000,
                offset: 80,
                zIndex: 10000,
            });

            localStorage.setItem('token', response.data.response.token);
            authStore.token = response.data.response.token;
            authStore.refreshUser();

            router.push('/');
        })
        .catch(error => {
            loading.value = false;

            let message
            if (error.status === 400) {
                message = 'Error al validar los datos de inicio de sesión:'
                    + error.response.data.response.errors.map(e => { return `<li class="pl-4">${e}</li>` }).join('');
            } else if (error.status === 401) {
                message = 'Usuario o contraseña incorrectos';
            } else if (error.status === 500) {
                message = 'Error interno del servidor';
            } else {
                message = 'Error desconocido';
            }

            ElNotification({
                title: 'Error',
                dangerouslyUseHTMLString: true,
                message: message,
                type: 'error',
                duration: 3000,
                offset: 80,
                zIndex: 10000,
            });
        });
}
</script>