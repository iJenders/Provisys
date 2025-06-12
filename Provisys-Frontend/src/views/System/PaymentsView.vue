<script setup>
import { ref } from 'vue';
import { CreditCard, Receipt, History, Settings, DollarSign } from 'lucide-vue-next';
import MethodsComponent from '@/components/Payments/MethodsComponent.vue';
import PaymentsComponent from '@/components/Payments/PaymentsComponent.vue';
import HistoryComponent from '@/components/Payments/HistoryComponent.vue';

const paymentButtons = [
    {
        name: 'Métodos',
        icon: CreditCard,
        component: MethodsComponent
    },
    {
        name: 'Pagos',
        icon: DollarSign,
        component: PaymentsComponent
    },
    {
        name: 'Historial',
        icon: History,
        component: HistoryComponent
    }
]

const activeSection = ref(0);

</script>

<template>
    <div class="grow min-w-0 flex flex-col gap-6">
        <!-- Payment Menu Links -->
        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <div v-for="(paymentButton, index) in paymentButtons" @click="activeSection = index" :key="index"
                class="min-w-[160px] flex flex-col justify-center items-center rounded-lg p-4 shadow-md cursor-pointer transition"
                :class="index == activeSection ? `bg-emerald-700 text-white` : 'bg-white text-stone-700'">
                <component :is="paymentButton.icon" class="w-10 h-10" />
                <p class="font-medium text-center">{{ paymentButton.name }}</p>
            </div>
        </div>

        <!-- Payment Menus -->
        <TransitionGroup name="fade" tag="div" class="w-full grow-0 overflow-hidden relative">
            <component :is="paymentButtons[activeSection].component" class="w-full bg-white shadow-md rounded-lg p-6"
                :key="paymentButtons[activeSection].name" />
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

<style>
.el-overlay {
    z-index: 10000 !important;
}
</style>