<script setup>
import ThemeButton from '@/components/ThemeButton.vue'

import { ref, computed, onMounted } from 'vue'
import { ElInput, ElSelect, ElOption, ElTooltip, ElDialog, ElPagination } from 'element-plus'
import { SlidersHorizontal, ArrowUpNarrowWide, ArrowDownNarrowWide } from 'lucide-vue-next'
import Line from '@/components/Line.vue'
import ProductComponent from '@/components/ProductComponent.vue'
import axios from 'axios'

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

let productsExample = ref([])

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: 0
});

const orderOptionSelected = ref(null);
const orderOptionsAsc = ref(true);

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    getProducts();
}

const getProducts = () => {
    axios.post(import.meta.env.VITE_API_URL + '/products/shop', {
        page: paginationConfig.value.page,
        orderBy: orderOptionSelected.value,
        order: orderOptionsAsc.value ? 'ASC' : 'DESC'
    }).then(response => {
        productsExample.value = response.data.response.products.map(product => {
            return {
                id: product.id,
                name: product.nombre,
                price: parseFloat(product.precio),
                stock: parseInt(product.stock),
                description: product.descripcion_producto,
                image: import.meta.env.VITE_API_URL + '/products/image/?id=' + product.id,
                provider: product.fabricante
            }
        });
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        console.log(error)
    })
}

onMounted(() => {
    getProducts();
})
</script>

<template>
    <!-- Main Banner -->
    <section class="flex flex-col items-center justify-center w-full h-[350px] relative">
        <div class="BannerImage flex flex-col items-center justify-center w-full h-full"></div>
        <div class="flex flex-col items-center justify-center w-full h-full absolute px-4">
            <h1 class="text-5xl font-bold text-stone-700 text-center">Tienda Online</h1>
            <p class="text-lg font-bold text-stone-500 text-center">Encuentra los mejores productos para tu negocio</p>
        </div>
    </section>

    <!-- Products Filter Panel -->
    <div
        class="flex flex-col gap-6 md:flex-row md:gap-0 items-center justify-between w-full bg-green-100/50 p-6 md:px-20">
        <div class="flex align-center gap-4 min-h-[32px]">
            <a href="javascript:"
                class="flex items-center justify-center gap-2 text-lg font-bold text-stone-700 hover:text-green-600 transition linear duration-160">
                <SlidersHorizontal size="24" /> Filtrar
            </a>
            <Line class="bg-stone-400" orientation="vertical" />
            <p class="text-lg flex items-center font-bold text-stone-700">
                Total: {{ paginationConfig.totalRows }} productos
            </p>
        </div>

        <!-- Order Options -->
        <div class="flex align-center gap-4 min-h-[32px]">
            <p class=" text-lg font-bold text-stone-700">
                Ordenar por:
            </p>
            <div class="w-[160px] flex items-center">
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

    <!-- Products Wrapper -->
    <section class="flex flex-col items-center justify-center w-full p-6 md:px-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <ProductComponent v-for="product in productsExample" :key="product.id" :product="product" />
        </div>
    </section>

    <!-- Pagination -->
    <section class="flex flex-col items-center justify-center w-full p-6 md:px-20 gap-2">
        <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
            :total="paginationConfig.totalRows" @change="handlePageChange" />
        <p class="text-lg font-medium text-stone-700">
            Mostrando {{ paginationConfig.rowsPerPage }} de {{ paginationConfig.totalRows }} productos
        </p>
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