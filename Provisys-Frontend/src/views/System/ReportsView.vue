<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold text-stone-700 mb-6">Reportes del Sistema</h1>
        <div class="mt-8">
            <el-card>
                <!-- Reportes Operacionales -->
                <h2 class="text-lg font-semibold pb-2">Reportes Operacionales</h2>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-x-6">
                    <el-select v-model="operationalReportType" placeholder="Seleccionar tipo" class="w-full">
                        <el-option label="Estado actual de inventario" value="1" />
                        <el-option label="Historial de pagos en compras y ventas" value="3" />
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

                    <el-button :loading="loadingReport" type="primary" @click="generateReport(operationalReportType)">
                        {{ loadingReport ? 'Generando...' : 'Generar' }}
                    </el-button>
                </div>
                <Line orientation="horizontal" class="bg-stone-200 my-4" />

                <!-- Reportes de Supervisión -->
                <h2 class="text-lg font-semibold pb-2">Reportes de Supervisión</h2>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-x-6">
                    <el-select v-model="supervisionReportType" placeholder="Seleccionar tipo" class="w-full">
                        <el-option label="Productos más vendidos" value="7" />
                        <el-option label="Productos menos vendidos" value="8" />
                        <el-option label="Pagos sin verificar" value="10" />
                        <el-option label="Cuotas sin verificar" value="9" />
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

                    <el-button :loading="loadingReport" type="primary" @click="generateReport(supervisionReportType)">
                        {{ loadingReport ? 'Generando...' : 'Generar' }}
                    </el-button>
                </div>
                <Line orientation="horizontal" class="bg-stone-200 my-4" />

                <!-- Reportes Gerenciales -->
                <h2 class="text-lg font-semibold pb-2">Reportes Gerenciales</h2>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-x-6">
                    <el-select v-model="gerencialReportType" placeholder="Seleccionar tipo" class="w-full">
                        <el-option label="Ranking de proveedores según volumen de compras" value="2" />
                        <el-option label="Flujo de Caja (Ingresos vs. Egresos)" value="11" />
                        <el-option label="Análisis de Rentabilidad por Categoría" value="12" />
                        <el-option label="Análisis de Rentabilidad por Fabricante" value="13" />
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

                    <el-button :loading="loadingReport" type="primary" @click="generateReport(gerencialReportType)">
                        {{ loadingReport ? 'Generando...' : 'Generar' }}
                    </el-button>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Line from '@/components/Line.vue'
const loadingReport = ref(false)
const operationalReportType = ref('')
const supervisionReportType = ref('')
const gerencialReportType = ref('')
const startDate = ref(new Date())
const endDate = ref(new Date())

const generateReport = (report) => {

    if (report === '') {
        alert('Selecciona un tipo de reporte')
        return
    }

    let link = import.meta.env.VITE_API_URL + '/reports/' + report

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
