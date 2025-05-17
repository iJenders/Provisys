<script setup>
import ThemeButton from '@/components/ThemeButton.vue'

import { ref, computed } from 'vue'
import { ElInput, ElSelect, ElOption, ElTooltip, ElDialog } from 'element-plus'
import { SlidersHorizontal, ArrowUpNarrowWide, ArrowDownNarrowWide } from 'lucide-vue-next'
import Line from '@/components/Line.vue'
import ProductComponent from '@/components/ProductComponent.vue'

const orderOptions = [
    {
        value: 'name',
        label: 'Nombre',
    },
    {
        value: 'price',
        label: 'Precio',
    },
    {
        value: 'category',
        label: 'Categoría',
    },
    {
        value: 'stock',
        label: 'Stock',
    },
]

let productsExample = ref([
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
    }, {
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
    }, {
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
    }, {
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
])

let paginationInfo = ref({
    currentPage: 1,
    totalPages: 10,
    productsPerPage: 16,
    totalProducts: 100
})

const getCurrentShowingProducts = computed(() => {
    let firstOffset = (paginationInfo.value.currentPage - 1) * paginationInfo.value.productsPerPage
    let lastOffset = firstOffset + paginationInfo.value.productsPerPage
    return `${firstOffset + 1} - ${lastOffset > paginationInfo.value.totalProducts ? paginationInfo.value.totalProducts : lastOffset}`
})

const orderOptionSelected = ref(null);
const orderOptionsAsc = ref(true);
</script>
<template>
    <!-- Main Banner -->
    <section class="flex flex-col items-center justify-center w-full h-[350px] relative">
        <div class="BannerImage flex flex-col items-center justify-center w-full h-full"></div>
        <div class="flex flex-col items-center justify-center w-full h-full absolute">
            <h1 class="text-5xl font-bold text-stone-700">Tienda Online</h1>
            <p class="text-lg font-bold text-stone-500">Encuentra los mejores productos para tu negocio</p>
        </div>
    </section>

    <!-- Products Filter Panel -->
    <div class="flex items-center justify-between w-full bg-green-100/50 p-6 md:px-20">
        <div class="flex align-center gap-4 h-[32px]">
            <a href="javascript:"
                class="flex items-center justify-center gap-2 text-lg font-bold text-stone-700 hover:text-green-600 transition linear duration-160">
                <SlidersHorizontal size="24" /> Filtrar
            </a>
            <Line class="bg-stone-400" orientation="vertical" />
            <p class="text-lg flex items-center font-bold text-stone-700">
                Mostrando {{ getCurrentShowingProducts }} de {{ paginationInfo.totalProducts }} productos
            </p>
        </div>
        <div class="flex align-center gap-4 h-[32px]">
            <p class=" text-lg font-bold text-stone-700">
                Ordenar por:
            </p>
            <div class="w-[160px]">
                <ElSelect v-model="orderOptionSelected" class="text-stone-700" placeholder="Seleccionar">
                    <ElOption v-for="option in orderOptions" :label="option.label" :value="option.value" />
                </ElSelect>
            </div>
            <Line class="bg-stone-400" orientation="vertical" />
            <div class="text-lg flex items-center font-bold text-stone-700">
                <el-tooltip effect="dark" content="Ascendente" placement="top">
                    <a href="javascript:" class="flex items-center justify-center gap-2 text-lg font-bold"
                        :class="[orderOptionsAsc ? 'text-green-600' : 'text-stone-700']"
                        @click="orderOptionsAsc = true">
                        <ArrowUpNarrowWide size="24" />
                    </a>
                </el-tooltip>
                <el-tooltip effect="dark" content="Descendente" placement="top">
                    <a href="javascript:" class="flex items-center justify-center gap-2 text-lg font-bold"
                        :class="[!orderOptionsAsc ? 'text-green-600' : 'text-stone-700']"
                        @click="orderOptionsAsc = false">
                        <ArrowDownNarrowWide size="24" />
                    </a>
                </el-tooltip>
            </div>
        </div>
    </div>

    <!-- Products List -->
    <section class="flex flex-col items-center justify-center w-full p-6 md:px-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <ProductComponent v-for="product in productsExample" :key="product.id" :product="product" />
        </div>
    </section>

    <!-- Pagination -->
    <section class="flex items-center justify-center w-full p-6 md:px-20">
        <div class="flex items-center justify-center gap-4">
            <ThemeButton class="border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
                :class="[paginationInfo.currentPage <= 1 ? 'saturate-0' : '']"
                :enabled="paginationInfo.currentPage > 1">
                Anterior
            </ThemeButton>
            <div class="text-green-600 font-medium">
                Página 1 de 10
            </div>
            <ThemeButton class="border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
                :class="[paginationInfo.currentPage >= paginationInfo.totalPages ? 'saturate-0' : '']"
                :enabled="paginationInfo.currentPage < paginationInfo.totalPages">
                Siguiente
            </ThemeButton>
        </div>
    </section>
</template>

<style scoped>
.BannerImage {
    background-image: url('@/assets/images/shopBanner.jpg');
    background-size: cover;
    background-position: center;
    filter: blur(2px);
}
</style>