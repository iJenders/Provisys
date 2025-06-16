<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center gap-4 flex-wrap">
            <h2 class="text-2xl font-bold text-gray-800">Pagos</h2>
        </div>

        <!-- Search -->
        <form class="flex items-center justify-end gap-4" @submit.prevent="getPayments">
            <el-input class="w-full" placeholder="Buscar..." v-model="search" />
            <el-button type="success" @click="getPayments" :loading="gettingPayments">
                <Search class="w-4 h-4 mr-2" />
                Aplicar Búsqueda
            </el-button>
        </form>

        <!-- Payments List -->
        <div class="grid gap-4" v-loading="gettingPayments">
            <div v-for="payment in payments" :key="payment.id"
                class="flex flex-col sm:flex-row items-center justify-between p-4 border rounded-lg gap-4"
                :class="!payment.verified || parseFloat(payment.amount) > parseFloat(payment.totalPaid) ? 'border-red-200 bg-red-100' : 'border-green-200 bg-green-100'">

                <!-- Payment Details -->
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <DollarSign class="w-8 h-8 text-emerald-600" />
                    <div>
                        <h3 class="font-semibold text-gray-800">Monto: ${{ payment.amount }}</h3>
                        <p class="text-sm text-gray-600">Fecha: {{ payment.date.split(' ')[0] }}</p>
                        <p class="text-sm text-gray-600">
                            {{ payment.orderId ? 'Pedido #' + payment.orderId :
                                'Compra #' + payment.purchaseId }}
                        </p>
                        <p class="text-sm text-gray-600 font-bold">
                            {{ payment.verified ? 'Verificado' : 'No Verificado' }}
                        </p>
                        <p class="text-sm text-gray-600 font-bold">
                            Pagado: ${{ payment.totalPaid ? payment.totalPaid : 0 }} / ${{ payment.amount }}
                        </p>
                    </div>
                </div>

                <!-- Payment Buttons -->
                <div class="flex items-center gap-2">
                    <ThemeButton @click="openDetailsModal(payment)"
                        class="bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-800 rounded-xl">
                        Ver Detalles
                    </ThemeButton>
                </div>
            </div>
            <div v-if="payments.length === 0" class="border-l-4 border-red-400 bg-red-100 rounded-lg p-4">
                <p class="text-gray-600 text-center font-medium">No hay pagos registrados.</p>
            </div>
        </div>

        <!-- Details Modal -->
        <el-dialog v-model="showDetailsModal" title="Detalles del Pago" width="80%" :fullscreen="fullScreenModals"
            class="rounded-lg" :loading="gettingDetails">
            <div v-if="selectedPayment" class="space-y-8 p-6 bg-white rounded-xl shadow-lg">
                <!-- Payment ID and Date -->
                <div class="flex justify-between items-center border-b border-emerald-200 pb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-1">
                            Pago ID: <span class="text-emerald-600">#{{ selectedPayment.id }}</span>
                        </h3>
                        <p class="text-gray-600 mt-1 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full"></span>
                            {{ selectedPayment.date.split(' ')[0] }}
                        </p>
                    </div>
                    <DollarSign class="w-12 h-12 text-emerald-600 p-2 bg-emerald-100 rounded-full" />
                </div>

                <!-- Origin Information -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700 text-xl flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Origen del Pago
                    </h4>
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-white p-6 rounded-xl shadow-sm border border-emerald-100">
                        <template v-if="selectedPayment.orderId">
                            <p class="text-gray-700 text-lg font-medium">Pedido #{{ selectedPayment.orderId }}</p>
                            <p class="text-gray-600 mt-2">Cliente: {{ selectedPayment.orderCustomer }}</p>
                        </template>
                        <template v-else>
                            <p class="text-gray-700 text-lg font-medium">Compra #{{ selectedPayment.purchaseId }}</p>
                        </template>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700 text-xl flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Detalles del Pago
                    </h4>
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-white p-6 rounded-xl shadow-sm border border-emerald-100">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 text-lg">Total del Crédito:</span>
                            <span class="font-bold text-2xl text-emerald-600">${{ selectedPayment.totalAmount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Installments -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700 text-xl flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Cuotas
                    </h4>

                    <!-- Installment List -->
                    <div class="grid divide-emerald-100 gap-4 pl-6 grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                        <div v-for="installment, index in selectedPayment.installments" :key="installment.id"
                            class="py-4 px-6 flex flex-col justify-between items-center transition-colors rounded-lg border-1"
                            :class="installment.verified ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">

                            <!-- Details -->
                            <div class="w-full">
                                <p class="flex justify-between items-center text-gray-700 font-medium text-lg">
                                    <span>Cuota #{{ index + 1 }}</span>
                                </p>
                                <ul class="list-disc list-inside text-gray-600">
                                    <li class="flex flex-col flex-wrap items-start text-gray-600 mt-1">
                                        <p class="font-semibold">Método:</p>
                                        <p class="pl-2">{{ installment.paymentMethod }}</p>
                                    </li>
                                    <li class="flex flex-col flex-wrap items-start text-gray-600">
                                        <p class="font-semibold">Fecha:</p>
                                        <p class="pl-2">{{ installment.date }}</p>
                                    </li>
                                    <li class="flex flex-col flex-wrap items-start text-gray-600">
                                        <p class="font-semibold">Referencia:</p>
                                        <p class="pl-2">{{ installment.reference }}</p>
                                    </li>
                                    <li class="flex flex-col flex-wrap items-start text-gray-600">
                                        <p class="font-semibold">Verificado:</p>
                                        <p class="font-bold pl-2"
                                            :class="installment.verified ? 'text-emerald-600' : 'text-red-600'">
                                            {{ installment.verified ? 'Sí' : 'No' }}
                                        </p>
                                    </li>
                                </ul>
                            </div>

                            <!-- Amount -->
                            <span class="font-bold text-xl"
                                :class="installment.verified ? 'text-emerald-600' : 'text-red-600'">
                                ${{ installment.amount }}
                            </span>

                            <!-- Buttons -->
                            <div class="flex gap-2 mt-2">
                                <!-- Verify Button -->
                                <ThemeButton @click="verifyInstallment(installment.id)"
                                    class="text-white font-semibold py-2 px-4 rounded-md transition-colors duration-100"
                                    :class="!installment.verified ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-orange-400 hover:bg-orange-500'">
                                    {{ !installment.verified ? "Verificar" : "Desverificar" }}
                                </ThemeButton>
                                <!-- Delete Button -->
                                <ThemeButton @click="deleteInstallment(installment.id)"
                                    class="bg-red-400 hover:bg-red-500 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-100">
                                    Eliminar
                                </ThemeButton>
                            </div>
                        </div>
                    </div>

                    <!-- Add Installment Button -->
                    <ThemeButton @click="showAddInstallmentModal"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-100 mt-4 w-full">
                        Agregar Cuota
                    </ThemeButton>

                    <!-- Total Amount -->
                    <div class="flex justify-between items-center p-4 rounded-lg mt-4"
                        :class="selectedPaymentTotallyPaid ? 'bg-emerald-100' : 'bg-red-100'">
                        <span class="text-gray-700 font-semibold text-lg">Total:</span>
                        <span class="font-bold text-2xl text-center"
                            :class="selectedPaymentTotallyPaid ? 'text-emerald-600' : 'text-red-600'">
                            ${{
                                parseFloat(selectedPayment.installments.reduce((total, installment) => total +
                                    parseFloat(installment.amount), 0)).toFixed(2)
                            }}
                            /
                            ${{ parseFloat(selectedPayment.totalAmount).toFixed(2) }}
                        </span>
                    </div>
                </div>

                <!-- Add Installment Modal -->
                <el-dialog v-model="addInstallmentModalVisible" title="Agregar Cuota" width="500px" draggable :fullscreen="fullScreenModals">
                    <div class="flex flex-col gap-4">
                        <!-- Amount Input -->
                        <div class="flex flex-col gap-2">
                            <label class="text-gray-700 font-semibold">Monto</label>
                            <input v-model="newInstallment.amount" type="number"
                                class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Ingrese el monto">
                        </div>

                        <!-- Payment Date Input -->
                        <div class="flex flex-col gap-2">
                            <label class="text-gray-700 font-semibold">Fecha de Pago</label>
                            <el-date-picker v-model="newInstallment.date" type="date"
                                class="w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Seleccione la fecha" format="DD/MM/YYYY" value-format="YYYY-MM-DD">
                            </el-date-picker>
                        </div>


                        <!-- Reference Input -->
                        <div class="flex flex-col gap-2">
                            <label class="text-gray-700 font-semibold">Referencia</label>
                            <input v-model="newInstallment.reference" type="text"
                                class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Ingrese la referencia">
                        </div>

                        <!-- Payment Method Select -->
                        <div class="flex flex-col gap-2">
                            <label class="text-gray-700 font-semibold">Método de Pago</label>
                            <el-select v-model="newInstallment.paymentMethod" filterable remote
                                :remote-method="getMethods" :loading="loadingMethods">
                                <el-option v-for="method in methods" :key="method.id" :label="method.name"
                                    :value="method.id">
                                    {{ method.name }}
                                </el-option>
                            </el-select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-2 mt-4">
                            <ThemeButton @click="addInstallmentModalVisible = false"
                                class="bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-100">
                                Cancelar
                            </ThemeButton>
                            <ThemeButton @click="addInstallment"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-100">
                                Agregar
                            </ThemeButton>
                        </div>
                    </div>
                </el-dialog>
            </div>
        </el-dialog>
    </div>
</template>

<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref, computed } from 'vue';
import { DollarSign, Search } from 'lucide-vue-next';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import ThemeButton from '../ThemeButton.vue';
import { ElMessageBox, ElNotification } from 'element-plus';

const payments = ref([]);

const gettingPayments = ref(false);
const gettingDetails = ref(false);

const selectedPayment = ref({
    id: 1, //
    date: '2024-01-15 10:30:00', //
    orderId: 123, //
    orderCustomer: 'John Doe', //
    totalAmount: 1500, //
    installments: [
        {
            id: 1,
            date: '2024-01-15 10:30:00',
            amount: 700,
            reference: 'REF123',
            verified: true,
            paymentMethod: 'Credit Card',
        },
        {
            id: 2,
            date: '2024-01-15 10:30:00',
            amount: 400,
            reference: 'REF123',
            verified: false,
            paymentMethod: 'Credit Card',
        },
        {
            id: 3,
            date: '2024-01-15 10:30:00',
            amount: 400,
            reference: 'REF123',
            verified: true,
            paymentMethod: 'Credit Card',
        },
    ]
});

const showDetailsModal = ref(false);

const search = ref('');

const selectedPaymentTotallyPaid = computed(() => {
    return parseFloat(selectedPayment.value.installments.reduce((total, installment) =>
        total + parseFloat(installment.amount), 0)) >=
        parseFloat(selectedPayment.value.totalAmount) &&
        selectedPayment.value.installments.every(installment => installment.verified);
})

const getPayments = () => {
    gettingPayments.value = true;

    let data = {
        search: search.value
    }

    axios.post(import.meta.env.VITE_API_URL + '/payments', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        payments.value = response.data.response.payments;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        gettingPayments.value = false;
    });
}

