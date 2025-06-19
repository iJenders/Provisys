<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold text-stone-700 mb-6">Reportes del Sistema</h1>
        <div class="mt-8">
            <el-card>
                <template #header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Filtros de Reporte</h3>
                    </div>
                </template>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <el-select v-model="reportType" placeholder="Seleccionar tipo" class="w-full">
                        <el-option label="Estado actual de inventario" value="1" />
                        <el-option label="Proveedores según volumen de compras" value="2" />
                        <el-option label="Ingresos y egresos" value="3" />
                        <el-option label="Pedidos en espera" value="4" />
                        <el-option label="Entradas de inventario" value="5" />
                        <el-option label="Salidas de inventario" value="6" />
                    </el-select>

                    <el-form-item label="Fecha Inicio">
                        <el-date-picker v-model="startDate" type="date" placeholder="Seleccionar fecha" class="w-full"
                            @change="() => {
                                if (startDate > endDate) endDate = startDate
                            }" />
                    </el-form-item>

                    <el-form-item label="Fecha Fin">
                        <el-date-picker v-model="endDate" type="date" placeholder="Seleccionar fecha" class="w-full"
                            :disabled-date="(date) => { return date < startDate }" />
                    </el-form-item>
                </div>

                <div class="flex justify-end mt-4">
                    <el-button :loading="loadingReport" type="primary" @click="generateReport()">
                        {{ loadingReport ? 'Generando...' : 'Generar' }}
                    </el-button>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { TrendingUp, ShoppingBag, Users } from 'lucide-vue-next'

const loadingReport = ref(false)
const reportType = ref('')
const startDate = ref(new Date())
const endDate = ref(new Date())

const generateReport = () => {
    if (reportType.value === '') {
        alert('Selecciona un tipo de reporte')
        return
    }

    let link = import.meta.env.VITE_API_URL + '/reports/' + reportType.value

    try {
        link += '?from=' + startDate.value.toISOString().split('T')[0]
    } catch (error) {
    }

    try {
        link += '&to=' + endDate.value.toISOString().split('T')[0]
    } catch (error) {
    }

    loadingReport.value = true
    setTimeout(() => {
        loadingReport.value = false
        window.open(link, '_blank');
    }, 1000)
}
</script>

<style scoped>
.el-card {
    border-radius: 0.5rem;
}
</style>
