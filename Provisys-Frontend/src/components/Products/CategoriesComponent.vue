<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import CategoryDetailsModal from './CategoryDetailsModal.vue';
import CategoryAddModal from './CategoryAddModal.vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const categories = ref([]);

const fetchingCategories = ref(false);
const addingCategory = ref(false);

const selectedCategory = ref(null);
const isSelectedCategory = ref(false);

const searchInput = ref('');
const onlyDisabled = ref(false);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: categories.value.length,
});

const tableRowClassName = ({ row }) => {
    if (row.disabled) {
        return '!bg-red-100';
    }
}

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchCategories();
    console.log(page);
}

const handleCategoryClick = (category) => {
    selectedCategory.value = category;
    isSelectedCategory.value = true;
}

const handleCloseDetailsModal = () => {
    isSelectedCategory.value = false;
    selectedCategory.value = null;
    fetchCategories();
}

const handleCloseAddModal = () => {
    addingCategory.value = false;
    fetchCategories();
}

const fetchCategories = () => {
    fetchingCategories.value = true;

    let data = {}

    if (searchInput.value) {
        data = {
            search: searchInput.value
        }
    }

    data.page = paginationConfig.value.page;
    data.onlyDisabled = onlyDisabled.value;

    axios.post(import.meta.env.VITE_API_URL + '/categories', data, {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    }).then(response => {
        categories.value = response.data.response.categories;
        paginationConfig.value.totalRows = parseInt(response.data.response.count);
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingCategories.value = false;
    });
}

onMounted(() => {
    fetchCategories();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Categories Content -->
        <h1 class="text-stone-700 font-medium">Categorías de Productos</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingCategories">
            <!-- Categories -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <el-input style="width: 100%;" placeholder="Buscar Categoría..." :prefix-icon="Search"
                        v-model="searchInput" />
                    <!-- See disabled check -->
                    <div>
                        <el-checkbox v-model="onlyDisabled" label="Solo deshabilitados" />
                    </div>
                    <!-- Apply search button -->
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchCategories">
                        <Search size="16" class="mr-1" />
                        Aplicar
                    </el-button>
                    <!--Add category button-->
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingCategory = true">
                        Añadir Categoría
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="categories" :row-class-name="tableRowClassName" border
                    style="width:100%" max-height="500" @row-click="(e) => { handleCategoryClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="disabled" label="Habilitado" min-width="100">
                        <template #default="scope">
                            <el-text :type="scope.row.disabled ? 'danger' : 'success'">
                                {{ scope.row.disabled ? 'No' : 'Si' }}
                            </el-text>
                        </template>
                    </el-table-column>
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