const openDetailsModal = (payment) => {
    gettingDetails.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/payments/details', {
        id: payment.id
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        selectedPayment.value = response.data.response.payment;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        gettingDetails.value = false;
    })

    showDetailsModal.value = true;
}

const verifyInstallment = (id) => {
    gettingDetails.value = true;

    ElMessageBox.confirm(
        '¿Estás seguro de que quieres verificar esta cuota?',
        'Verificación de cuota',
        {
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar',
            type: 'warning',
        }
    ).then(() => {
        axios.post(import.meta.env.VITE_API_URL + '/payments/verify-installment', {
            id: id
        }, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            getPayments();
            openDetailsModal(selectedPayment.value);
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            gettingDetails.value = false;
        })
    }).catch(() => { })

}

const deleteInstallment = (id) => {
    gettingDetails.value = true;

    ElMessageBox.confirm(
        '¿Estás seguro de que quieres eliminar esta cuota?',
        'Verificación de cuota',
        {
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar',
            type: 'warning',
        }
    ).then(() => {
        ElMessageBox.prompt('¡La nota se eliminará PERMANENTEMENTE!. Escribe "Eliminar" para confirmar', '¿Estás seguro?', {
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
            inputPattern: /^Eliminar$/,
        }).then(() => {
            axios.post(import.meta.env.VITE_API_URL + '/payments/delete-installment', {
                id: id
            }, {
                headers: {
                    'Authorization': localStorage.getItem('token')
                }
            }).then(response => {
                getPayments();
                openDetailsModal(selectedPayment.value);
            }).catch(error => {
                handleRequestError(error);
            }).finally(() => {
                gettingDetails.value = false;
            })
        })
    })
}

