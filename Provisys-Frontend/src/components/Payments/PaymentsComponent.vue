<script setup>
import { useFullScreenModals } from '@/composables/fullScreenModals';
import { onMounted, ref } from 'vue';
import { DollarSign, Search } from 'lucide-vue-next';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const payments = ref([]);

const gettingPayments = ref(false);

const search = ref('');

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

onMounted(() => {
    getPayments();
})
</script>

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
                class="flex flex-col sm:flex-row items-center justify-between p-4 border rounded-lg"
                :class="payment.deleted ? 'border-red-200 bg-red-100' : 'border-green-200 bg-green-100'">

                <!-- Payment Details -->
                <div class="flex items-center gap-4">
                    <DollarSign class="w-8 h-8 text-emerald-600" />
                    <div>
                        <h3 class="font-semibold text-gray-800">Monto: {{ payment.amount }}</h3>
                        <p class="text-sm text-gray-600">Fecha: {{ payment.date }}</p>
                        <p class="text-sm text-gray-600">
                            {{ payment.orderId ? 'Orden #' + payment.orderId :
                                payment.purchaseId ? 'Compra #' + payment.purchaseId :
                                    'Nota #' + payment.noteId }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-if="payments.length === 0" class="border-l-4 border-red-400 bg-red-100 rounded-lg p-4">
                <p class="text-gray-600 text-center font-medium">No hay pagos registrados.</p>
            </div>
        </div>
    </div>
</template>
