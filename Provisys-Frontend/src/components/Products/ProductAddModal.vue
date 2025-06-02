<script setup>
import { ref } from 'vue';
import { confirmation, errorNotification, successNotification } from '@/utils/feedback';
import { useFullScreenModals } from '@/composables/fullScreenModals';
import Line from '@/components/Line.vue';
import axios from 'axios';

const { fullScreenModals } = useFullScreenModals();

const emit = defineEmits(['closeModal']);

const newProduct = ref({
    name: '',
    description: '',
    price: '',
    stock: '',
    provider: { id: null, name: '', phone: '', email: '', address: '' },
    category: { id: null, name: '', description: '' },
    iva: { id: null, name: '', iva: 0 },
    warehouse: { id: null, name: '', description: '' },
    image: null,
});
const productImage = ref(null);

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

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            newProduct.value.image = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const validatedFields = () => {
    // Validaciones
}

const handleAddProduct = () => {
    confirmation("Alerta", "¿Estás seguro de que deseas guardar este producto?",
        () => {
            // Simular eliminación del producto
            fetchingModal.value = true;

            // Validar todos los campos necesarios
            if (validatedFields()) {
                errorNotification("Por favor, completa todos los campos obligatorios.");
                fetchingModal.value = false;
                return;
            }

            let formData = new FormData();
            // Eliminar la propiedad "image" del objeto newProduct, ya que no se enviará en el JSON
            // (Se enviará como archivo separado)
            let withoutImage = { ...newProduct.value };
            delete withoutImage.image;

            formData.append('product', JSON.stringify(withoutImage));
            formData.append('image', productImage.value.files[0]);

            // Simular una llamada a la API para agregar el producto
            axios.post('/api/products', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })

            setTimeout(() => {
                fetchingModal.value = false;
                if (Math.random() > 0.5) {
                    successNotification("Producto añadido correctamente.");
                } else {
                    errorNotification("No se pudo agregar el producto. Inténtalo de nuevo más tarde.");
                }
            }, 1000);
        },
    )
}

const closeModalConfirmation = (done) => {
    confirmation("Alerta", "¿Estás seguro de que deseas salir sin guardar?",
        () => {
            done();
        },
    );
};

const handleCloseModal = (e) => {
    newProduct.value = {
        name: '',
        description: '',
        price: '',
        stock: '',
        provider: { id: null, name: '', phone: '', email: '', address: '' },
        category: { id: null, name: '', description: '' },
        iva: { id: null, name: '', iva: 0 },
        warehouse: { id: null, name: '', description: '' },
        image: null,
    };
    productImage.value = null;
    emit('closeModal');
};

const closeModal = () => {
    confirmation("Alerta", "¿Estás seguro de que deseas salir sin guardar?",
        () => {
            handleCloseModal();
        },
    );
};
</script>

<template>
    <el-dialog title="Nuevo Producto" width="80%" @close="handleCloseModal" :before-close="closeModalConfirmation"
        :fullscreen="fullScreenModals" class="!p-6" :destroy-on-close="true">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <div class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
            <!-- Product Image -->
            <div
                class="w-full md:w-fit justify-center items-center   shrink-0 justify-center flex relative md:border-stone-200 md:border-r-[2px] md:pr-4">
                <input type="file" accept="image/*" class="hidden" id="newProductImageInput" @change="handleImageChange"
                    ref="productImage" />
                <img :src="newProduct.image !== null ? newProduct.image : '/src/assets/images/product-placeholder.jpg'"
                    alt="Product Image"
                    class="object-cover w-[300px] h-[300px] object-cover rounded-lg shadow-lg shadow-stone-300 hover:shadow-stone-400 transition cursor-pointer"
                    @click="() => { productImage.click() }" />
            </div>

            <Line orientation="horizontal" class="bg-stone-200 md:hidden" />

            <!-- Product Info -->
            <div class="w-full min-w-0 flex flex-col gap-4">
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Id:</p>
                    <el-input v-model="newProduct.id" class="w-full" type="text" placeholder="ID o Código de barras" />
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Nombre:</p>
                    <el-input v-model="newProduct.name" class="w-full" type="text" placeholder="Nombre del Producto" />
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Descripción:</p>
                    <el-input v-model="newProduct.description" class="w-full" type="textarea"
                        placeholder="Descripción del Producto" />
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Proveedor:</p>
                    <el-select v-model="newProduct.provider" value-key="id" class="w-full"
                        placeholder="Seleccionar Proveedor" :loading="fetchingProviders" filterable remote
                        :remote-method="handleProviderSearch">
                        <el-option v-for="provider in providers" :key="provider.id" :label="provider.name"
                            :value="provider" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Precio Actual:</p>
                    <el-input-number v-model="newProduct.currentPrice" controls-position="right" :precision="2"
                        :min="0.01" :max="9999999999.99" class="!w-fit">
                        <template #prefix>
                            <span>$</span>
                        </template>
                    </el-input-number>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Categoría:</p>
                    <el-select v-model="newProduct.category" value-key="id" class="w-full"
                        placeholder="Seleccionar Categoría" :loading="fetchingCategories" filterable remote
                        :remote-method="handleCategorySearch">
                        <el-option v-for="category in categories" :key="category.id" :label="category.name"
                            :value="category" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">IVA actual:</p>
                    <el-select v-model="newProduct.currentIva" value-key="id" class="w-full h-fit"
                        placeholder="Seleccionar IVA Actual" :loading="fetchingIVA" filterable remote
                        :remote-method="handleIvaSearch">
                        <el-option v-for="iva in IVAs" :key="iva.id" :label="`${iva.name} (${iva.iva})%`"
                            :value="iva" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Almacenes Compatibles:</p>
                    <el-select v-model="newProduct.compatibleWarehouses" value-key="id" class="w-full h-fit"
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
                <el-button class="!ml-0" type="info" :disabled="fetchingModal" @click="closeModal">
                    Cerrar
                </el-button>
                <el-button class="!ml-0" type="success" :disabled="fetchingModal" @click="handleAddProduct">
                    Añadir Producto
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>