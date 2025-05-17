<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import HeaderComponent from './components/HeaderComponent.vue'
import FooterComponent from './components/FooterComponent.vue'
</script>

<template>
  <HeaderComponent />
  <main class="w-full min-h-[calc(100vh-80px)] p-0 pt-[80px]">
    <router-view />
  </main>
  <FooterComponent />
</template>