<script setup>
import { ref } from 'vue';
import { confirmation, errorNotification, successNotification } from '@/utils/feedback';
import { useFullScreenModals } from '@/composables/fullScreenModals';
import Line from '@/components/Line.vue';

const { fullScreenModals } = useFullScreenModals();

defineProps([
    'selectedProduct'
]);

defineEmits(['closeModal']);

const fetchingModal = ref(false);
const fetchingProviders = ref(false);
const fetchingCategories = ref(false);
const fetchingIVA = ref(false);
const fetchingWarehouses = ref(false);

const providers = ref([
    { id: 1, name: "Tech Solutions C.A.", phone: "4121234567", email: "info@techsolutions.com", address: "Av. Principal, Torre B, Piso 3, Ofic. 10" },
    { id: 2, name: "Gamer Zone Corp.", phone: "4249876543", email: "contacto@gamerzone.com", address: "Centro Comercial Millennium, Nivel Feria" },
    { id: 3, name: "Sound Masters S.A.", phone: "4165551122", email: "ventas@soundmasters.com", address: "Calle El Sol, Edif. Armonía, PB" },
    { id: 4, name: "NetConnect S.A.", phone: "4147778899", email: "support@netconnect.com", address: "Calle Los Robles, Local 7" },
    { id: 5, name: "Home Comfort C.A.", phone: "4161112233", email: "ventas@homecomfort.com", address: "Av. Francisco de Miranda, Centro Plaza" }
])

