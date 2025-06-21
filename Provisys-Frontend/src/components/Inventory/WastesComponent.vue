<script setup>
import { Search, Trash2, Plus } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import { successNotification } from '@/utils/feedback';
import { ElMessageBox } from 'element-plus';

const wastes = ref([]);

const fetchingWastes = ref(false);
const addingWaste = ref(false);

const filters = ref({
    from: new Date(),
    to: new Date(),
    deleted: 0
});

const selectedWaste = ref({
    id: null,
    product_id: null,
    product_name: '',
    quantity: 0,
    reason: '',
    date: '',
});
const isSelectedWaste = ref(false);

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
        filters: {
            from: filters.value.from.toISOString().split('T')[0],
            to: filters.value.to.toISOString().split('T')[0],
            deleted: filters.value.deleted
        },
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

// New Waste
const fetchingProducts = ref(false);
const savingWaste = ref(false);
const products = ref([]);

const newWaste = ref({
    product_id: null,
    quantity: 1,
    reason: '',
    date: '',
});

const getProducts = (str) => {
    fetchingProducts.value = true;

    let data = {
        filters: {
            deleted: 0
        },
        search: str ? str : null
    }

    axios.post(import.meta.env.VITE_API_URL + '/products', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        products.value = response.data.response.products.map(product => {
            return {
                id: product.id,
                name: product.name,
                description: product.description,
                price: product.actualPrice,
                iva: product.actualIva.value
            }
        });
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProducts.value = false;
    });
}

const saveWaste = () => {
    savingWaste.value = true;


    let data = {
        product_id: newWaste.value.product_id,
        quantity: newWaste.value.quantity,
        reason: newWaste.value.reason,
        date: newWaste.value.date
    }

    axios.post(import.meta.env.VITE_API_URL + '/wastes/create', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        fetchWastes();
        newWaste.value = {
            product_id: null,
            quantity: 1,
            reason: '',
            date: '',
        };
        addingWaste.value = false;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        savingWaste.value = false;
    });
}

const deleteWaste = () => {
    ElMessageBox.confirm('¿Estás seguro de que quieres eliminar esta mermas?', 'Eliminar mermas', {
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        type: 'warning'
    }).then(() => {
        let data = {
            id: selectedWaste.value.id
        }
        axios.post(import.meta.env.VITE_API_URL + '/wastes/delete', data, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            successNotification('Merma eliminada correctamente');
            selectedWaste.value = {
                id: null,
                product_id: null,
                product_name: '',
                quantity: 0,
                reason: '',
                date: ''
            }
            isSelectedWaste.value = false;
            fetchWastes();
        }).catch(error => {
            handleRequestError(error);
        });
    }).catch(() => {
        // Do nothing
    });
}

onMounted(() => {
    fetchWastes();
});

</script>


