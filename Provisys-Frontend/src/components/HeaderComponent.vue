<script setup>
import { computed, ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { User, ShoppingCart, MonitorCog, LogOut, UserPlus, LogIn } from 'lucide-vue-next'
import { useShoppingCartStore } from '@/stores/shoppingCart.js'
import Line from './Line.vue'
import ThemeButton from './ThemeButton.vue'
import ShoppingCartItem from './ShoppingCartItem.vue'

// Shopping Cart Test Data
let shoppingCart = []

const headerLinks = [
    {
        name: 'Inicio',
        link: '/'
    },
    {
        name: 'Tienda',
        link: '/shop'
    },
    {
        name: 'Nosotros',
        link: '/about'
    },
    {
        name: 'Contacto',
        link: '/contact'
    }
]

const userOptions = [
    {
        name: 'Registrarse',
        icon: UserPlus,
        link: '/register'
    },
    {
        name: 'Iniciar Sesión',
        icon: LogIn,
        link: '/login'
    },
    {
        name: 'Mi Perfil',
        icon: User,
        link: '/profile'
    },
    {
        name: 'Sistema de Gestión',
        icon: MonitorCog,
        link: '/system'
    },
    {
        name: 'Cerrar Sesión',
        icon: LogOut,
        link: '/logout',
        twColor: 'text-red-500'
    },
]

const shoppingCartStore = useShoppingCartStore()

const showShoppingCart = ref(false)
const showUserMenu = ref(false)

const getSubTotal = computed(() => {
    let subtotal = 0
    shoppingCart.forEach(item => {
        subtotal += item.product.price * item.quantity
    })
    return subtotal.toFixed(2)
})

const toggleShoppingCart = () => {
    showShoppingCart.value = !showShoppingCart.value
    showUserMenu.value = false
}

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value
    showShoppingCart.value = false
}

onMounted(() => {
    shoppingCart = shoppingCartStore.products
})
</script>

<template>
    <!-- Header Component -->
    <header
        class="fixed flex flex-row items-center justify-between bg-emerald-700 text-white w-full h-[80px] px-[75px] z-1000">
        <!-- Header Logo-->
        <section class="HeaderIndex">
            <h1 class="text-2xl font-bold text-3xl">
                Codalca
            </h1>
        </section>

        <!-- Header Links -->
        <section class="HeaderLinks flex flex-row items-center justify-center">
            <ul class="flex flex-row items-center justify-center">
                <li v-for="link in headerLinks" class="mx-4">
                    <RouterLink :to="link.link" class="text-white font-semibold hover:text-green-500 transition">
                        {{ link.name }}
                    </RouterLink>
                </li>
            </ul>
        </section>

        <!-- Header Options -->
        <section class="HeaderOptions flex flex-row items-center justify-center gap-4">
            <a href="" class="text-white font-semibold hover:text-green-500 transition"
                @click="(e) => { e.preventDefault(); toggleUserMenu() }">
                <User size="32" />
            </a>
            <a href="" class="text-white font-semibold hover:text-green-500 transition"
                @click="(e) => { e.preventDefault(); toggleShoppingCart() }">
                <ShoppingCart size="32" />
            </a>
        </section>
    </header>

    <!-- Shopping Cart Tooltip -->
    <Transition>
        <aside v-if="showShoppingCart"
            class="fixed right-0 top-[76px] shadow-[0_0_20px_rgba(0,0,0,0.25)] bg-white w-[420px] p-8 rounded-2xl z-999">
            <div class="flex flex-col gap-6">
                <h2 class="text-2xl font-bold text-black text-shadow-lg text-shadow-stone-300">Carrito de Compras</h2>
                <Line class="bg-stone-200" orientation="horizontal" />
                <ul class="flex flex-col gap-6 max-h-[320px] overflow-y-auto">
                    <li v-for="item in shoppingCart">
                        <ShoppingCartItem :item="item"></ShoppingCartItem>
                    </li>
                </ul>
                <div class="flex justify-between items-center">
                    <p class="text-lg text-black text-shadow-lg text-shadow-stone-300">Subtotal:</p>
                    <p class="text-lg text-black text-shadow-lg text-green-600">${{ getSubTotal }}</p>
                </div>
                <Line class="bg-stone-200" orientation="horizontal" />
                <div class="flex justify-end items-center">
                    <ThemeButton
                        class="border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white rounded-full">
                        Proceder al Pago
                    </ThemeButton>
                </div>
            </div>
        </aside>
    </Transition>

    <!-- User Menu Tooltip -->
    <Transition>
        <aside v-show="showUserMenu"
            class="fixed right-0 top-[76px] shadow-[0_0_20px_rgba(0,0,0,0.25)] bg-white w-[420px] p-8 rounded-2xl z-999">
            <div class="flex flex-col gap-6">
                <h2 class="text-2xl font-bold text-black text-shadow-lg text-shadow-stone-300">Opciones de Usuario</h2>
                <Line class="bg-stone-200" orientation="horizontal" />
                <ul class="flex flex-col gap-4 max-h-[320px] overflow-y-auto px-4">
                    <li v-for="option in userOptions" :key="option.name">
                        <RouterLink :to="option.link"
                            class="flex items-center text-lg text-shadow-lg text-shadow-stone-200 transition linear duration-200"
                            :class="[option.twColor ? option.twColor : 'text-green-600']">
                            <component :is="option.icon" size="26" class="inline-block mr-3" />
                            <p class="text-lg font-normal">{{ option.name }}</p>
                        </RouterLink>
                    </li>
                </ul>
            </div>
        </aside>
    </Transition>
</template>

<style scoped>
aside {
    interpolate-size: allow-keywords;
    overflow: hidden;
}

.v-enter-active,
.v-leave-active {
    transition: 0.15s ease;
}

.v-enter-from,
.v-leave-to {
    height: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.v-enter-to,
.v-leave-from {
    height: calc-size(auto);
}
</style>