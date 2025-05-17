<script setup>
import ThemeButton from '@/components/ThemeButton.vue'
import ProductComponent from '@/components/ProductComponent.vue'

let productsExample = [
    {
        id: 1,
        name: 'Producto 1',
        price: 10.99,
        stock: 5,
        description: 'Descripción del producto 1.',
        image: 'https://images.freeimages.com/slides/4f53a54c6e114a76a99b9df9573a8259.webp',
provider: "Parmalat"
    },
    {
        id: 2,
        name: 'Producto 2',
        price: 20.99,
        stock: 0,
        description: 'Descripción del producto 2.',
        image: 'https://images.freeimages.com/slides/4f53a54c6e114a76a99b9df9573a8259.webp',
provider: "Parmalat"
    },
    {
        id: 3,
        name: 'Producto 3',
        price: 15.99,
        stock: 10,
        description: 'Descripción del producto 3.',
        image: 'https://images.freeimages.com/slides/4f53a54c6e114a76a99b9df9573a8259.webp',
provider: "Parmalat"
    },
    {
        id: 4,
        name: 'Producto 4',
        price: 5.99,
        stock: 0,
        description: 'Descripción del producto 4.',
        image: 'https://images.freeimages.com/slides/4f53a54c6e114a76a99b9df9573a8259.webp',
provider: "Parmalat"
    }
]
</script>
<template>
    <!-- Main Banner -->
    <div class="flex flex-col items-center justify-center w-full h-[700px] relative">
        <div class="BannerImage flex items-center justify-center w-full h-full"></div>
        <div
            class="flex flex-col gap-4 absolute bg-green-100 rounded-3xl p-10 w-[640px] shadow-lg left-[calc(75%-320px)]">
            <p class="font-semibold text-stone-800">Tu aliado número uno en distribución de productos de consumo masivo
            </p>
            <h1 class="text-4xl font-bold text-green-700">Todo lo que Necesitas en un Solo Lugar</h1>
            <p class="font-semibold text-stone-800">
                CODALCA te ofrece una amplia gama de productos para tu empresa, con la eficiencia y el compromiso que
                nos
                caracterizan en la región.
            </p>
            <ThemeButton
                class="mt-5 px-10 py-4 w-max border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
                routerLink="/shop">
                Explora nuestros productos
            </ThemeButton>
        </div>
    </div>

    <!-- Products -->
    <div class="p-10 flex flex-col items-center">
        <h2 class="text-3xl font-bold text-stone-700">Nuestros Productos</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-5">
            <ProductComponent v-for="product in productsExample" :product="product" />
        </div>
        <ThemeButton class="mt-5 px-10 py-4 w-max border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
            routerLink="/shop">
            Explorar más...
        </ThemeButton>
    </div>
</template>

<style scoped>
.BannerImage {
    background-image: url('@/assets/images/banner.jpg');
    background-size: cover;
    background-position: center;
}
</style>