<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Gestión de Mermas</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="fetchingWastes">
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex justify-end items-center gap-4 flex-wrap">
                    <div class="w-full flex flex-col gap-1">
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Desde:</p>
                                <el-date-picker style="width: 100%;" v-model="filters.from" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return date >= Date.now() }" />
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Hasta:</p>
                                <el-date-picker style="width: 100%;" v-model="filters.to" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return (date >= Date.now()) || (date < filters.from) }" />
                            </div>
                        </div>
                    </div>
                    <el-checkbox v-model="filters.deleted" label="Mostrar eliminados " size="large" />
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

                    <el-table-column prop="date" label="Fecha" min-width="120">
                        <template #default="scope">
                            {{ scope.row.date.split(' ')[0] }}
                        </template>
                    </el-table-column>

                    <el-table-column prop="product_name" label="Producto" min-width="200" />

                    <el-table-column prop="quantity" label="Cantidad" min-width="100">
                        <template #default="scope">
                            <el-text type="danger">{{ scope.row.quantity }}</el-text>
                        </template>
                    </el-table-column>

                    <el-table-column prop="price" label="Precio" min-width="120">
                        <template #default="scope">
                            <el-text type="danger">
                                ${{
                                    parseFloat(scope.row.price).toFixed(2)
                                }}
                            </el-text>
                        </template>
                    </el-table-column>

                    <el-table-column prop="iva" label="IVA" min-width="100">
                        <template #default="scope">
                            {{ scope.row.iva }}%
                        </template>
                    </el-table-column>

                    <el-table-column prop="reason" label="Motivo" min-width="250" />

                    <el-table-column prop="price" label="Desperdicio Total" min-width="120">
                        <template #default="scope">
                            <el-text type="danger">
                                ${{
                                    (parseFloat(scope.row.quantity) * parseFloat(scope.row.price) * (1 +
                                        parseFloat(scope.row.iva) / 100)).toFixed(2)
                                }}
                            </el-text>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingWastes" @change="handlePageChange" />
                </div>

                <!-- Add Waste Modal -->
                <el-dialog v-model="addingWaste" title="Registrar Merma" width="500px" :close-on-click-modal="false">
                    <div class="flex flex-col gap-4">

                        <!-- Product -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Producto:</p>
                            <el-select v-model="newWaste.product_id" filterable placeholder="Seleccionar producto"
                                style="width: 100%;" remote :remote-method="getProducts">
                                <el-option v-for="product in products" :key="product.id" :label="product.name"
                                    :value="product.id" />
                            </el-select>
                        </div>

                        <!-- Quantity -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Cantidad:</p>
                            <el-input-number v-model="newWaste.quantity" :min="1" style="width: 100%;" />
                        </div>

                        <!-- Date -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Fecha:</p>
                            <el-date-picker v-model="newWaste.date" type="date" placeholder="Seleccionar fecha"
                                style="width: 100%;" format="DD/MM/YYYY" value-format="YYYY-MM-DD" />
                        </div>

                        <!-- Reason -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Motivo:</p>
                            <el-input v-model="newWaste.reason" type="textarea" :rows="3"
                                placeholder="Ingrese el motivo de la merma" />
                        </div>


                    </div>
                    <template #footer>
                        <div class="flex justify-end gap-2">
                            <el-button @click="addingWaste = false">Cancelar</el-button>
                            <el-button type="primary" :loading="savingWaste" @click="saveWaste">Guardar</el-button>
                        </div>
                    </template>
                </el-dialog>

                <!-- Edit Waste Modal -->
                <el-dialog v-model="isSelectedWaste" title="Ver Merma" width="500px" :close-on-click-modal="false">
                    <div class="flex flex-col gap-4">

                        <!-- Product -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Producto:</p>
                            <el-select v-model="selectedWaste.product_id" filterable placeholder="Seleccionar producto"
                                style="width: 100%;" disabled>
                                <el-option v-for="product in products" :key="product.id" :label="product.name"
                                    :value="product.id" />
                            </el-select>
                        </div>

                        <!-- Quantity -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Cantidad:</p>
                            <el-input-number v-model="selectedWaste.quantity" :min="1" style="width: 100%;" disabled />
                        </div>

                        <!-- Date -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Fecha:</p>
                            <el-date-picker v-model="selectedWaste.date" type="date" placeholder="Seleccionar fecha"
                                style="width: 100%;" format="DD/MM/YYYY" value-format="YYYY-MM-DD" disabled />
                        </div>

                        <!-- Reason -->
                        <div class="w-full">
                            <p class="text-stone-700 text-sm font-medium mb-1">Motivo:</p>
                            <el-input v-model="selectedWaste.reason" type="textarea" :rows="3"
                                placeholder="Ingrese el motivo de la merma" disabled />
                        </div>

                    </div>
                    <template #footer>
                        <div class="flex justify-end gap-2">
                            <el-button @click="isSelectedWaste = false">Cancelar</el-button>
                            <el-button type="danger" @click="deleteWaste">Eliminar</el-button>
                        </div>
                    </template>
                </el-dialog>
            </div>
        </div>
    </div>
</template>

<style>
.pointer-rows .el-table__row {
    cursor: pointer;
}
</style>
