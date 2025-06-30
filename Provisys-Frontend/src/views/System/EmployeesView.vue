<template>
    <div>
        <!-- Management Menu -->
        <div class="mb-6">
            <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                <div v-for="(menu, index) in managementMenu" @click="activeSection = index" :key="index"
                    class="min-w-[160px] flex flex-col justify-center items-center rounded-lg p-4 shadow-md cursor-pointer transition"
                    :class="index == activeSection ? `bg-emerald-700 text-white` : 'bg-white text-stone-700'">
                    <component :is="menu.icon" class="w-10 h-10" />
                    <p class="font-medium text-center">{{ menu.name }}</p>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <TransitionGroup name="fade" tag="div" class="w-full relative overflow-hidden">
            <component :is="managementMenu[activeSection].component" class="w-full bg-white shadow-md rounded-lg p-6"
                :key="managementMenu[activeSection].name" />
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Users, Shield } from 'lucide-vue-next'
import EmployeesComponent from '@/components/Employees/EmployeesComponent.vue'
import RolesComponent from '@/components/Employees/RolesComponent.vue'

const managementMenu = [
    {
        name: 'Empleados',
        icon: Users,
        component: EmployeesComponent
    },
    {
        name: 'Roles',
        icon: Shield,
        component: RolesComponent
    }
]

const activeSection = ref(0)
</script>

<style scoped>
.fade-move,
.fade-enter-active,
.fade-leave-active {
    transition: all 0.2s cubic-bezier(0.55, 0, 0.1, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateX(20px);
    position: absolute;
}

.fade-enter-active{
    transition-delay: 0.2s !important;
}
</style>