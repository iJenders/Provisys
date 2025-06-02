<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { ref } from 'vue';
import ProviderDetailsModal from './ProviderDetailsModal.vue';
import ProviderAddModal from './ProviderAddModal.vue';

const providers = ref([
    { id: 1, name: 'Proveedor A', phone: '123456789', email: 'proveedorA@example.com', address: 'Calle 123, Ciudad' },
    { id: 2, name: 'Proveedor B', phone: '987654321', email: 'proveedorB@example.com', address: 'Avenida 456, Ciudad' },
    { id: 3, name: 'Proveedor C', phone: '456789123', email: 'proveedorC@example.com', address: 'Calle 789, Ciudad' },
]);

const fetchingProviders = ref(false);
const addingProvider = ref(false);

const selectedProvider = ref(null);
const isSelectedProvider = ref(false);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 1,
    totalRows: providers.value.length,
});

const handlePageChange = (page) => {
    fetchingProviders.value = true;
    setTimeout(() => {
        fetchingProviders.value = false;
    }, 1000);
}

const handleProviderClick = (provider) => {
    selectedProvider.value = provider;
    isSelectedProvider.value = true;
}

const handleCloseDetailsModal = () => {
    isSelectedProvider.value = false;
    selectedProvider.value = null;
}

const handleCloseAddModal = () => {
    addingProvider.value = false;
}
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Providers Content -->
        <h1 class="text-stone-700 font-medium">Proveedores de Productos</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingProviders">
            <!-- Products -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex items-center gap-4">
                    <el-input style="width: 100%;" placeholder="Buscar Proveedor..." :prefix-icon="Search" />
                    <!--Add Provider button-->
                    <el-button class="!rounded-full !py-1 !px-2" type="success" @click="addingProvider = true">
                        Añadir Proveedor
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="providers" stripe border style="width:100%" max-height="500"
                    @row-click="(e) => { handleProviderClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="name" label="Nombre" min-width="120" />
                    <el-table-column prop="phone" label="Teléfono" min-width="120" />
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