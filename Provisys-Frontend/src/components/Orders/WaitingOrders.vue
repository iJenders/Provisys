<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import ThemeButton from '../ThemeButton.vue';
import { useOrderState } from '@/composables/orderState.js';
import { confirmation, successNotification, errorNotification } from '@/utils/feedback';

const {
    fetchingModal,
    fetchingOrders,
    ordersFilter,
    computedOrders,
    paginationConfig,
    selectedOrder,
    isSelectedOrder,
    fullScreenModals,
    handleOrderClick,
    handlePageChange
} = useOrderState();

const handleCancelOrder = () => {
    confirmation("Alerta", "¿Está seguro de que desea cancelar el pedido",
        () => {
            // Simular petición...
            fetchingModal.value = true;
            setTimeout(() => {
                fetchingModal.value = false;
                if (Math.random() > 0.5) {
                    successNotification("Pedido cancelado exitosamente");
                } else {
                    errorNotification("Error al cancelar el pedido");
                }
            }, 2000)
        },
        () => { }
    )
}

const handleConfirmPayment = () => {
    confirmation("Alerta", "¿Está seguro de que desea confirmar el pago del pedido",
        () => {
            successNotification("Pago confirmado exitosamente");
        },
        () => { }
    )
}

const handleBillOrder = () => {
    confirmation("Alerta", "¿Está seguro de que desea facturar el pedido",
        () => {
            successNotification("Pedido facturado exitosamente");
        },
        () => { }
    )
}
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
                                    :disabled-date="(date) => { return date > Date.now() }" />
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[50px] shrink-0 text-stone-700 text-sm font-medium">Hasta:</p>
                                <el-date-picker style="width: 100%;" v-model="ordersFilter.date.to" type="date"
                                    placeholder="aaaa-mm-dd" size="default"
                                    :disabled-date="(date) => { return date > Date.now() }" />
                            </div>
                        </div>
                    </div>

                    <!-- Client Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Cliente</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="text-stone-700 text-sm font-medium">Desde:</p>
                                <el-input style="width: 100%;" v-model="ordersFilter.client"
                                    placeholder="Buscar Cliente..." :prefix-icon="Search" />
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
                <div class=" w-full flex flex-col gap-1">
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
                <el-table class="pointer-rows" :data="computedOrders" stripe border style="width:100%"
                    @row-click="(e) => { handleOrderClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="date" label="Fecha" width="120" />
                    <el-table-column prop="payment.method.name" label="Método de Pago" min-width="180" />
                    <el-table-column prop="payment.verified" label="Estado Pago" min-width="130">
                        <template #default="scope">
                            <span class="text-green-600" v-if="scope.row.payment.verified"> Verificado </span>
                            <span class="text-red-600" v-else> No Verificado </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="subTotal" label="Valor" min-width="100">
                        <template #default="scope">
                            ${{ scope.row.subTotal }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="totalProducts" label="Cant. Productos" min-width="100" />
                    <el-table-column prop="client.fullName" label="Cliente" min-width="180" />
                    <el-table-column prop="client.address" label="Ubicación" min-width="180" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingOrders" @change="handlePageChange" />
                </div>

            </div>
        </div>

        <!-- Waiting Orders Modal -->
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
                        <p class="text-stone-700 text-sm font-bold">Cliente:
                            <span class="font-normal">{{ selectedOrder.client.fullName }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Fecha:
                            <span class="font-normal">{{ selectedOrder.date }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Ubicación:
                            <span class="font-normal">{{ selectedOrder.client.address }}</span>
                        </p>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-stone-700 text-sm font-bold">Método de Pago:
                            <span class="font-normal">{{ selectedOrder.payment.method.name }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Referencia:
                            <span class="font-normal">{{ selectedOrder.payment.paymentReference }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Estado del Pago:
                            <span v-if="selectedOrder.payment.verified" class="text-green-600">Verificado</span>
                            <span v-else="selectedOrder.payment.verified" class="text-red-600">No Verificado</span>
                        </p>
                    </div>
                </div>

                <Line orientation="horizontal" class="bg-stone-200" />

                <!-- Order Products -->
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-stone-700 text-lg font-medium">Productos:</h3>
                    <el-table :data="selectedOrder.products" stripe border style="width:100%">
                        <el-table-column prop="productDetails.id" label="Id" min-width="60" />
                        <el-table-column prop="productDetails.name" label="Nombre" width="120" />
                        <el-table-column prop="productDetails.provider.name" label="Proveedor" width="120" />
                        <el-table-column prop="productDetails.description" label="Descripción" width="120">
                            <template #default="scope">
                                <span class="line-clamp-3">{{ scope.row.productDetails.description }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="price" label="precio" width="120">
                            <template #default="scope">
                                ${{ scope.row.price }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="iva" label="IVA" width="120">
                            <template #default="scope">
                                {{ scope.row.iva }} %
                            </template>
                        </el-table-column>
                        <el-table-column prop="quantity" label="Cantidad" width="120" />
                        <el-table-column label="Subtotal" width="120">
                            <template #default="scope">
                                ${{ (
                                    (scope.row.price * scope.row.quantity) +
                                    (scope.row.price * scope.row.quantity) *
                                    (scope.row.iva / 100)
                                ).toFixed(2) }}
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="flex justify-end">
                        <p class="text-stone-700 text-lg font-medium">Total: ${{
                            selectedOrder.products.reduce((acc, curr) => {
                                return acc + (curr.price * curr.quantity) + (curr.price * curr.quantity) * (curr.iva /
                                    100)
                            }, 0).toFixed(2)
                        }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Buttons -->
            <template #footer v-if="selectedOrder">
                <div class="dialog-footer flex justify-end flex-wrap gap-4 gap-y-2">
                    <el-button class="!ml-0" type="danger" :disabled="fetchingModal" @click="handleCancelOrder">
                        Anular Pedido
                    </el-button>
                    <el-button v-if="!selectedOrder.payment.verified" class="!ml-0" type="primary"
                        :disabled="fetchingModal" @click="handleConfirmPayment">
                        Confirmar Pago
                    </el-button>
                    <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleBillOrder">
                        Facturar Pedido
                    </el-button>
                </div>
            </template>
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
</style>