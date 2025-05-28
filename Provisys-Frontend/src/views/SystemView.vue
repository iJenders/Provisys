<script setup>
import { LayoutDashboard, Truck, Apple, Package, User, ShieldUser, ScrollText } from 'lucide-vue-next';
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

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
    }
]

onMounted(() => {
    if (router.currentRoute.value.path === '/system') {
        router.push('/system/summary')
    }
})

</script>

<template>
    <div class="p-6 flex flex-col md:flex-row gap-6">
        <!-- System Links -->
        <aside class="bg-white rounded-lg shadow-md p-6 w-full md:w-fit h-fit pr-28 md:sticky top-[100px]">
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
        <RouterView class="bg-white rounded-lg shadow-md p-6 w-full" />
    </div>
</template>

<style scoped>
.router-link-active {
    color: var(--color-green-600);
}
</style>