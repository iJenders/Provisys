<script setup>
import { Search } from 'lucide-vue-next';
import Line from '@/components/Line.vue';
import ThemeButton from '../ThemeButton.vue';
import { ref } from 'vue';
import ProductDetailsModal from './ProductDetailsModal.vue';
import ProductAddModal from './ProductAddModal.vue';

const fetchingProducts = ref(false);

const productsFilter = ref({
    searcher: '',
    category: '',
    provider: '',
    price: {
        min: null,
        max: null
    }
});

const products = ref([
    {
        id: 101,
        name: "Laptop HP Pavilion",
        description: "Laptop ideal para trabajo y estudio, con procesador Intel Core i7 y 16GB de RAM.",
        currentPrice: 850.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 1, name: "Tech Solutions C.A.", phone: "4121234567", email: "info@techsolutions.com", address: "Av. Principal, Torre B, Piso 3, Ofic. 10" }
    },
    {
        id: 201,
        name: "Teclado Mecánico RGB",
        description: "Teclado gaming con switches Cherry MX, retroiluminación RGB personalizable y diseño ergonómico.",
        currentPrice: 75.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 2, name: "Gamer Zone Corp.", phone: "4249876543", email: "contacto@gamerzone.com", address: "Centro Comercial Millennium, Nivel Feria" }
    },
    {
        id: 202,
        name: "Mouse Ergonómico Inalámbrico",
        description: "Mouse inalámbrico con diseño ergonómico para reducir la fatiga en largas jornadas de uso, sensor de alta precisión.",
        currentPrice: 30.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 2, name: "Gamer Zone Corp.", phone: "4249876543", email: "contacto@gamerzone.com", address: "Centro Comercial Millennium, Nivel Feria" }
    },
    {
        id: 301,
        name: "Audífonos Bluetooth Noise Cancelling",
        description: "Audífonos con tecnología de cancelación de ruido activa, sonido de alta fidelidad y batería de larga duración.",
        currentPrice: 60.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 3, name: "Sound Masters S.A.", phone: "4165551122", email: "ventas@soundmasters.com", address: "Calle El Sol, Edif. Armonía, PB" }
    },
    {
        id: 302,
        name: "Cable USB-C a USB-C",
        description: "Cable de carga rápida (hasta 100W) y transferencia de datos (USB 3.1 Gen 2) de 1.5 metros de longitud.",
        currentPrice: 15.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 3, name: "Sound Masters S.A.", phone: "4165551122", email: "ventas@soundmasters.com", address: "Calle El Sol, Edif. Armonía, PB" }
    },
    {
        id: 401,
        name: "Monitor Curvo 27 pulgadas",
        description: "Monitor curvo Full HD de 27 pulgadas, tasa de refresco de 144Hz y tiempo de respuesta de 1ms, ideal para gaming.",
        currentPrice: 250.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 1, name: "Tech Solutions C.A.", phone: "4121234567", email: "info@techsolutions.com", address: "Av. Principal, Torre B, Piso 3, Ofic. 10" }
    },
    {
        id: 402,
        name: "Webcam Full HD",
        description: "Webcam con resolución Full HD (1080p) a 30fps, micrófono integrado con reducción de ruido y clip universal.",
        currentPrice: 40.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 1, name: "Tech Solutions C.A.", phone: "4121234567", email: "info@techsolutions.com", address: "Av. Principal, Torre B, Piso 3, Ofic. 10" }
    },
    {
        id: 501,
        name: "Router Wi-Fi 6 Doble Banda",
        description: "Router de última generación con tecnología Wi-Fi 6, doble banda (2.4GHz y 5GHz) para velocidades ultra rápidas.",
        currentPrice: 90.00,
        category: { id: 1, name: "Electrónica", description: "Dispositivos electrónicos de consumo y profesionales." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 4, name: "NetConnect S.A.", phone: "4147778899", email: "support@netconnect.com", address: "Calle Los Robles, Local 7" }
    },
    {
        id: 601,
        name: "Cafetera Expresso Automática",
        description: "Cafetera automática para preparar espressos, cappuccinos y lattes con solo tocar un botón.",
        currentPrice: 300.00,
        category: { id: 2, name: "Electrodomésticos", description: "Aparatos eléctricos para el hogar." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 5, name: "Home Comfort C.A.", phone: "4161112233", email: "ventas@homecomfort.com", address: "Av. Francisco de Miranda, Centro Plaza" }
    },
    {
        id: 602,
        name: "Licuadora de Alta Potencia",
        description: "Licuadora con motor de 1200W, cuchillas de acero inoxidable y jarra de vidrio de 1.5 litros.",
        currentPrice: 80.00,
        category: { id: 2, name: "Electrodomésticos", description: "Aparatos eléctricos para el hogar." },
        currentIva: { id: 1, name: "IVA General", iva: 16 },
        provider: { id: 5, name: "Home Comfort C.A.", phone: "4161112233", email: "ventas@homecomfort.com", address: "Av. Francisco de Miranda, Centro Plaza" }
    }
]);

