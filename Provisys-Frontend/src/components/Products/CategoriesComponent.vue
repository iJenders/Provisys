<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { ref } from 'vue';
import CategoryDetailsModal from './CategoryDetailsModal.vue';
import CategoryAddModal from './CategoryAddModal.vue';

const categories = ref([
    { id: 1, name: 'Electrónica', description: 'Dispositivos electrónicos y accesorios', currentPrice: 299.99, category: { name: 'Tecnología' }, provider: { name: 'Proveedor A' } },
    { id: 2, name: 'Ropa', description: 'Vestimenta y accesorios de moda', currentPrice: 49.99, category: { name: 'Moda' }, provider: { name: 'Proveedor B' } },
    { id: 3, name: 'Hogar', description: 'Artículos para el hogar y decoración', currentPrice: 89.99, category: { name: 'Hogar' }, provider: { name: 'Proveedor C' } },
]);

const fetchingCategories = ref(false);
const addingCategory = ref(false);

const selectedCategory = ref(null);
const isSelectedCategory = ref(false);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 1,
    totalRows: categories.value.length,
});

const handlePageChange = (page) => {
    fetchingCategories.value = true;
    setTimeout(() => {
        fetchingCategories.value = false;
    }, 1000);
}

const handleCategoryClick = (category) => {
    selectedCategory.value = category;
    isSelectedCategory.value = true;
}

const handleCloseDetailsModal = () => {
    isSelectedCategory.value = false;
    selectedCategory.value = null;
}

const handleCloseAddModal = () => {
    addingCategory.value = false;
}
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Categories Content -->
        <h1 class="text-stone-700 font-medium">Categorías de Productos</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingCategories">
            <!-- Products -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex items-center gap-4">
                    <el-input style="width: 100%;" placeholder="Buscar Categoría..." :prefix-icon="Search" />
                    <!--Add category button-->
                    <el-button class="!rounded-full !py-1 !px-2" type="success" @click="addingCategory = true">
                        Añadir Categoría
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="categories" stripe border style="width:100%" max-height="500"
                    @row-click="(e) => { handleCategoryClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="name" label="Nombre" min-width="120" />
                    <el-table-column prop="description" label="Descripción" min-width="240" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingCategories" @change="handlePageChange" />
                </div>

            </div>
        </div>

        <!-- Category Details Modal -->
        <CategoryDetailsModal v-model="isSelectedCategory" :selectedCategory="selectedCategory"
            :isSelectedCategory="isSelectedCategory" @close-modal="handleCloseDetailsModal" />

        <!-- New Category Modal -->
        <CategoryAddModal v-model="addingCategory" :adding-category="addingCategory"
            @close-modal="handleCloseAddModal" />
    </div>
</template>

<style>
.pointer-rows .el-table__row {
    cursor: pointer;
}

.el-overlay {
    z-index: 10000 !important;
}

.el-select__popper {
    z-index: 10000 !important;
}
</style>