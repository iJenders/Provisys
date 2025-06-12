<!--

    probablemente todo esto está mal. Hay que implementarlo debidamente

-->



<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Listado de Compras</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col lg:flex-row gap-4" v-loading="fetchingPurchases">
            <!-- Filters -->
            <div class="w-full lg:w-[250px] shrink-0 flex flex-col gap-3 lg:border-stone-200 lg:border-r-[2px] lg:pr-4">
                <p class="text-xl font-medium text-stone-700">Filtrar</p>
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-1 gap-x-6 gap-y-2">
                    <!-- Date Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Fecha de compra</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Desde:</p>
                                <el-date-picker style="width: 100%;" v-model="purchasesFilter.date.from" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return date >= Date.now() }" />
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Hasta:</p>
                                <el-date-picker style="width: 100%;" v-model="purchasesFilter.date.to" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return (date >= Date.now()) || (date < purchasesFilter.date.from) }" />
                            </div>
                        </div>
                    </div>

                    <!-- Provider Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Proveedor</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <el-input style="width: 100%;" v-model="purchasesFilter.provider"
                                    placeholder="Buscar Proveedor..." :prefix-icon="Search" />
                            </div>
                        </div>
                    </div>

                    <!-- Value Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Valor</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Min:</p>
                                <el-input-number v-model="purchasesFilter.value.from" controls-position="right"
                                    style="width: 100%;" :precision="2" :min="0.01" :max="9999999999.99">
                                    <template #prefix>
                                        <span>$</span>
                                    </template>
                                </el-input-number>
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Max:</p>
                                <el-input-number v-model="purchasesFilter.value.to" controls-position="right"
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
                <div class="w-full flex flex-col gap-1">
                    <div class="w-full flex flex-col justify-center gap-2">
                        <ThemeButton
                            class="w-full rounded-full text-sm !py-1 border-green-600 text-green-600 hover:bg-green-600 hover:text-white">
                            Aplicar Filtros
                        </ThemeButton>
                        <ThemeButton
                            class="w-full rounded-full text-sm !py-1 border-stone-700 text-stone-700 hover:bg-stone-700 hover:text-white">
                            Limpiar
                        </ThemeButton>
                    </div>
                </div>
            </div>

            <Line orientation="horizontal" class="lg:hidden bg-stone-200" />

            <!-- Purchases -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex items-center gap-1">
                    <el-input style="width: 100%;" v-model="purchasesFilter.searcher" placeholder="Buscar Compra..."
                        :prefix-icon="Search" />
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="computedPurchases" stripe border style="width:100%"
                    max-height="500" @row-click="(e) => { handlePurchaseClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="date" label="Fecha" width="120" />
                    <el-table-column prop="provider.name" label="Proveedor" min-width="180" />
                    <el-table-column prop="total" label="Valor" min-width="100">
                        <template #default="scope">
                            ${{ scope.row.total }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="totalProducts" label="Cant. Productos" min-width="100" />
                    <el-table-column prop="provider.phone" label="Contacto" min-width="180" />
                    <el-table-column prop="provider.address" label="Dirección" min-width="180" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingPurchases" @change="handlePageChange" />
                </div>
            </div>
        </div>

        <!-- Purchase Details Modal -->
        <el-dialog v-model="isSelectedPurchase" title="Detalles de la Compra" width="80%"
            @close="selectedPurchase = null" :fullscreen="fullScreenModals" class="!p-6">
            <!-- Modal Content -->
            <Line orientation="horizontal" class="bg-stone-200" />
            <div v-if="selectedPurchase" class="w-full flex flex-col gap-4 mt-4" v-loading="fetchingModal">
                <!-- Purchase Info -->
                <div class="w-full flex flex-col sm:flex-row sm:gap-6 relative overflow-x-scroll">
                    <div class="flex flex-col sm:border-r-2 sm:border-stone-200 sm:pr-6">
                        <p class="text-stone-700 text-sm font-bold">Id:
                            <span class="font-normal">{{ selectedPurchase.id }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Proveedor:
                            <span class="font-normal">{{ selectedPurchase.provider.name }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Fecha:
                            <span class="font-normal">{{ selectedPurchase.date }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Dirección:
                            <span class="font-normal">{{ selectedPurchase.provider.address }}</span>
                        </p>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-stone-700 text-sm font-bold">Estado:
                            <span v-if="selectedPurchase.status === 'completed'"
                                class="text-green-600">Completada</span>
                            <span v-else-if="selectedPurchase.status === 'pending'"
                                class="text-yellow-600">Pendiente</span>
                            <span v-else class="text-red-600">Cancelada</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Contacto:
                            <span class="font-normal">{{ selectedPurchase.provider.contact }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Teléfono:
                            <span class="font-normal">{{ selectedPurchase.provider.phone }}</span>
                        </p>
                    </div>
                </div>

                <Line orientation="horizontal" class="bg-stone-200" />

                <!-- Purchase Products -->
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-stone-700 text-lg font-medium">Productos:</h3>
                    <el-table :data="selectedPurchase.products" stripe border style="width:100%" max-height="500">
                        <el-table-column prop="id" label="Id" min-width="60" />
                        <el-table-column prop="name" label="Nombre" width="120" />
                        <el-table-column prop="description" label="Descripción" width="120">
                            <template #default="scope">
                                <span class="line-clamp-3">{{ scope.row.description }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="unitPrice" label="Precio Unitario" width="120">
                            <template #default="scope">
                                ${{ scope.row.unitPrice }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="quantity" label="Cantidad" width="120" />
                        <el-table-column label="Subtotal" width="120">
                            <template #default="scope">
                                ${{ (scope.row.unitPrice * scope.row.quantity).toFixed(2) }}
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="flex justify-end">
                        <p class="text-stone-700 text-lg font-medium">Total: ${{
                            selectedPurchase.products.reduce((acc, curr) => {
                                return acc + (curr.unitPrice * curr.quantity)
                            }, 0).toFixed(2)
                        }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Buttons -->
            <template #footer v-if="selectedPurchase">
                <div class="dialog-footer flex justify-end flex-wrap gap-4 gap-y-2">
                    <el-button class="!ml-0" type="danger" :disabled="fetchingModal" @click="handleCancelPurchase">
                        Cancelar Compra
                    </el-button>
                    <el-button v-if="selectedPurchase.status === 'pending'" class="!ml-0" type="success"
                        :disabled="fetchingModal" @click="handleCompletePurchase">
                        Completar Compra
                    </el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import ThemeButton from '../ThemeButton.vue';
import { confirmation, successNotification, errorNotification } from '@/utils/feedback';

const fetchingModal = ref(false);
const fetchingPurchases = ref(false);
const fullScreenModals = ref(false);
const selectedPurchase = ref(null);
const isSelectedPurchase = ref(false);

const purchasesFilter = ref({
    date: {
        from: null,
        to: null
    },
    provider: '',
    value: {
        from: null,
        to: null
    },
    searcher: ''
});

const paginationConfig = ref({
    rowsPerPage: 10,
    totalRows: 100
});

const computedPurchases = computed(() => {
    // Add your computed purchases logic here
    return [];
});

const handlePurchaseClick = (purchase) => {
    selectedPurchase.value = purchase;
    isSelectedPurchase.value = true;
};

const handlePageChange = (page) => {
    // Add your page change logic here
};

const handleCancelPurchase = () => {
    confirmation("Alerta", "¿Está seguro de que desea cancelar la compra?",
        () => {
            fetchingModal.value = true;
            setTimeout(() => {
                fetchingModal.value = false;
                if (Math.random() > 0.5) {
                    successNotification("Compra cancelada exitosamente");
                } else {
                    errorNotification("Error al cancelar la compra");
                }
            }, 2000)
        },
        () => { }
    )
}

const handleCompletePurchase = () => {
    confirmation("Alerta", "¿Está seguro de que desea completar la compra?",
        () => {
            successNotification("Compra completada exitosamente");
        },
        () => { }
    )
}
</script>

<style>
.pointer-rows .el-table__row {
    cursor: pointer;
}

.el-overlay {
    z-index: 10000 !important;
}

.el-date-table td.disabled .el-date-table-cell {
    background-color: var(--color-red-100) !important;
}
</style>
