<script setup>
import { Search, ArrowLeftRight, Plus } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const movements = ref([]);

const fetchingMovements = ref(false);
const addingMovement = ref(false);

const selectedMovement = ref({
    id: null,
    product_id: null,
    product_name: '',
    from_storage: '',
    to_storage: '',
    quantity: 0,
    date: '',
    notes: '',
});
const isSelectedMovement = ref(false);

const searchInput = ref('');
const dateRange = ref([]);
const movementType = ref('');

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: movements.value.length,
});

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchMovements();
}

const handleMovementClick = (movement) => {
    selectedMovement.value = movement;
    isSelectedMovement.value = true;
}

const getMovementTypeTag = (type) => {
    const types = {
        'in': { label: 'Entrada', type: 'success' },
        'out': { label: 'Salida', type: 'danger' },
        'transfer': { label: 'Transferencia', type: 'warning' }
    };
    return types[type] || { label: type, type: 'info' };
}

const fetchMovements = () => {
    fetchingMovements.value = true;

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

    if (movementType.value) {
        data.filters.type = movementType.value;
    }

    axios.post(import.meta.env.VITE_API_URL + '/movements', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        movements.value = response.data.response.movements;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingMovements.value = false;
    });
}

onMounted(() => {
    fetchMovements();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Movimientos de Inventario</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingMovements">
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <el-input style="width: 100%;" placeholder="Buscar producto..." :prefix-icon="Search"
                        v-model="searchInput" />
                    <el-select v-model="movementType" placeholder="Tipo de movimiento" clearable>
                        <el-option label="Entrada" value="in" />
                        <el-option label="Salida" value="out" />
                        <el-option label="Transferencia" value="transfer" />
                    </el-select>
                    <el-date-picker v-model="dateRange" type="daterange" range-separator="a"
                        start-placeholder="Fecha inicio" end-placeholder="Fecha fin" format="YYYY-MM-DD"
                        value-format="YYYY-MM-DD" />
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchMovements">
                        <Search size="16" class="mr-1" />
                        Aplicar
                    </el-button>
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingMovement = true">
                        <Plus size="16" class="mr-1" />
                        Nuevo Movimiento
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="movements" border style="width:100%" max-height="500"
                    @row-click="(e) => { handleMovementClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="date" label="Fecha" min-width="120" />
                    <el-table-column prop="type" label="Tipo" min-width="120">
                        <template #default="scope">
                            <el-tag :type="getMovementTypeTag(scope.row.type).type">
                                {{ getMovementTypeTag(scope.row.type).label }}
                            </el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="product_name" label="Producto" min-width="200" />
                    <el-table-column prop="quantity" label="Cantidad" min-width="100" />
                    <el-table-column prop="from_storage" label="Origen" min-width="150" />
                    <el-table-column prop="to_storage" label="Destino" min-width="150" />
                    <el-table-column prop="notes" label="Notas" min-width="200" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingMovements" @change="handlePageChange" />
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