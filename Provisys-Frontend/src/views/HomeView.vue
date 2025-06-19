<script setup>
import ThemeButton from '@/components/ThemeButton.vue'
import ProductComponent from '@/components/ProductComponent.vue'
import { useMouse } from '@/composables/mouse.js'
import { onMounted, ref } from 'vue'
import { MapPin, ShoppingBag, Target, Award, Users, TrendingUp, Truck } from 'lucide-vue-next';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

let productsExample = ref([]);

const ratio = 0.05; // Cuanto mayor sea este valor, menos suave será la transición
const withTransitionMouseX = ref(null);
const withTransitionMouseY = ref(null);

const { getMouseRelativeX, getMouseRelativeY } = useMouse()

onMounted(() => {
    withTransitionMouseX.value = getMouseRelativeX.value;
    withTransitionMouseY.value = getMouseRelativeY.value;

    let frame = () => {
        requestAnimationFrame(frame);
        withTransitionMouseX.value = (getMouseRelativeX.value - withTransitionMouseX.value) * ratio + withTransitionMouseX.value;
        withTransitionMouseY.value = (getMouseRelativeY.value - withTransitionMouseY.value) * ratio + withTransitionMouseY.value;
    }
    frame();
})

const getProducts = () => {
    axios.post(import.meta.env.VITE_API_URL + '/products/shop', {
    }).then(response => {
        let mapped = response.data.response.products.map((product, index) => {
            return {
                id: product.id,
                name: product.nombre,
                price: parseFloat(product.precio),
                stock: parseInt(product.stock),
                description: product.descripcion_producto,
                image: import.meta.env.VITE_API_URL + '/products/image/?id=' + product.id,
                provider: product.fabricante
            }
        })

        let objects = (mapped.length <= 4 ? mapped.length : 4);
        for (let i = 0; i < objects; i++) {
            productsExample.value.push(mapped[i])
        }
    }).catch(error => {
        handleRequestError(error)
    })
}

onMounted(() => {
    getProducts()
})
</script>
<template>
    <!-- Main Banner -->
    <div class="flex flex-col items-center justify-center w-full h-[700px] relative overflow-hidden">
        <div class="BannerImage flex items-center justify-center w-full h-full relative"></div>
        <div
            class="flex flex-col gap-4 absolute bg-green-100 rounded-3xl p-10 max-w-[80vw] md:w-[640px] shadow-lg md:right-10">
            <p class="font-semibold text-stone-800">Tu aliado número uno en distribución de productos de consumo masivo
            </p>
            <h1 class="text-4xl font-bold text-green-700">Todo lo que Necesitas en un Solo Lugar</h1>
            <p class="font-semibold text-stone-800">
                Te ofrecemos una amplia gama de productos para tu empresa, con la eficiencia y el compromiso que
                nos
                caracterizan en la región.
            </p>
            <ThemeButton
                class="mt-5 p-4 md:px-10 md:w-max border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
                routerLink="/shop">
                Explora nuestro catálogo de productos
            </ThemeButton>
        </div>
    </div>

    <!-- Products -->
    <div class="p-10 flex flex-col items-center">
        <h2 class="text-3xl font-bold text-stone-700">Productos Destacados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 mt-5">
            <ProductComponent v-for="product in productsExample" :product="product" />
        </div>
        <ThemeButton class="mt-5 px-10 py-4 w-max border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
            routerLink="/shop">
            Explorar más...
        </ThemeButton>
    </div>

    <!-- Company Header -->
    <div class="CompanyHeader text-center">
        <div class=" mb-12 px-4 py-20 backdrop-blur-xs backdrop-brightness-50">
            <img src="@/assets/images/codalca.png" alt="Company Logo" class="w-50 mx-auto mb-4" />
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                Comercializadora De Alimentos
            </h1>
            <h2 class="text-xl md:text-2xl font-medium text-white">Provisys</h2>
        </div>
    </div>
    <div class="max-w-5xl mx-auto px-4 py-12 md:py-16">
        <!-- Company Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Location Card -->
            <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                <div class="flex items-start mb-4">
                    <div class="bg-emerald-100 p-3 rounded-full mr-4">
                        <MapPin class="h-6 w-6 text-emerald-600" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Ubicación</h3>
                        <p class="text-gray-600">
                            Avenida Intercomunal Barquisimeto-Acarigua, Sector el Troncal.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Activity Card -->
            <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
                <div class="flex items-start mb-4">
                    <div class="bg-emerald-100 p-3 rounded-full mr-4">
                        <ShoppingBag class="h-6 w-6 text-emerald-600" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Actividad Principal</h3>
                        <p class="text-gray-600">
                            Comercialización de alimentos para consumo masivo a empresas Venezolanas,
                            con especial enfoque en el estado Lara, precisamente en las ciudades de
                            Barquisimeto y Cabudare.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission -->
        <div class="bg-emerald-50 rounded-lg p-8 shadow-sm border border-emerald-100">
            <div class="flex items-start mb-6">
                <div class="bg-emerald-100 p-3 rounded-full mr-4">
                    <Target class="h-6 w-6 text-emerald-600" />
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Nuestra Misión</h3>
            </div>
            <blockquote class="pl-4 border-l-4 border-emerald-300 italic text-gray-700">
                "Distribuimos y comercializamos productos alimenticios de consumo masivo e higiene
                personal a empresas venezolanas con una clara visión de crecimiento y desarrollo.
                Gracias al compromiso de nuestro equipo, proveedores, aliados y clientes, garantizamos
                la presencia de marcas de alta calidad en los anaqueles, lo que genera presencia en el
                mercado y un crecimiento sostenido del negocio."
            </blockquote>
        </div>

        <!-- Company Values -->
        <div class="mt-12">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 text-center">Nuestros Valores</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div
                        class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
                        <Award class="h-6 w-6 text-emerald-600" />
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Calidad</h4>
                    <p class="text-sm text-gray-600">Productos de alta calidad para nuestros clientes</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div
                        class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
                        <Users class="h-6 w-6 text-emerald-600" />
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Compromiso</h4>
                    <p class="text-sm text-gray-600">Dedicación con nuestros clientes y proveedores</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div
                        class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
                        <TrendingUp class="h-6 w-6 text-emerald-600" />
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Crecimiento</h4>
                    <p class="text-sm text-gray-600">Visión clara de desarrollo y expansión</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div
                        class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
                        <Truck class="h-6 w-6 text-emerald-600" />
                    </div>
                    <h4 class="font-medium text-gray-800 mb-1">Distribución</h4>
                    <p class="text-sm text-gray-600">Presencia efectiva en el mercado venezolano</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.BannerImage {
    width: calc(100% + 50px);
    height: calc(100% + 50px);
    background-image: url('@/assets/images/banner.jpg');
    background-size: cover;
    top: calc(-25px + v-bind(withTransitionMouseY) * 25px);
    left: calc(-25px + v-bind(withTransitionMouseX) * 50px);
}

.CompanyHeader {
    background-image: url('@/assets/images/about-banner.jpg');
    background-size: cover;
    background-position: center;
}
</style>