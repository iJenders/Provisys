<script setup>
import { Search, Warehouse, Plus } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import StoragesAddModal from './StoragesAddModal.vue';
import StoragesDetailsModal from './StoragesDetailsModal.vue';

const storages = ref([]);

const fetchingStorages = ref(false);
const addingStorage = ref(false);

const selectedStorage = ref({
    id: null,
    name: '',
    description: '',
    vehicle: false,
    deleted: false,
});
const isSelectedStorage = ref(false);

const searchInput = ref('');
const onlyDisabled = ref(false);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: storages.value.length,
});

const tableRowClassName = ({ row }) => {
    if (row.deleted) {
        return '!bg-red-100';
    }
}

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchStorages();
}

const handleStorageClick = (storage) => {
    selectedStorage.value = JSON.parse(JSON.stringify(storage));
    isSelectedStorage.value = true;
}

const fetchStorages = () => {
    fetchingStorages.value = true;

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

    axios.post(import.meta.env.VITE_API_URL + '/storages', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        let res = response.data.response;

        for (let i = 0; i < res.storages.length; i++) {
            res.storages[i].vehicle = res.storages[i].vehicle ? true : false;
        }

        storages.value = res.storages;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingStorages.value = false;
    });
}

const handleCloseModal = () => {
    isSelectedStorage.value = false;
    fetchStorages();
}

onMounted(() => {
    fetchStorages();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Gestión de Almacenes</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingStorages">
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <el-input style="width: 100%;" placeholder="Buscar almacén..." :prefix-icon="Search"
                        v-model="searchInput" />
                    <div>
                        <el-checkbox v-model="onlyDisabled" label="Solo deshabilitados" />
                    </div>
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchStorages">
                        <Search size="16" class="mr-1" />
                        Aplicar
                    </el-button>
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingStorage = true">
                        <Plus size="16" class="mr-1" />
                        Añadir Almacén
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="storages" :row-class-name="tableRowClassName" border
                    style="width:100%" max-height="500" @row-click="(e) => { handleStorageClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="disabled" label="Habilitado" min-width="100">
                        <template #default="scope">
                            <el-text :type="scope.row.deleted ? 'danger' : 'success'">
                                {{ scope.row.deleted ? 'No' : 'Si' }}
                            </el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="Nombre" min-width="150" />
                    <el-table-column prop="description" label="Descripción" min-width="250" />
                    <el-table-column prop="vehicle" label="Vehicular" min-width="100">
                        <template #default="scope">
                            <el-text :type="scope.row.vehicle ? 'success' : 'danger'">
                                {{ scope.row.vehicle ? 'Si' : 'No' }}
                            </el-text>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingStorages" @change="handlePageChange" />
                </div>
            </div>
        </div>

        <!-- Add Storage Modal -->
        <StoragesAddModal v-model="addingStorage" @storage-added="fetchStorages"
            @closeModal="addingStorage = false" />

        <!-- Edit Storage Modal -->
        <StoragesDetailsModal :selected-storage="selectedStorage" :is-selected-storage="isSelectedStorage"
            @close-modal="handleCloseModal" />
    </div>
</template>

<style>
.pointer-rows .el-table__row {
    cursor: pointer;
}
</style>
