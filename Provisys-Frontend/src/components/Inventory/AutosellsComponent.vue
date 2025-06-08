<script setup>
import { Search, ShoppingCart, Plus, Settings } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const autosells = ref([]);

const fetchingAutosells = ref(false);
const addingAutosell = ref(false);

const selectedAutosell = ref({
    id: null,
    product_id: null,
    product_name: '',
    min_stock: 0,
    max_stock: 0,
    auto_order_quantity: 0,
    supplier_id: null,
    supplier_name: '',
    is_active: true,
});
const isSelectedAutosell = ref(false);

const searchInput = ref('');
const onlyActive = ref(true);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: autosells.value.length,
});

const tableRowClassName = ({ row }) => {
    if (!row.is_active) {
        return '!bg-gray-100';
    }
}

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchAutosells();
}

const handleAutosellClick = (autosell) => {
    selectedAutosell.value = autosell;
    isSelectedAutosell.value = true;
}

const getStatusTag = (isActive) => {
    return isActive ? { label: 'Activo', type: 'success' } : { label: 'Inactivo', type: 'danger' };
}

const fetchAutosells = () => {
    fetchingAutosells.value = true;

    let data = {
        filters: {}
    }

    if (searchInput.value) {
        data.search = searchInput.value
    }
    data.offset = paginationConfig.value.page;

    if (onlyActive.value) {
        data.filters.is_active = true;
    }

    axios.post(import.meta.env.VITE_API_URL + '/autosells', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        autosells.value = response.data.response.autosells;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingAutosells.value = false;
    });
}

onMounted(() => {
    fetchAutosells();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Configuración de Auto-ventas</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingAutosells">
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <el-input style="width: 100%;" placeholder="Buscar producto..." :prefix-icon="Search"
                        v-model="searchInput" />
                    <div>
                        <el-checkbox v-model="onlyActive" label="Solo activos" />
                    </div>
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchAutosells">
                        <Search size="16" class="mr-1" />
                        Aplicar
                    </el-button>
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingAutosell = true">
                        <Plus size="16" class="mr-1" />
                        Nueva Auto-venta
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="autosells" :row-class-name="tableRowClassName" border
                    style="width:100%" max-height="500" @row-click="(e) => { handleAutosellClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="is_active" label="Estado" min-width="100">
                        <template #default="scope">
                            <el-tag :type="getStatusTag(scope.row.is_active).type">
                                {{ getStatusTag(scope.row.is_active).label }}
                            </el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="product_name" label="Producto" min-width="200" />
                    <el-table-column prop="min_stock" label="Stock Mínimo" min-width="120" />
                    <el-table-column prop="max_stock" label="Stock Máximo" min-width="120" />
                    <el-table-column prop="auto_order_quantity" label="Cantidad Auto-pedido" min-width="160" />
                    <el-table-column prop="supplier_name" label="Proveedor" min-width="180" />
                    <el-table-column prop="last_execution" label="Última Ejecución" min-width="150">
                        <template #default="scope">
                            <el-text v-if="scope.row.last_execution">{{ scope.row.last_execution }}</el-text>
                            <el-text v-else type="info">Nunca</el-text>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingAutosells" @change="handlePageChange" />
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.pointer-rows .el-table__row {
    cursor: pointer;
}
</style>
