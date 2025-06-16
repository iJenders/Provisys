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
                                <p class="w-[50px] shrink-d0 text-stone-700 text-sm font-medium">Desde:</p>
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
                                <el-select v-model="purchasesFilter.provider" placeholder="Seleccionar proveedor"
                                    size="default" style="width: 100%;" filterable remote :remote-method="getProviders">
                                    <el-option v-for="provider in providers" :key="provider.id" :label="provider.name"
                                        :value="provider.name" />
                                </el-select>
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
                        <ThemeButton @click="getPurchases"
                            class="w-full rounded-full text-sm !py-1 border-green-600 text-green-600 hover:bg-green-600 hover:text-white">
                            Aplicar Filtros
                        </ThemeButton>
                        <ThemeButton @click="clearFilters"
                            class="w-full rounded-full text-sm !py-1 border-stone-700 text-stone-700 hover:bg-stone-700 hover:text-white">
                            Limpiar
                        </ThemeButton>
                    </div>
                </div>
            </div>

            <Line orientation="horizontal" class="lg:hidden bg-stone-200" />

            <!-- Purchases -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">

                <!-- Table -->
                <el-table class="pointer-rows" :data="purchases" stripe border style="width:100%" max-height="500"
                    @row-click="(e) => { handlePurchaseClick(e) }">
                    <el-table-column prop="id_compra" label="Id" min-width="60" />
                    <el-table-column prop="fecha_compra" label="Fecha" width="120">
                        <template #default="scope">
                            {{ scope.row.fecha_compra }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="nombre" label="Proveedor" min-width="180" />
                    <el-table-column prop="total" label="Valor" min-width="100">
                        <template #default="scope">
                            ${{
                                parseFloat(scope.row.total).toFixed(2)
                            }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="pago_verificado" label="Pagado" min-width="200">
                        <template #default="scope">
                            <span class="font-bold"
                                :class="scope.row.pago_verificado ? 'text-green-600' : 'text-red-600'">
                                {{ scope.row.pago_verificado ? "Si" : "No" }}
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="cantidadProductos" label="Cant. Productos" min-width="100" />
                    <el-table-column prop="contacto" label="Contacto" min-width="180" />
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
                            <span class="font-normal">{{ selectedPurchase.date.split(' ')[0] }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Dirección Proveedor:
                            <span class="font-normal">{{ selectedPurchase.provider.address }}</span>
                        </p>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-stone-700 text-sm font-bold">Pagado:
                            <span class="font-bold" :class="selectedPurchasePaid ? 'text-green-600' : 'text-red-600'">
                                ${{
                                    selectedPurchase.payments.reduce((acc, curr) => {
                                        return acc += parseFloat(curr.amount)
                                    }, 0).toFixed(2)
                                }}
                                /
                                ${{
                                    selectedPurchase.products.reduce((acc, curr) => {
                                        return acc += (
                                            parseFloat(curr.price) * parseFloat(curr.quantity) * (1 + parseFloat(curr.iva) / 100)
                                        )
                                    }, 0).toFixed(2)
                                }}
                            </span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Correo Proveedor:
                            <span class="font-normal">{{ selectedPurchase.provider.email }}</span>
                        </p>
                        <p class="text-stone-700 text-sm font-bold">Teléfono Proveedor:
                            <span class="font-normal">{{ selectedPurchase.provider.phone }}</span>
                        </p>
                    </div>
                </div>

                <Line orientation="horizontal" class="bg-stone-200" />

                <!-- Purchased Products -->
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

                        <el-table-column prop="price" label="Precio Unitario" width="120">
                            <template #default="scope">
                                ${{ scope.row.price }}
                            </template>
                        </el-table-column>

                        <el-table-column prop="quantity" label="Cantidad" width="120" />

                        <el-table-column prop="iva" label="IVA" width="120">
                            <template #default="scope">
                                {{ scope.row.iva }}%
                            </template>
                        </el-table-column>

                        <el-table-column label="Subtotal" width="120">
                            <template #default="scope">
                                ${{ (scope.row.price * scope.row.quantity + (scope.row.price * scope.row.quantity *
                                    scope.row.iva / 100)
                                ).toFixed(2) }}
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="flex justify-end">
                        <p class="text-stone-700 text-lg font-medium">Total: ${{
                            selectedPurchase.products.reduce((acc, curr) => {
                                return acc + (curr.price * curr.quantity) + (curr.price * curr.quantity * curr.iva / 100)
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
                    <!-- Add Payment -->
                    <el-button class="!ml-0" type="primary" :disabled="fetchingModal" @click="toggleAddPaymentModal">
                        Agregar Pago
                    </el-button>
                </div>
            </template>
        </el-dialog>

        <!-- Add Payment Modal -->
        <el-dialog v-model="showAddPaymentModal" title="Agregar Pago" width="80%" :fullscreen="fullScreenModals">
            <Line orientation="horizontal" class="bg-stone-200" />
            <div class="w-full flex flex-col gap-4 mt-4" v-loading="fetchingModal">
                <el-form :model="newPayment" class="p-4">
                    <el-form-item label="Método de Pago">
                        <!-- Select Payment Method -->
                        <el-select v-model="newPayment.methodId" placeholder="Seleccione un método de pago"
                            style="width: 100%" remote :remote-method="getPaymentMethods" :loading="gettingPayments"
                            filterable>
                            <el-option v-for="item in paymentMethods" :key="item.value" :label="item.name"
                                :value="item.id" />
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Monto" class="w-[200px]">
                        <el-input-number v-model="newPayment.amount" controls-position="right" style="width: 100%"
                            :precision="2" :min="0.01" :max="9999999999.99">
                            <template #prefix>
                                <span>$</span>
                            </template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item label="Fecha de Pago">
                        <el-date-picker v-model="newPayment.date" type="date" placeholder="Seleccione una fecha"
                            style="width: 100%" />
                    </el-form-item>

                    <el-form-item label="Número de referencia">
                        <el-input v-model="newPayment.reference" placeholder="Ingrese el número de referencia"
                            style="width: 100%" />
                    </el-form-item>

                </el-form>
            </div>
            <template #footer>
                <div class="dialog-footer flex justify-end gap-4">
                    <el-button @click="showAddPaymentModal = false">Cancelar</el-button>
                    <el-button type="primary" @click="handleSavePayment" :loading="savingPayment">Guardar</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Line from '@/components/Line.vue';
import ThemeButton from '../ThemeButton.vue';
import { confirmation, successNotification, errorNotification } from '@/utils/feedback';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import { ElMessageBox } from 'element-plus';
import { useFullScreenModals } from '@/composables/fullScreenModals';

const fetchingModal = ref(false);
const fetchingPurchases = ref(false);
const selectedPurchase = ref(null);
const isSelectedPurchase = ref(false);

const purchases = ref([]);
const providers = ref([]);

const selectedPurchasePaid = computed(() => {
    return ((
        (selectedPurchase.value.payments.reduce((acc, curr) => {
            return acc += parseFloat(curr.amount)
        }, 0).toFixed(2)) // SUMA DE TODOS LOS PAGOS
        -
        (selectedPurchase.value.products.reduce((acc, curr) => {
            return acc += (parseFloat(curr.price) * parseFloat(curr.quantity) * (1 + parseFloat(curr.iva) / 100))
        }, 0).toFixed(2)) // SUMA DEL TOTAL DE TODOS LOS PRODUCTOS
    ) >= 0)
})

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
    search: ''
});

const paginationConfig = ref({
    rowsPerPage: 10,
    totalRows: 100
});

const handlePurchaseClick = (purchase) => {
    // Obtener detalles de la compra clickeada y mostrar el modal
    fetchingModal.value = true;
    axios.post(import.meta.env.VITE_API_URL + '/purchases/details', {
        id: purchase.id_compra
    }, {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    }).then(response => {
        selectedPurchase.value = response.data.response.purchase;
        isSelectedPurchase.value = true;
    }).catch(error => {
        console.log(error)
        handleRequestError(error);
    }).finally(() => {
        fetchingModal.value = false;
    });
}

const getPurchases = () => {
    fetchingPurchases.value = true;
    let data = {
        filters: {
            date: {
                from: purchasesFilter.value.date.from ? purchasesFilter.value.date.from.toISOString().split('T')[0] : null,
                to: purchasesFilter.value.date.to ? purchasesFilter.value.date.to.toISOString().split('T')[0] : null
            },
            value: {
                from: purchasesFilter.value.value.from,
                to: purchasesFilter.value.value.to
            },
            provider: purchasesFilter.value.provider ? purchasesFilter.value.provider : null
        }
    }

    axios.post(import.meta.env.VITE_API_URL + '/purchases', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        purchases.value = response.data.response.purchases;
        paginationConfig.value.totalRows = response.data.response.count;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingPurchases.value = false;
    });
}

const getProviders = () => {
    axios.post(import.meta.env.VITE_API_URL + '/providers', {}, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        providers.value = response.data.response.providers;
    }).catch(error => {
        handleRequestError(error);
    });
}

const clearFilters = () => {
    purchasesFilter.value = {
        date: {
            from: null,
            to: null
        },
        provider: '',
        value: {
            from: null,
            to: null
        },
        search: ''
    };
    getPurchases();
}

const handlePageChange = (page) => {
};

const handleCancelPurchase = () => {
    confirmation("Alerta", "¿Está seguro de que desea eliminar PERMANENTEMENTE la compra?",
        () => {
            ElMessageBox.prompt('Si realmente desea eliminar la compra PERMANENTEMENTE, escriba "Eliminar"', 'Si la compra es eliminada, se eliminarán también todos los pagos asociados a la misma', {
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                inputPattern: /Eliminar/,
                inputErrorMessage: 'La entrada no coincide con la expresión'
            }).then(({ value }) => {
                fetchingModal.value = true;
                axios.post(import.meta.env.VITE_API_URL + '/purchases/delete', {
                    id: selectedPurchase.value.id
                }, {
                    headers: {
                        'Authorization': localStorage.getItem('token')
                    }
                }).then(response => {
                    successNotification("Compra eliminada", "La compra ha sido eliminada correctamente");
                    getPurchases();
                }).catch(error => {
                    handleRequestError(error);
                }).finally(() => {
                    isSelectedPurchase.value = false;
                    selectedPurchase.value = null;
                    fetchingModal.value = false;
                });
            }).catch(() => { });
        },
        () => { }
    )
}

// Add Payment Modal Implementation

const showAddPaymentModal = ref(false);
const savingPayment = ref(false);
const gettingPayments = ref(false);

const newPayment = ref({
    amount: null,
    date: null,
    reference: null,
    methodId: null,
})

const toggleAddPaymentModal = async () => {
    if (selectedPurchasePaid.value) {
        ElMessageBox.confirm('El monto total ya ha sido cubierto', '¿Está seguro de que desea continuar?', {
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar',
            type: 'warning'
        }).then(() => {
            showAddPaymentModal.value = !showAddPaymentModal.value;
        }).catch(() => { return });
    } else {
        showAddPaymentModal.value = !showAddPaymentModal.value;
    }

}

const handleSavePayment = () => {
    // validate
    if (!newPayment.value.amount || newPayment.value.amount <= 0) {
        errorNotification("Error de validación: El monto debe ser mayor a 0");
        return;
    }

    if (!newPayment.value.date) {
        errorNotification("Error de validación: La fecha es requerida");
        return;
    }

    if (!newPayment.value.methodId) {
        errorNotification("Error de validación: El método de pago es requerido");
        return;
    }

    savingPayment.value = true;

    let data = {
        purchaseId: selectedPurchase.value.id,
        amount: newPayment.value.amount,
        date: newPayment.value.date.toISOString().split('T')[0],
        reference: newPayment.value.reference,
        methodId: newPayment.value.methodId,
    }

    axios.post(import.meta.env.VITE_API_URL + '/purchases/add-payment', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        successNotification("Pago guardado", "El pago ha sido guardado correctamente");
        showAddPaymentModal.value = false;
        getPurchases();
        newPayment.value = {
            amount: null,
            date: null,
            reference: null,
            methodId: null,
        };
        isSelectedPurchase.value = false;
        selectedPurchase.value = null;

    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        savingPayment.value = false;
    });

}

const paymentMethods = ref([]);
const getPaymentMethods = () => {
    gettingPayments.value = true;

    let data = {
        filters: {
            deleted: 0
        }
    }

    axios.post(import.meta.env.VITE_API_URL + '/paymentMethods', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        paymentMethods.value = response.data.response.paymentMethods;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        gettingPayments.value = false;
    });
}

onMounted(() => {
    getPurchases();
})

const {
    fullScreenModals
} = useFullScreenModals();
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

.el-select__popper {
    z-index: 10000 !important;
}
</style>
