<script setup>
import { ref } from 'vue';
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import ThemeButton from '@/components/ThemeButton.vue';
import { useOrderState } from '@/composables/orderState.js'
import { confirmation, successNotification, errorNotification } from '@/utils/feedback.js';
import { onMounted } from 'vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const {
    fetchingOrders,
    ordersFilter,
    orders,
    paginationConfig,
    fullScreenModals,
    handlePageChange
} = useOrderState()

const selectedOrder = ref({
    id: 0,
    date: "",
    status: 0,
    username: "",
    totalProducts: "",
    payment: {
        id: 0,
        value: "",
        paid: "",
        verified: 0,
        paying: 0
    }
});
const isSelectedOrder = ref(false);
const fetchingModal = ref(false);

const getOrders = () => {
    fetchingOrders.value = true;
    let data = {
        filters: {
            status: 3
        }
    }
    axios.post(import.meta.env.VITE_API_URL + '/orders', data, {
        headers: {
            Authorization: `${localStorage.getItem('token')}`
        }
    }).then((res) => {
        orders.value = res.data.response.orders
        paginationConfig.value.totalRows = res.data.response.orders.length
    }).catch((err) => {
        handleRequestError(err)
    }).finally(() => {
        fetchingOrders.value = false;
    })
}

const handleOrderClick = (order) => {
    fetchingModal.value = true;

    let data = {
        orderId: order.id
    }

    axios.post(import.meta.env.VITE_API_URL + '/orders/products', data, {
        headers: {
            Authorization: `${localStorage.getItem('token')}`
        }
    }).then((res) => {
        selectedOrder.value = order;
        selectedOrder.value.products = res.data.response.products;
        selectedOrder.value.payment.payments = res.data.response.payments;
        isSelectedOrder.value = true;
    }).catch((err) => {
        console.log(err)
        handleRequestError(err)
        isSelectedOrder.value = false;
    }).finally(() => {
        fetchingModal.value = false;
    })
}

