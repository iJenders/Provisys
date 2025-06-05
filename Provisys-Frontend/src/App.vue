<script setup>
import HeaderComponent from './components/HeaderComponent.vue'
import FooterComponent from './components/FooterComponent.vue'
import { onBeforeMount, onMounted } from 'vue';
import { useAuthStore } from './stores/authStore';

const authStore = useAuthStore();

onBeforeMount(() => {
  // Verificar la autenticación al cargar la aplicación
  // y cargar el token desde localStorage si está disponible
  const token = localStorage.getItem('token');
  authStore.token = token;
  if (!!token) {
    authStore.refreshUser();
  }
})
</script>

<template>
  <HeaderComponent />
  <main class="w-full min-h-[calc(100vh-80px)] p-0 pt-[80px]">
    <router-view />
  </main>
  <FooterComponent />
</template>