const warehouses = ref([
    { id: 1, name: "Almacén 1", description: "Almacén principal de la empresa." },
    { id: 2, name: "Refrigeración", description: "Almacén de productos de refrigeración." },
    { id: 3, name: "Electrónica", description: "Almacén de productos electrónicos." },
    { id: 4, name: "Electrodomésticos", description: "Almacén de electrodomésticos." },
    { id: 5, name: "Gaming", description: "Almacén de productos gaming." }
]);

const paginationConfig = ref({
    page: 1,
    rowsPerPage: 1,
    totalRows: products.value.length,
});

const selectedProduct = ref(null);
const isSelectedProduct = ref(selectedProduct.value !== null)
const addingProduct = ref(false);

const handleProductClick = (product) => {
    selectedProduct.value = { ...product }
    selectedProduct.value.image = 'https://picsum.photos/512/512?random=' + product.id; // Simulate image URL
    selectedProduct.value.compatibleWarehouses = warehouses.value.filter(warehouse => Math.random() > 0.5); // Simulate compatible warehouses

    isSelectedProduct.value = true;
}

const handleCloseDetailsModal = () => {
    selectedProduct.value = null;
    isSelectedProduct.value = false;
}

const handleCloseAddingModal = () => {
    addingProduct.value = false;
}

const handlePageChange = (page) => {
    console.log(page)
    fetchingProducts.value = true;
    setTimeout(() => {
        fetchingProducts.value = false;
    }, 1000);
}
</script>

<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <!-- Products Content -->
        <h1 class="text-stone-700 font-medium">Listado de Productos</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col lg:flex-row gap-4" v-loading="fetchingProducts">
            <!-- Filters -->
            <div class="w-full lg:w-[250px] shrink-0 flex flex-col gap-3 lg:border-stone-200 lg:border-r-[2px] lg:pr-4">
                <p class="text-xl font-medium text-stone-700">Filtrar</p>
                <div class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-1 gap-x-6 gap-y-2">
                    <!-- Category Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Categoría:</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <el-input style="width: 100%;" v-model="productsFilter.category"
                                    placeholder="Buscar Categoría..." :prefix-icon="Search" />
                            </div>
                        </div>
                    </div>

                    <!-- Provider Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Proveedor:</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <el-input style="width: 100%;" v-model="productsFilter.category"
                                    placeholder="Buscar Proveedor..." :prefix-icon="Search" />
                            </div>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="w-full flex flex-col gap-1">
                        <p class="text-stone-700 text-sm font-medium">Precio</p>
                        <div class="w-full flex flex-col pl-6 gap-1">
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Min:</p>
                                <el-input-number v-model="productsFilter.price.from" controls-position="right"
                                    style="width: 100%;" :precision="2" :min="0.01" :max="9999999999.99">
                                    <template #prefix>
                                        <span>$</span>
                                    </template>
                                </el-input-number>
                            </div>
                            <div class="w-full flex items-center gap-1">
                                <p class="w-[34px] shrink-0 text-stone-700 text-sm font-medium">Max:</p>
                                <el-input-number v-model="productsFilter.price.to" controls-position="right"
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

            <!-- Products -->
            <div class="w-full flex flex-col gap-4 overflow-x-hidden">
                <!-- Searcher -->
                <div class="w-full flex items-center gap-4">
                    <el-input style="width: 100%;" v-model="productsFilter.searcher" placeholder="Buscar Producto..."
                        :prefix-icon="Search" />
                    <!--Add product button-->
                    <el-button class="!rounded-full !py-1 !px-2" type="success" @click="addingProduct = true">
                        Añadir Producto
                    </el-button>
                </div>

                <!-- Table -->
                <el-table class="pointer-rows" :data="products" stripe border style="width:100%" max-height="500"
                    @row-click="(e) => { handleProductClick(e) }">
                    <el-table-column prop="id" label="Id" min-width="60" />
                    <el-table-column prop="name" label="Nombre" width="120" fixed />
                    <el-table-column prop="description" label="Descripción" min-width="240" />
                    <el-table-column prop="currentPrice" label="Precio Actual" min-width="100">
                        <template #default="scope">
                            ${{ scope.row.currentPrice.toFixed(2) }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="category.name" label="Categoría" min-width="160" />
                    <el-table-column prop="provider.name" label="Proveedor" min-width="180" />
                </el-table>

                <!-- Pagination -->
                <div class="w-full flex justify-center mt-4">
                    <el-pagination layout="prev, pager, next" background :page-size="paginationConfig.rowsPerPage"
                        :total="paginationConfig.totalRows" :disabled="fetchingProducts" @change="handlePageChange" />
                </div>

            </div>
        </div>

        <!-- Product Details Modal -->
        <ProductDetailsModal v-model="isSelectedProduct" :selectedProduct="selectedProduct"
            @closeModal="handleCloseDetailsModal" />

        <!-- New Product Modal -->
        <ProductAddModal v-model="addingProduct" @closeModal="handleCloseAddingModal" />
    </div>
</template>

<style>
.pointer-rows .el-table__row {
    cursor: pointer;
}

.el-overlay {
    z-index: 10000 !important;
}

.el-select__popper {
    z-index: 10000 !important;
}
</style>