onMounted(() => {
    getOrders()
})
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Waiting Orders Content -->
        <h1 class="text-stone-700 font-medium">Pedidos en Espera</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col lg:flex-row gap-4" v-loading="fetchingOrders">
            <!-- Filters -->
            <div class="w-full lg:w-[250px] shrink-0 flex flex-col gap-3 lg:border-stone-200 lg:border-r-[2px] lg:pr-4">
                <p class="text-xl font-medium text-stone-700">Filtrar</p>
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-1 gap-x-6 gap-y-2">
                    <!-- Date Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Fecha de pedido</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Desde:</p>
                                <el-date-picker style="width: 100%;" v-model="ordersFilter.date.from" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return date >= Date.now() }" />
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Hasta:</p>
                                <el-date-picker style="width: 100%;" v-model="ordersFilter.date.to" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return (date >= Date.now()) || (date < ordersFilter.date.from) }" />
                            </div>
                        </div>
                    </div>

                    <!-- Value Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Valor</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Min:</p>
                                <el-input-number v-model="ordersFilter.value.from" controls-position="right"
                                    style="width: 100%;" :precision="2" :min="0.01" :max="9999999999.99">
                                    <template #prefix>
                                        <span>$</span>
                                    </template>
                                </el-input-number>
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Max:</p>
                                <el-input-number v-model="ordersFilter.value.to" controls-position="right"
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

            <!-- Orders -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex items-center gap-1">
                    <el-input style="width: 100%;" v-model="ordersFilter.searcher" placeholder="Buscar Pedido..."
                        :prefix-icon="Search" />
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="orders" stripe border style="width:100%" max-height="500"
                    @row-click="(e) => { handleOrderClick(e) }">

                    <el-table-column prop="id" label="Id" min-width="60" />

                    <el-table-column prop="status" label="Estado" min-width="130">
                        <template #default="scope">
                            <span class="text-yellow-600" v-if="scope.row.status == '0'">Pendiente</span>
                            <span class="text-blue-600" v-else-if="scope.row.status == '1'">Facturado</span>
                            <span class="text-green-600" v-else-if="scope.row.status == '2'">Entregado</span>
                            <span class="text-red-600" v-else>Cancelado</span>
                        </template>
                    </el-table-column>

                    <el-table-column prop="date" label="Fecha" width="120" />

                    <el-table-column prop="payment" label="Valor" min-width="100">
                        <template #default="scope">
                            ${{ parseFloat(scope.row.payment.value).toFixed(2) }}
                        </template>
                    </el-table-column>

                    <el-table-column prop="payment" label="Estado Pago" min-width="130">
                        <template #default="scope">
                            <span class="text-green-600" v-if="scope.row.payment.verified">Verificado</span>
                            <span class="text-red-600" v-else>No Verificado</span>
                        </template>
                    </el-table-column>

                    <el-table-column prop="payment" label="Total pagado" min-width="130">
                        <template #default="scope">
                            <span class="font-semibold"
                                :class="parseFloat(scope.row.payment.value).toFixed(2) <= parseFloat(scope.row.payment.paid).toFixed(2) ? 'text-green-600' : 'text-red-600'">
                                ${{ parseFloat(scope.row.payment.paid).toFixed(2) }} /
                                ${{ parseFloat(scope.row.payment.value).toFixed(2) }}
                            </span>

                        </template>
                    </el-table-column>

                    <el-table-column prop="totalProducts" label="Cant. Productos" min-width="100" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingOrders" @change="handlePageChange" />
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <el-dialog v-model="isSelectedOrder" title="Detalles del Pedido" width="80%" @close="selectedOrder = null"
            :fullscreen="fullScreenModals" class="!p-6">
            <!-- Modal Content -->
            <Line orientation="horizontal" class="bg-stone-200" />
            <div v-if="selectedOrder" class="w-full flex flex-col gap-4 mt-4" v-loading="fetchingModal">
                <!-- Order Info -->
                <div class="w-full flex flex-col sm:flex-row sm:gap-6 relative overflow-x-scroll">
                    <div class="flex flex-col sm:border-r-2 sm:border-stone-200 sm:pr-6">

                        <p class="text-stone-700 text-sm font-bold">Id:
                            <span class="font-normal">{{ selectedOrder.id }}</span>
                        </p>

                        <p class="text-stone-700 text-sm font-bold">Fecha:
                            <span class="font-normal">{{ selectedOrder.date }}</span>
                        </p>

                        <p class="text-stone-700 text-sm font-bold">Estado:
                            <span class="text-yellow-600" v-if="selectedOrder.status == '0'">Pendiente</span>
                            <span class="text-blue-600" v-else-if="selectedOrder.status == '1'">Facturado</span>
                            <span class="text-green-600" v-else-if="selectedOrder.status == '2'">Entregado</span>
                            <span class="text-red-600" v-else>Cancelado</span>
                        </p>

                    </div>

                    <div class="flex flex-col">
                        <p class="text-stone-700 text-sm font-bold">Total pagado:
                            <span
                                v-if="parseFloat(selectedOrder.payment.value).toFixed(2) <= parseFloat(selectedOrder.payment.paid).toFixed(2)"
                                class="text-green-600">
                                ${{ parseFloat(selectedOrder.payment.paid).toFixed(2) }} /
                                ${{ parseFloat(selectedOrder.payment.value).toFixed(2) }}
                            </span>
                            <span v-else class="text-red-600">
                                ${{ parseFloat(selectedOrder.payment.paid).toFixed(2) }} /
                                ${{ parseFloat(selectedOrder.payment.value).toFixed(2) }}
                            </span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Verificación de pagos:
                            <span class="text-green-600" v-if="selectedOrder.payment.verified">Verificado</span>
                            <span class="text-red-600" v-else>No verificado</span>
                        </p>
                    </div>
                </div>

                <Line orientation="horizontal" class="bg-stone-200" />

                <!-- Order Products -->
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-stone-700 text-lg font-medium">Productos:</h3>
                    <el-table :data="selectedOrder.products" stripe border style="width:100%" max-height="500">
                        <el-table-column prop="id" label="Id" min-width="60" />

                        <el-table-column prop="name" label="Nombre" width="120" />

                        <el-table-column prop="manufacturer" label="Fabricante" width="120" />

                        <el-table-column prop="description" label="Descripción" width="120">
                            <template #default="scope">
                                <span class="line-clamp-3">{{ scope.row.description }}</span>
                            </template>
                        </el-table-column>

                        <el-table-column prop="price" label="Precio de compra" width="120">
                            <template #default="scope">
                                ${{ scope.row.price }}
                            </template>
                        </el-table-column>

                        <el-table-column prop="tax" label="IVA instantáneo" width="120">
                            <template #default="scope">
                                {{ scope.row.tax }} %
                            </template>
                        </el-table-column>

                        <el-table-column prop="quantity" label="Cantidad" width="120" />

                        <el-table-column label="Subtotal" width="120">
                            <template #default="scope">
                                ${{ (
                                    (scope.row.price * scope.row.quantity) * (1 + scope.row.tax / 100)
                                ).toFixed(2) }}
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="flex justify-end">
                        <p class="text-stone-700 text-lg font-medium">Total: ${{
                            parseFloat(selectedOrder.payment.value).toFixed(2)
                        }}</p>
                    </div>
                </div>

                <!-- Order Payments Modal -->
                <div class="w-full flex flex-col gap-4" v-if="selectedOrder.payment.payments.length > 0">
                    <h3 class="text-stone-700 text-lg font-medium">Pagos:</h3>
                    <el-table :data="selectedOrder.payment.payments" stripe border style="width:100%" max-height="300">
                        <el-table-column prop="id" label="Id" min-width="60" />

                        <el-table-column prop="date" label="Fecha" min-width="120">
                            <template #default="scope">
                                {{ new Date(scope.row.date).toLocaleDateString() }}
                            </template>
                        </el-table-column>

                        <el-table-column prop="amount" label="Monto" min-width="120">
                            <template #default="scope">
                                ${{ parseFloat(scope.row.amount).toFixed(2) }}
                            </template>
                        </el-table-column>

                        <el-table-column prop="reference" label="Número de referencia" min-width="150" />

                        <el-table-column prop="verified" label="Verificación" min-width="120">
                            <template #default="scope">
                                <el-tag :type="scope.row.verified ? 'success' : 'warning'">
                                    {{ scope.row.verified == 1 ? 'Verificado' : 'Pendiente' }}
                                </el-tag>
                            </template>
                        </el-table-column>

                        <el-table-column prop="method" label="Método de pago" min-width="120" />
                    </el-table>
                    <div class="flex justify-end gap-4">
                        <p class="text-stone-700 text-lg font-medium">Pagado: ${{
                            parseFloat(selectedOrder.payment.paid).toFixed(2)
                        }}</p>
                        <p class="text-stone-700 text-lg font-medium">Restante: ${{
                            (parseFloat(selectedOrder.payment.value) -
                                parseFloat(selectedOrder.payment.paid)).toFixed(2)
                        }}</p>
                    </div>
                </div>

            </div>
        </el-dialog>
    </div>
</template>

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