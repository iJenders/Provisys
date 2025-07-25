<script setup>
import { LayoutDashboard, Truck, Apple, Package, User, ShieldUser, ScrollText, Receipt, PackagePlus } from 'lucide-vue-next';
import { onBeforeMount, onMounted, onUpdated } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const authStore = useAuthStore();

const router = useRouter();

const systemLinks = [
    {
        name: 'Resumen',
        icon: LayoutDashboard,
        link: '/system/summary'
    },
    {
        name: 'Pedidos',
        icon: Truck,
        link: '/system/orders'
    },
    {
        name: 'Compras',
        icon: PackagePlus,
        link: '/system/purchases'
    },
    {
        name: 'Pagos',
        icon: Receipt,
        link: '/system/payments'
    },
    {
        name: 'Productos',
        icon: Apple,
        link: '/system/products'
    },
    {
        name: 'Inventario',
        icon: Package,
        link: '/system/inventory'
    },
    {
        name: 'Clientes',
        icon: User,
        link: '/system/customers'
    },
    {
        name: 'Empleados',
        icon: ShieldUser,
        link: '/system/employees'
    },
    {
        name: 'Reportes',
        icon: ScrollText,
        link: '/system/reports'
    },
]

onBeforeMount(() => {
    // Si no se tiene sesión iniciada, redirigir al login

    if (router.currentRoute.value.path.includes('/system')) {
        if (authStore.token === '' || authStore.token === null || !authStore.user || authStore.user.roleId == 2) {
            router.push('/login');
        }
    }
})

onMounted(() => {
    if (router.currentRoute.value.path === '/system' || router.currentRoute.value.path === '/system/') {
        // Redirigir a la ruta de resumen si se accede a /system directamente
        router.push('/system/summary')
    }
})

onUpdated(() => {
    if (router.currentRoute.value.path === '/system' || router.currentRoute.value.path === '/system/') {
        // Redirigir a la ruta de resumen si se accede a /system directamente
        router.push('/system/summary')
    }
})

</script>

<template>
    <div class="w-full p-6 flex flex-col md:flex-row gap-6">
        <!-- System Links -->
        <aside
            class="bg-white rounded-lg shadow-md p-6 w-full md:w-fit h-fit pr-28 md:sticky top-[100px] flex-shrink-0">
            <ul class="flex flex-col gap-4">
                <li v-for="link in systemLinks" :key="link.name"
                    class="flex text-stone-800 font-medium items-center gap-4 hover:text-green-600 transition linear">
                    <RouterLink :to="link.link" class="tailwind">
                        <div class="flex items-center gap-2">
                            <component :is="link.icon" size="20" />
                            <span>{{ link.name }}</span>
                        </div>
                    </RouterLink>
                </li>
            </ul>
        </aside>

        <!-- System Content -->
        <TransitionGroup name="system-category-fade" tag="div" class="w-full flex flex-col min-w-0 grow-0 relative">
            <RouterView :key="router.currentRoute.value.path" />
        </TransitionGroup>
    </div>
</template>

<style scoped>
.router-link-active {
    color: var(--color-green-600);
}

.system-category-fade-move,
.system-category-fade-enter-active,
.system-category-fade-leave-active {
    transition: all 0.2s cubic-bezier(0.55, 0, 0.1, 1);
}

.system-category-fade-enter-active {
    transition-delay: 0.2s;
}

.system-category-fade-enter-from,
.system-category-fade-leave-to {
    opacity: 0;
    transform: translate(0, 30px);
}

.system-category-fade-leave-active {
    width: 100%;
    position: absolute;
}
</style>