<script setup>
import { Search, Trash2, Plus } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const wastes = ref([]);

const fetchingWastes = ref(false);
const addingWaste = ref(false);

const selectedWaste = ref({
    id: null,
    product_id: null,
    product_name: '',
    quantity: 0,
    reason: '',
    date: '',
});
const isSelectedWaste = ref(false);

const searchInput = ref('');
const dateRange = ref([]);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: wastes.value.length,
});

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchWastes();
}

const handleWasteClick = (waste) => {
    selectedWaste.value = waste;
    isSelectedWaste.value = true;
}

const fetchWastes = () => {
    fetchingWastes.value = true;

    let data = {
        filters: {}
    }

    if (searchInput.value) {
        data.search = searchInput.value
    }
    data.offset = paginationConfig.value.page;

    if (dateRange.value && dateRange.value.length === 2) {
        data.filters.date_from = dateRange.value[0];
        data.filters.date_to = dateRange.value[1];
    }

    axios.post(import.meta.env.VITE_API_URL + '/wastes', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        wastes.value = response.data.response.wastes;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingWastes.value = false;
    });
}

onMounted(() => {
    fetchWastes();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Gestión de Mermas</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingWastes">
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <el-input style="width: 100%;" placeholder="Buscar producto..." :prefix-icon="Search"
                        v-model="searchInput" />
                    <el-date-picker v-model="dateRange" type="daterange" range-separator="a"
                        start-placeholder="Fecha inicio" end-placeholder="Fecha fin" format="YYYY-MM-DD"
                        value-format="YYYY-MM-DD" />
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchWastes">
                        <Search size="16" class="mr-1" />
                        Aplicar
                    </el-button>
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingWaste = true">
                        <Plus size="16" class="mr-1" />
                        Registrar Merma
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="wastes" border style="width:100%" max-height="500"
                    @row-click="(e) => { handleWasteClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="date" label="Fecha" min-width="120" />
                    <el-table-column prop="product_name" label="Producto" min-width="200" />
                    <el-table-column prop="quantity" label="Cantidad" min-width="100">
                        <template #default="scope">
                            <el-text type="danger">{{ scope.row.quantity }}</el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="reason" label="Motivo" min-width="250" />
                    <el-table-column prop="total_value" label="Valor Total" min-width="120">
                        <template #default="scope">
                            <el-text type="danger">${{ scope.row.total_value }}</el-text>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingWastes" @change="handlePageChange" />
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
