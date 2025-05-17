<script setup>
import Line from './Line.vue'
</script>

<template>
    <footer class="flex flex-col items-center justify-center text-white h-[80px] px-10 gap-5">
        <Line orientation="horizontal" class="bg-stone-300" />
        <div class="flex items-center justify-between w-full h-full">
            <p class="text-black">&copy; 2025 Codalca. Todos los derechos reservados.</p>
            <p class="text-stone-500">Av. Intecomunal Lara-Acarigua, sector los rastrojos</p>
        </div>
    </footer>
</template>