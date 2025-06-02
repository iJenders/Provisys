<script setup>
import { ref } from 'vue';
import { Clock, Receipt, PackageCheck, Trash2 } from 'lucide-vue-next';
import WaitingOrders from '@/components/Orders/WaitingOrders.vue';
import BilledOrders from '@/components/Orders/BilledOrders.vue';
import DeliveredOrders from '@/components/Orders/DeliveredOrders.vue';
import CanceledOrders from '@/components/Orders/CanceledOrders.vue';

const ordersButtons = [
    {
        name: 'En Espera',
        icon: Clock,
        component: WaitingOrders
    },
    {
        name: 'Facturados',
        icon: Receipt,
        component: BilledOrders
    },
    {
        name: 'Entregados',
        icon: PackageCheck,
        component: DeliveredOrders
    },
    {
        name: 'Cancelados',
        icon: Trash2,
        component: CanceledOrders
    }
]

const activeSection = ref(0);

</script>

<template>
    <div class="grow min-w-0 flex flex-col gap-6">
        <!-- Orders Status Links -->
        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <div v-for="(orderButton, index) in ordersButtons" @click="activeSection = index" :key="index"
                class="min-w-[160px] flex flex-col justify-center items-center rounded-lg p-4 shadow-md cursor-pointer transition"
                :class="index == activeSection ? `bg-emerald-700 text-white` : 'bg-white text-stone-700'">
                <component :is="orderButton.icon" class="w-10 h-10" />
                <p class="font-medium">{{ orderButton.name }}</p>
            </div>
        </div>

        <!-- Orders -->
        <TransitionGroup name="fade" tag="div" class="w-full grow-0 overflow-hidden relative">
            <component :is="ordersButtons[activeSection].component" class="w-full bg-white shadow-md rounded-lg p-6"
                :key="ordersButtons[activeSection].name" />
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