// New installment implementation
const addInstallmentModalVisible = ref(false);
const showAddInstallmentModal = () => {
    addInstallmentModalVisible.value = true;
}

const newInstallment = ref({
    amount: null,
    date: null,
    reference: null,
    paymentMethod: null
});

const loadingMethods = ref(false);
const methods = ref([]);

const addInstallment = () => {
    gettingDetails.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/payments/create-installment', {
        payment_id: selectedPayment.value.id,
        amount: newInstallment.value.amount,
        date: newInstallment.value.date,
        reference: newInstallment.value.reference,
        payment_method: newInstallment.value.paymentMethod
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        ElNotification({
            title: 'Éxito',
            message: 'La cuota se ha agregado correctamente',
            type: 'success',
            offset: 80,
            zIndex: 10000
        })
        getPayments();
        openDetailsModal(selectedPayment.value);
        newInstallment.value = {
            amount: null,
            date: null,
            reference: null,
            paymentMethod: null
        };
        addInstallmentModalVisible.value = false;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        gettingDetails.value = false;
    });
}

const getMethods = () => {
    loadingMethods.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/paymentMethods', {}, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        methods.value = response.data.response.paymentMethods;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        loadingMethods.value = false;
    });
}


const { fullScreenModals } = useFullScreenModals();

onMounted(() => {
    getPayments();
})
</script>

<style>
.el-select__popper {
    z-index: 10000 !important;
}
</style>