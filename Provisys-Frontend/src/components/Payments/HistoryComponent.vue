<script setup>
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const paymentHistory = ref([]);
const loading = ref(false);

const getPayments = () => {
  loading.value = true;

  axios.post(import.meta.env.VITE_API_URL + '/payments/history', {}, {
    headers: {
      'Authorization': localStorage.getItem('token')
    }
  }).then(response => {
    paymentHistory.value = response.data.response.payments;
  }).catch(error => {
    handleRequestError(error);
  }).finally(() => {
    loading.value = false;
  })
}

onMounted(() => {
  getPayments();
})
</script>
<template>
  <div class="w-full flex  p-[20px]">
    <el-table :data="paymentHistory" class="w-full" v-loading="loading">
      <el-table-column prop="id" label="ID" min-width="80" />
      <el-table-column prop="date" label="Fecha" min-width="180">
        <template #default="scope">
          {{ scope.row.date.split(' ')[0] }}
        </template>
      </el-table-column>
      <el-table-column prop="amount" label="Monto Cuota" min-width="120">
        <template #default="scope">
          ${{ scope.row.amount }}
        </template>
      </el-table-column>
      <el-table-column prop="payment" label="Monto Pago Total" min-width="200">
        <template #default="scope">
          ${{ scope.row.payment.amount }}
        </template>
      </el-table-column>
      <el-table-column prop="reference" label="Referencia" min-width="150" />
      <el-table-column prop="paymentMethod" label="Método de Pago" min-width="200" />
      <el-table-column prop="verified" label="Estado" width="120">
        <template #default="scope">
          <el-tag :type="scope.row.verified ? 'success' : 'warning'">
            {{ scope.row.verified ? 'Verificado' : 'Pendiente' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="payment" label="Fuente" min-width="200">
        <template #default="scope">
          <span class="font-bold">
            {{ scope.row.payment.orderId ?
              'Orden #' + scope.row.payment.orderId :
              'Compra #' + scope.row.payment.purchaseId }}
          </span>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>