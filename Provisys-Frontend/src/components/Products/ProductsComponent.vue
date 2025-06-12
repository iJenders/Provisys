<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import ThemeButton from '../ThemeButton.vue';
import { onMounted, ref } from 'vue';
import ProductDetailsModal from './ProductDetailsModal.vue';
import ProductAddModal from './ProductAddModal.vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const fetchingProducts = ref(false);

const productsFilter = ref({
    searcher: '',
    category: '',
    provider: '',
    price: {
        min: null,
        max: null
    },
    showDeleted: false
});

const products = ref([]);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: products.value.length,
});

const selectedProduct = ref(null);
const isSelectedProduct = ref(selectedProduct.value !== null)
const addingProduct = ref(false);

const fetchingProviders = ref(false);
const fetchingCategories = ref(false);

const providers = ref([]);
const categories = ref([]);

const handleProductClick = (product) => {
    selectedProduct.value = { ...product }
    selectedProduct.value.image =
        import.meta.env.VITE_API_URL + '/products/image/?id=' + product.id;
    isSelectedProduct.value = true;
}

const handleCloseDetailsModal = () => {
    selectedProduct.value = null;
    isSelectedProduct.value = false;
    fetchProducts();
}

const handleCloseAddingModal = () => {
    addingProduct.value = false;
    fetchProducts();
}

const fetchProducts = () => {
    fetchingProducts.value = true;

    let data = {
        search: productsFilter.value.searcher || '',
        filters: {
            deleted: 0
        },
        range: {},
        offset: paginationConfig.value.page,
    }

    // Mostrar eliminados
    if (productsFilter.value.showDeleted) {
        delete data.filters.deleted;
    }

    // Filtrar por categoría
    if (productsFilter.value.category != '') {
        data.filters.categoria = productsFilter.value.category.id;
    }

    // Filtrar por proveedor
    if (productsFilter.value.provider != '') {
        data.filters.fabricante = productsFilter.value.provider.id;
    }

    // Filtrar por precio mínimo
    if (productsFilter.value.price.min != null) {
        data.range.actualPrice = Object.assign(
            data.range.actualPrice || {},
            {
                min: productsFilter.value.price.min
            }
        );
    }

    // Filtrar por precio máximo
    if (productsFilter.value.price.max != null) {
        data.range.actualPrice = Object.assign(
            data.range.actualPrice || {},
            {
                max: productsFilter.value.price.max
            }
        );
    }

    axios.post(import.meta.env.VITE_API_URL + '/products', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        let fetched = response.data.response;

        fetched.products.forEach(product => {
            product.actualPrice = parseFloat(product.actualPrice);
        });

        paginationConfig.value.totalRows = fetched.count;

        products.value = fetched.products;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProducts.value = false;
    })
}

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchProducts();
}

const tableRowClassName = (row) => {
    if (row.row.deleted == 1) {
        return '!bg-red-100';
    }
}

const handleProviderSearch = (value) => {
    // Implementar lógica para obtener proveedores basados en la búsqueda
    fetchingProviders.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/manufacturers', {
        search: value == '' ? null : value,
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        providers.value = response.data.response.manufacturers;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProviders.value = false;
    });
}

const handleCategorySearch = (value) => {
    // Implementar lógica para obtener categorías basadas en la búsqueda
    fetchingCategories.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/categories', {
        search: value == '' ? null : value
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        categories.value = response.data.response.categories;
    }).catch(error => {

    }).finally(() => {
        fetchingCategories.value = false;
    });

}

const resetFilters = () => {
    productsFilter.value = {
        searcher: '',
        category: '',
        provider: '',
        price: {
            min: null,
            max: null
        },
        showDeleted: false
    }
    fetchProducts();
}

