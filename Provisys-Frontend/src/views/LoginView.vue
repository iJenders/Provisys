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

const user = ref({
    username: '',
    password: ''
});

const showPassword = ref(false);

const loading = ref(false);

const handleLogin = () => {
    loading.value = true;
    // Simular petición
    setTimeout(() => {
        loading.value = false;
        ElNotification.success({
            title: 'Éxito',
            message: 'Inicio de sesión exitoso.',
            duration: 3000,
            zIndex: 10000,
            offset: 80
        });
    }, 1000);
}
</script>