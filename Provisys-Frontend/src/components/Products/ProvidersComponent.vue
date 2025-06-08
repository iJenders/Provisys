<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import ProviderDetailsModal from './ProviderDetailsModal.vue';
import ProviderAddModal from './ProviderAddModal.vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const providers = ref([]);

const fetchingProviders = ref(false);
const addingProvider = ref(false);

const selectedProvider = ref({
    "id": "",
    "name": "",
    "phone": "",
    "secondaryPhone": "",
    "email": "",
    "address": "",
    "deleted": 0
});
const isSelectedProvider = ref(false);

const searchInput = ref('');
const onlyDisabled = ref(false);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: 100,
});

const handlePageChange = (page) => {
    paginationConfig.value.page = page;
    fetchProviders();
}

const handleProviderClick = (provider) => {
    selectedProvider.value = provider;
    isSelectedProvider.value = true;
}

const handleCloseDetailsModal = () => {
    isSelectedProvider.value = false;
    selectedProvider.value = {
        "id": "",
        "name": "",
        "phone": "",
        "secondaryPhone": "",
        "email": "",
        "address": "",
        "deleted": 0
    };
    fetchProviders();
}

const handleCloseAddModal = () => {
    addingProvider.value = false;
    fetchProviders();
}

const fetchProviders = () => {
    fetchingProviders.value = true;

    let data = {
        offset: paginationConfig.value.page,
        filters: {},
        search: searchInput.value,
    }

    if (onlyDisabled.value) {
        data.filters.deleted = true;
    }

    // Consulta
    axios.post(import.meta.env.VITE_API_URL + '/manufacturers', data, {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    }).then(response => {
        providers.value = response.data.response.manufacturers;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProviders.value = false;
    });
}

const tableRowClassName = ({ row }) => {
    if (row.deleted) {
        return '!bg-red-100';
    }
}

onMounted(() => {
    fetchProviders();
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Providers Content -->
        <h1 class="text-stone-700 font-medium">Fabricantes de Productos</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingProviders">
            <!-- Products -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <!-- Searcher -->
                    <el-input style="width: 100%;" placeholder="Buscar Fabricante..." :prefix-icon="Search"
                        v-model="searchInput" />

                    <!-- Show only disabled -->
                    <el-checkbox v-model="onlyDisabled" label="Solo deshabilitados" />

                    <!-- Apply Search Filters-->
                    <el-button class="!rounded-full !py-1 !px-2" type="primary" @click="fetchProviders">
                        <Search :size="20" class="mr-2" />
                        Aplicar
                    </el-button>

                    <!--Add Provider button-->
                    <el-button class="!rounded-full !py-1 !px-2 !m-0" type="success" @click="addingProvider = true">
                        Añadir Fabricante
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="providers" border :row-class-name="tableRowClassName"
                    style="width:100%" max-height="500" @row-click="(e) => { handleProviderClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="140" />
                    <el-table-column prop="deleted" label="Habilitado" min-width="100">
                        <template #default="scope">
                            <el-text :type="scope.row.deleted ? 'danger' : 'success'">
                                {{ scope.row.deleted ? 'No' : 'Si' }}
                            </el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="Nombre" min-width="120" />
                    <el-table-column prop="phone" label="Teléfono" min-width="140" />
                    <el-table-column prop="secondaryPhone" label="Teléfono secundario" min-width="140">
                        <template #default="scope">
                            <el-text :class="scope.row.secondaryPhone == '' ? '!text-red-500' : ''">
                                {{ !(scope.row.secondaryPhone == '') ? scope.row.secondaryPhone : 'No' }}
                            </el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="email" label="Correo Electrónico" min-width="120" />
                    <el-table-column prop="address" label="Dirección" min-width="120" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingProviders" @change="handlePageChange" />
                </div>

            </div>
        </div>

        <!-- Provider Details Modal -->
        <ProviderDetailsModal v-model="isSelectedProvider" :selectedProvider="selectedProvider"
            :isSelectedProvider="isSelectedProvider" @close-modal="handleCloseDetailsModal" />

        <!-- New Provider Modal -->
        <ProviderAddModal v-model="addingProvider" :adding-provider="addingProvider"
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