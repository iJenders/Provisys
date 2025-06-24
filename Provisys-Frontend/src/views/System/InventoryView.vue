
<script setup>
import { ref } from 'vue';
import { Warehouse, Trash2, ArrowLeftRight, ShoppingCart } from 'lucide-vue-next';
import StoragesComponent from '@/components/Inventory/StoragesComponent.vue';
import WastesComponent from '@/components/Inventory/WastesComponent.vue';

const inventoryButtons = [
    {
        name: 'Almacenes',
        icon: Warehouse,
        component: StoragesComponent
    },
    {
        name: 'Mermas',
        icon: Trash2,
        component: WastesComponent
    },
]

const activeSection = ref(0);

</script>

<template>
    <div class="grow min-w-0 flex flex-col gap-6">
        <!-- Inventory Menu Links -->
        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <div v-for="(inventoryButton, index) in inventoryButtons" @click="activeSection = index" :key="index"
                class="min-w-[160px] flex flex-col justify-center items-center rounded-lg p-4 shadow-md cursor-pointer transition"
                :class="index == activeSection ? `bg-emerald-700 text-white` : 'bg-white text-stone-700'">
                <component :is="inventoryButton.icon" class="w-10 h-10" />
                <p class="font-medium text-center">{{ inventoryButton.name }}</p>
            </div>
        </div>

        <!-- Inventory Menus -->
        <TransitionGroup name="fade" tag="div" class="w-full grow-0 overflow-hidden relative">
            <component :is="inventoryButtons[activeSection].component" class="w-full bg-white shadow-md rounded-lg p-6"
                :key="inventoryButtons[activeSection].name" />
        </TransitionGroup>
    </div>
</template>

<style scoped>
.fade-move,
.fade-enter-active,
.fade-leave-active {
    transition: all 0.2s cubic-bezier(0.55, 0, 0.1, 1);
}

.fade-enter-active {
    transition-delay: 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translate(30px, 0);
}

.fade-leave-active {
    position: absolute;
}
</style>
