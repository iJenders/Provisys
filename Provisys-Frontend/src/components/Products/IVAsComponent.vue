<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import IVADetailsModal from './IVADetailsModal.vue';
import IVAAddModal from './IVAAddModal.vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const ivas = ref([]);

const fetchingIVAs = ref(false);
const addingIVA = ref(false);

const selectedIVA = ref({
    id: null,
    name: '',
    value: 0,
    description: '',
});
const isSelectedIVA = ref(false);

const searchInput = ref('');
const onlyDisabled = ref(false);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: ivas.value.length,
});

const tableRowClassName = ({ row }) => {
    if (row.deleted) {
        return '!bg-red-100';
    }
}

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchIVAs();
    console.log(page);
}

const handleIVAClick = (iva) => {
    selectedIVA.value = iva;
    isSelectedIVA.value = true;
}

const handleCloseDetailsModal = () => {
    isSelectedIVA.value = false;
    selectedIVA.value = {
        id: null,
        name: '',
        value: 0,
        description: '',
    };
    fetchIVAs();
}

const handleCloseAddModal = () => {
    addingIVA.value = false;
    fetchIVAs();
}

const fetchIVAs = () => {
    fetchingIVAs.value = true;

    let data = {
        filters: {}
    }

    if (searchInput.value) {
        data.search = searchInput.value
    }
    data.offset = paginationConfig.value.page;

    if (onlyDisabled.value) {
        data.filters.deleted = true;
    }

    // Consulta
    axios.post(import.meta.env.VITE_API_URL + '/ivas', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        ivas.value = response.data.response.IVAs;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingIVAs.value = false;
    });
}

onMounted(() => {
    fetchIVAs();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- IVAs Content -->
        <h1 class="text-stone-700 font-medium">Configuración de Impuestos sobre Valor Agregado (IVA)</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingIVAs">
            <!-- IVAs -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <el-input style="width: 100%;" placeholder="Buscar IVA..." :prefix-icon="Search"
                        v-model="searchInput" />
                    <!-- See disabled check -->
                    <div>
                        <el-checkbox v-model="onlyDisabled" label="Solo deshabilitados" />
                    </div>
                    <!-- Apply search button -->
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchIVAs">
                        <Search size="16" class="mr-1" />
                        Aplicar
                    </el-button>
                    <!--Add IVA button-->
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingIVA = true">
                        Añadir IVA
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="ivas" :row-class-name="tableRowClassName" border
                    style="width:100%" max-height="500" @row-click="(e) => { handleIVAClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="disabled" label="Habilitado" min-width="100">
                        <template #default="scope">
                            <el-text :type="scope.row.deleted ? 'danger' : 'success'">
                                {{ scope.row.deleted ? 'No' : 'Si' }}
                            </el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="Denominación" min-width="120" />
                    <el-table-column prop="value" label="Valor" min-width="240">
                        <template #default="scope">
                            <el-text>{{ scope.row.value }}%</el-text>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingIVAs" @change="handlePageChange" />
                </div>

            </div>
        </div>

        <!-- IVA Details Modal -->
        <IVADetailsModal v-model="isSelectedIVA" :selectedIVA="selectedIVA" :isSelectedIVA="isSelectedIVA"
            @close-modal="handleCloseDetailsModal" />

        <!-- New IVA Modal -->
        <IVAAddModal v-model="addingIVA" :addingIVA="addingIVA" @close-modal="handleCloseAddModal" />
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