const categories = ref([
    { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
    { id: 2, name: "Electrodomésticos", description: "Aparatos eléctricos para el hogar." }
]);

const IVAs = ref([
    { id: 1, name: "IVA General", iva: 16 },
    { id: 2, name: "Exento", iva: 0 },
]);

const warehouses = ref([
    { id: 1, name: "Almacén 1", description: "Almacén principal de la empresa." },
    { id: 2, name: "Refrigeración", description: "Almacén de productos de refrigeración." },
    { id: 3, name: "Electrónica", description: "Almacén de productos electrónicos." },
    { id: 4, name: "Electrodomésticos", description: "Almacén de electrodomésticos." },
    { id: 5, name: "Gaming", description: "Almacén de productos gaming." }
]);

const handleProviderSearch = (value) => {
    // Implementar lógica para obtener proveedores basados en la búsqueda

    fetchingProviders.value = true;
    setTimeout(() => {
        fetchingProviders.value = false;
    }, 500);
}

const handleCategorySearch = (value) => {
    // Implementar lógica para obtener categorías basadas en la búsqueda
    fetchingCategories.value = true;
    setTimeout(() => {
        fetchingCategories.value = false;
    }, 500);
}

const handleIvaSearch = (value) => {
    // Implementar lógica para obtener IVAs basados en la búsqueda
    fetchingIVA.value = true;
    setTimeout(() => {
        fetchingIVA.value = false;
    }, 500);
}

const handleWarehouseSearch = (value) => {
    // Implementar lógica para obtener almacenes basados en la búsqueda
    fetchingWarehouses.value = true;
    setTimeout(() => {
        fetchingWarehouses.value = false;
    }, 500);
}

const handleDeleteProduct = (product) => {
    confirmation("Alerta", "¿Estás seguro de que deseas eliminar este producto?",
        () => {
            fetchingModal.value = true;
            setTimeout(() => {
                fetchingModal.value = false;
                // Simular eliminación del producto
                if (Math.random() > 0.5) {
                    successNotification("Producto eliminado correctamente.");
                } else {
                    errorNotification("No se pudo eliminar el producto. Inténtalo de nuevo más tarde.");
                }
            }, 1000);
        },
    )
}

const handleEditProduct = (product) => {
    confirmation("Alerta", "¿Estás seguro de que deseas guardar los cambios de este producto?",
        () => {
            // Simular eliminación del producto
            fetchingModal.value = true;
            setTimeout(() => {
                fetchingModal.value = false;
                if (Math.random() > 0.5) {
                    successNotification("Producto actualizado correctamente.");
                } else {
                    errorNotification("No se pudo actualizar el producto. Inténtalo de nuevo más tarde.");
                }
            }, 1000);
        },
    )
}
</script>

<template>
    <el-dialog title="Detalles del Producto" width="80%" @close="() => { $emit('closeModal') }"
        :fullscreen="fullScreenModals" class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <div v-if="selectedProduct" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
            <!-- Product Image -->
            <div
                class="w-full md:w-fit justify-center items-center   shrink-0 justify-center flex relative md:border-stone-200 md:border-r-[2px] md:pr-4">
                <img :src="selectedProduct.image" alt="Product Image"
                    class="object-cover w-[300px] h-[300px] object-cover rounded-lg shadow-lg shadow-stone-300" />
            </div>

            <Line orientation="horizontal" class="bg-stone-200 md:hidden" />

            <!-- Product Info -->
            <div class="w-full min-w-0 flex flex-col gap-4">
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Id:</p>
                    <p class="text-xl font-normal text-stone-600">{{ selectedProduct.id }}</p>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Nombre:</p>
                    <el-input v-model="selectedProduct.name" class="w-full" type="text"
                        placeholder="Nombre del Producto" />
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Descripción:</p>
                    <el-input v-model="selectedProduct.description" class="w-full" type="textarea"
                        placeholder="Descripción del Producto" />
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Proveedor:</p>
                    <el-select v-model="selectedProduct.provider" value-key="id" class="w-full"
                        placeholder="Seleccionar Proveedor" :loading="fetchingProviders" filterable remote
                        :remote-method="handleProviderSearch">
                        <el-option v-for="provider in providers" :key="provider.id" :label="provider.name"
                            :value="provider" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Precio Actual:</p>
                    <el-input-number v-model="selectedProduct.currentPrice" controls-position="right" :precision="2"
                        :min="0.01" :max="9999999999.99" class="!w-fit">
                        <template #prefix>
                            <span>$</span>
                        </template>
                    </el-input-number>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Categoría:</p>
                    <el-select v-model="selectedProduct.category" value-key="id" class="w-full"
                        placeholder="Seleccionar Categoría" :loading="fetchingCategories" filterable remote
                        :remote-method="handleCategorySearch">
                        <el-option v-for="category in categories" :key="category.id" :label="category.name"
                            :value="category" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">IVA actual:</p>
                    <el-select v-model="selectedProduct.currentIva" value-key="id" class="w-full h-fit"
                        placeholder="Seleccionar IVA Actual" :loading="fetchingIVA" filterable remote
                        :remote-method="handleIvaSearch">
                        <el-option v-for="iva in IVAs" :key="iva.id" :label="`${iva.name} (${iva.iva})%`"
                            :value="iva" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Almacenes Compatibles:</p>
                    <el-select v-model="selectedProduct.compatibleWarehouses" value-key="id" class="w-full h-fit"
                        placeholder="Seleccionar Almacenes Compatibles" multiple :loading="fetchingWarehouses"
                        filterable remote :remote-method="handleWarehouseSearch">
                        <el-option v-for="warehouse in warehouses" :key="warehouse.id" :label="warehouse.name"
                            :value="warehouse" />
                    </el-select>
                </div>
            </div>
        </div>

        <!-- Modal Buttons -->
        <template #footer>
            <div class="dialog-footer flex justify-end flex-wrap gap-4 gap-y-2">
                <el-button class="!ml-0" type="info" :disabled="fetchingModal" @click="() => { $emit('closeModal') }">
                    Cerrar
                </el-button>
                <el-button class="!ml-0" type="danger" :disabled="fetchingModal" @click="handleDeleteProduct">
                    Eliminar Producto
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleEditProduct">
                    Guardar Cambios
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>