onMounted(() => {
    fetchProducts();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Products Content -->
        <h1 class="text-stone-700 font-medium">Listado de Productos</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col lg:flex-row gap-4" v-loading="fetchingProducts">
            <!-- Filters -->
            <div class="w-full lg:w-[250px] shrink-0 flex flex-col gap-3 lg:border-stone-200 lg:border-r-[2px] lg:pr-4">
                <p class="text-xl font-medium text-stone-700">Filtrar</p>
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-1 gap-x-6 gap-y-2">
                    <!-- Category Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Categoría:</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <el-select v-model="productsFilter.category" value-key="id" class="w-full"
                                    placeholder="Seleccionar Categoría" :loading="fetchingProviders" filterable remote
                                    :remote-method="handleCategorySearch">
                                    <el-option v-for="category in categories" :key="category.id" :label="category.name"
                                        :value="category" />
                                </el-select>
                            </div>
                        </div>
                    </div>

                    <!-- Provider Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Proveedor:</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <el-select v-model="productsFilter.provider" value-key="id" class="w-full"
                                    placeholder="Seleccionar Proveedor" :loading="fetchingProviders" filterable remote
                                    :remote-method="handleProviderSearch">
                                    <el-option v-for="provider in providers" :key="provider.id" :label="provider.name"
                                        :value="provider" />
                                </el-select>
                            </div>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Precio</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Min:</p>
                                <el-input-number v-model="productsFilter.price.min" controls-position="right"
                                    style="width: 100%;" :precision="2" :min="0.01" :max="9999999999.99">
                                    <template #prefix>
                                        <span>$</span>
                                    </template>
                                </el-input-number>
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Max:</p>
                                <el-input-number v-model="productsFilter.price.max" controls-position="right"
                                    style="width: 100%;" :precision="2" :min="0.01" :max="9999999999.99">
                                    <template #prefix>
                                        <span>$</span>
                                    </template>
                                </el-input-number>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Buttons -->
                <div class=" w-full flex flex-col gap-1">
                    <div class="w-full flex flex-col justify-center gap-2">
                        <ThemeButton
                            class="w-full rounded-full text-sm !py-1 border-green-600 text-green-600 hover:bg-green-600 hover:text-white"
                            @click="fetchProducts">
                            Aplicar Filtros
                        </ThemeButton>
                        <ThemeButton
                            class="w-full rounded-full text-sm !py-1 border-stone-700 text-stone-700 hover:bg-stone-700 hover:text-white"
                            @click="resetFilters">
                            Limpiar
                        </ThemeButton>
                    </div>
                </div>
            </div>

            <Line orientation="horizontal" class="lg:hidden bg-stone-200" />

            <!-- Products -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-2 flex-wrap">
                    <el-input style="width: 100%;" v-model="productsFilter.searcher" placeholder="Buscar Producto..."
                        :prefix-icon="Search" />

                    <!-- Show Deleted Check -->
                    <el-checkbox v-model="productsFilter.showDeleted" label="Mostrar eliminados" />

                    <!-- Apply search button -->
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchProducts">
                        <Search :size="18" class="mr-2" />
                        Buscar
                    </el-button>
                    <!--Add product button-->
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingProduct = true">
                        Añadir Producto
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="products" border style="width:100%" max-height="500"
                    @row-click="(e) => { handleProductClick(e) }" :row-class-name="tableRowClassName">
                    <el-table-column prop="id" label="Id" min-width="200" />
                    <el-table-column prop="name" label="Nombre" width="120" fixed />
                    <el-table-column prop="deleted" label="Habilitado" min-width="140">
                        <template #default="scope">
                            <el-text :type="scope.row.deleted ? 'danger' : 'success'">
                                {{ scope.row.deleted ? 'No' : 'Si' }}
                            </el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="description" label="Descripción" min-width="240" />
                    <el-table-column prop="actualPrice" label="Precio Actual" min-width="100">
                        <template #default="scope">
                            ${{ parseInt(scope.row.actualPrice).toFixed(2) }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="actualIva" label="IVA actual" min-width="100">
                        <template #default="scope">
                            {{ parseInt(scope.row.actualIva.value).toFixed(0) }}%
                        </template>
                    </el-table-column>
                    <el-table-column prop="description" label="Descripción" min-width="240" />
                    <el-table-column prop="categoria.name" label="Categoría" min-width="160" />
                    <el-table-column prop="fabricante.name" label="Proveedor" min-width="180" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingProducts" @change="handlePageChange" />
                </div>

            </div>
        </div>

        <!-- Product Details Modal -->
        <ProductDetailsModal v-model="isSelectedProduct" :selectedProduct="selectedProduct"
            @closeModal="handleCloseDetailsModal" />

        <!-- New Product Modal -->
        <ProductAddModal v-model="addingProduct" @closeModal="handleCloseAddingModal" />
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