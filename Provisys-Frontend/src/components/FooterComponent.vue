<script setup>
import Line from './Line.vue'
</script>

<template>
    <footer class="flex flex-col items-center justify-center text-white px-10">
        <Line orientation="horizontal" class="bg-stone-300" />
        <div class="flex flex-col items-center justify-center md:flex-row md:justify-between w-full h-full py-6">
            <p class="text-black text-center">&copy; 2025 Codalca. Todos los derechos reservados.</p>
            <p class="text-stone-500 text-center">Av. Intecomunal Lara-Acarigua, sector los rastrojos</p>
        </div>
    </footer>
</template>