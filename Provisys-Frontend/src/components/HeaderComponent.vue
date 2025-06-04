<script setup>
import { computed, ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { User, ShoppingCart, MonitorCog, LogOut, UserPlus, LogIn, X, Menu } from 'lucide-vue-next'
import { useShoppingCartStore } from '@/stores/shoppingCart.js'
import { ElMessageBox, ElNotification } from 'element-plus'
import { useAuthStore } from '@/stores/authStore'
import Line from './Line.vue'
import ThemeButton from './ThemeButton.vue'
import ShoppingCartItem from './ShoppingCartItem.vue'

const authStore = useAuthStore()

// Shopping Cart Test Data
let shoppingCart = ref([])

const headerLinks = [
    {
        name: 'Inicio',
        link: '/',
        userCondition: () => {
            return true;
        }
    },
    {
        name: 'Tienda',
        link: '/shop',
        userCondition: () => {
            return true;
        }
    },
    {
        name: 'Contacto',
        link: '/contact',
        userCondition: () => {
            return true;
        }
    }
]

const userOptions = [
    {
        name: 'Iniciar Sesión',
        icon: LogIn,
        link: '/login',
        userCondition: () => {
            return !authStore.isAuthenticated;
        }
    },
    {
        name: 'Registrarse',
        icon: UserPlus,
        link: '/register',
        userCondition: () => {
            return !authStore.isAuthenticated;
        }
    },
    {
        name: 'Mi Perfil',
        icon: User,
        link: '/profile',
        userCondition: () => {
            return authStore.isAuthenticated;
        }
    },
    {
        name: 'Sistema de Gestión',
        icon: MonitorCog,
        link: '/system',
        userCondition: () => {
            return authStore.isAuthenticated && authStore.checkPermission('manage_system');
        }
    },
]

const shoppingCartStore = useShoppingCartStore()

const showShoppingCart = ref(false)
const showUserMenu = ref(false)

// For responsive design, we will toggle the links menu
const showLinksMenu = ref(true)

const getSubTotal = computed(() => {
    let subtotal = 0
    shoppingCart.value.forEach(item => {
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

const toggleLinksMenu = () => {
    showLinksMenu.value = !showLinksMenu.value
    showUserMenu.value = false
    showShoppingCart.value = false
}

const handleLogout = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas cerrar sesión?', 'Confirmación', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
    }).then(() => {
        authStore.logout()
        showUserMenu.value = false
        showShoppingCart.value = false

        ElNotification({
            title: 'Éxito',
            message: 'Has cerrado sesión correctamente.',
            type: 'success',
            duration: 3000,
            offset: 80,
            zIndex: 10000
        })
    }).catch(() => {
        // El usuario canceló la acción
    })
}

onMounted(() => {
    shoppingCart.value = shoppingCartStore.products
    if (window.innerWidth < 768) {
        showLinksMenu.value = false
    }
})

</script>

<template>
    <!-- Header Component -->
    <header
        class="fixed flex flex-row items-center justify-between bg-emerald-700 text-white w-full h-[80px] px-10 z-10000">
        <!-- Responsive Menu Toggle -->
        <div class="md:hidden flex items-center justify-center mr-4 cursor-pointer">
            <button @click="toggleLinksMenu" class="text-white">
                <Menu size="40" />
            </button>
        </div>

        <!-- Header Logo-->
        <section class="HeaderIndex">
            <h1 class="text-2xl font-bold text-3xl">
                Codalca
            </h1>
        </section>

        <!-- Header Links -->
        <Transition>
            <section
                class="HeaderLinks flex flex-col md:items-center md:justify-center fixed left-0 top-[80px] md:w-full bg-emerald-700 z-10000 overflow-hidden rounded-b-xl px-8 pb-8 md:relative md:left-auto md:top-auto md:w-auto md:h-auto md:bg-transparent md:px-0 md:pb-0"
                v-show="showLinksMenu">
                <ul class="flex flex-col md:flex-row md:items-center md:justify-center h-full gap-4 md:gap-0">
                    <li v-for="link in headerLinks" class="mx-4">
                        <RouterLink v-if="link.userCondition()" :to="link.link"
                            class="text-white font-semibold hover:text-green-500 transition">
                            {{ link.name }}
                        </RouterLink>
                    </li>
                </ul>
            </section>
        </Transition>

        <!-- Header Options -->
        <section class="HeaderOptions flex flex-row items-center justify-center gap-4">
            <a href="" class="text-white font-semibold hover:text-green-500 transition"
                @click="(e) => { e.preventDefault(); toggleUserMenu() }">
                <User size="32" />
            </a>
            <a v-if="!authStore.isAuthenticated || authStore.user.roleId == 2" href=""
                class="text-white font-semibold hover:text-green-500 transition"
                @click="(e) => { e.preventDefault(); toggleShoppingCart() }">
                <ShoppingCart size="32" />
            </a>
        </section>
    </header>

    <!-- Shopping Cart Tooltip -->
    <Transition>
        <aside v-if="showShoppingCart"
            class="fixed right-0 top-[76px] shadow-[0_0_20px_rgba(0,0,0,0.25)] bg-white w-full md:w-[420px] p-8 rounded-2xl z-9999">
            <div class="flex flex-col gap-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-black text-shadow-lg text-shadow-stone-300">Carrito de Compras
                    </h2>
                    <ThemeButton
                        class="border-2 border-red-400 text-red-400 hover:bg-red-400 hover:text-white rounded-full"
                        @click="(e) => { e.preventDefault(); showShoppingCart = false }">
                        <X size="24" />
                    </ThemeButton>
                </div>
                <Line class="bg-stone-200" orientation="horizontal" />
                <div v-if="shoppingCart.length > 0" class="flex flex-col gap-6">
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
                <div v-else class="flex flex-col gap-6">
                    <blockquote data-v-d4948df9=""
                        class="p-4 border-l-4 border-red-400 bg-stone-100 italic text-gray-700">
                        No tienes productos en tu carrito de compras. Añade productos a tu carrito de compras para
                        verlos aquí.
                    </blockquote>
                    <ThemeButton
                        class="border-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white rounded-full"
                        routerLink="/shop" @click="(e) => { e.preventDefault(); showShoppingCart = false }">
                        Ir a la Tienda
                    </ThemeButton>
                </div>
            </div>
        </aside>
    </Transition>

    <!-- User Menu Tooltip -->
    <Transition>
        <aside v-show="showUserMenu"
            class="fixed right-0 top-[76px] shadow-[0_0_20px_rgba(0,0,0,0.25)] bg-white w-full md:w-[420px] p-8 rounded-2xl z-9999">
            <div class="flex flex-col gap-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-black text-shadow-lg text-shadow-stone-300">Opciones de Usuario
                    </h2>
                    <ThemeButton
                        class="border-2 border-red-400 text-red-400 hover:bg-red-400 hover:text-white rounded-full"
                        @click="(e) => { e.preventDefault(); showUserMenu = false }">
                        <X size="24" />
                    </ThemeButton>
                </div>
                <Line class="bg-stone-200" orientation="horizontal" />
                <ul class="flex flex-col max-h-[320px] overflow-y-auto px-4">
                    <li v-for="option in userOptions" :key="option.name">
                        <RouterLink :to="option.link" v-if="option.userCondition()"
                            class="flex items-center my-2 text-lg text-shadow-lg text-shadow-stone-200 transition linear duration-200"
                            :class="[option.twColor ? option.twColor : 'text-green-600']">
                            <component :is="option.icon" size="26" class="inline-block mr-3" />
                            <p class="text-lg font-normal">{{ option.name }}</p>
                        </RouterLink>
                    </li>
                    <!-- Logout -->
                    <li>
                        <RouterLink v-if="authStore.isAuthenticated" to="#"
                            class="flex items-center my-2 text-lg text-red-500 hover:text-red-700 transition"
                            @click="handleLogout">
                            <LogOut size="26" class="inline-block mr-3" />
                            <p class="text-lg font-normal">Cerrar Sesión</p>
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