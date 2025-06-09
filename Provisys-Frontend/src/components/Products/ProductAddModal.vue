<script setup>
import { ref } from 'vue';
import { confirmation, errorNotification, successNotification } from '@/utils/feedback';
import { useFullScreenModals } from '@/composables/fullScreenModals';
import Line from '@/components/Line.vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import { ElNotification } from 'element-plus';

const { fullScreenModals } = useFullScreenModals();

const emit = defineEmits(['closeModal']);

const newProduct = ref({
    id: '',
    name: '',
    description: '',
    price: 0,
    provider: { id: null, name: '', phone: '', email: '', address: '' },
    category: { id: null, name: '', description: '' },
    iva: { id: null, name: '', value: 0 },
    image: null,
});
const productImage = ref(null);

const fetchingModal = ref(false);
const fetchingProviders = ref(false);
const fetchingCategories = ref(false);
const fetchingIVA = ref(false);

const providers = ref([]);

const categories = ref([]);

const IVAs = ref([]);

const handleProviderSearch = (value) => {
    // Implementar lógica para obtener proveedores basados en la búsqueda
    fetchingProviders.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/manufacturers', {
        search: value == '' ? null : value,
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        providers.value = response.data.response.manufacturers;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProviders.value = false;
    });
}

const handleCategorySearch = (value) => {
    // Implementar lógica para obtener categorías basadas en la búsqueda
    fetchingCategories.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/categories', {
        search: value == '' ? null : value
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        categories.value = response.data.response.categories;
    }).catch(error => {

    }).finally(() => {
        fetchingCategories.value = false;
    });

}

const handleIvaSearch = (value) => {
    // Implementar lógica para obtener IVAs basados en la búsqueda
    fetchingIVA.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/ivas', {
        search: value == '' ? null : value
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        IVAs.value = response.data.response.IVAs;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingIVA.value = false;
    });
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

            let productData = {
                id: newProduct.value.id,
                name: newProduct.value.name,
                description: newProduct.value.description,
                actualPrice: newProduct.value.price,
                actualIva: newProduct.value.iva.id,
                categoria: newProduct.value.category.id,
                fabricante: newProduct.value.provider.id,
            };

            formData.append('product', JSON.stringify(productData));
            formData.append('image', productImage.value.files[0]);

            // Simular una llamada a la API para agregar el producto
            axios.post(import.meta.env.VITE_API_URL + '/products/create', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Authorization': localStorage.getItem('token')
                },
            }).then(response => {
                successNotification("Producto agregado exitosamente.");
                emit('closeModal');
            }).catch(error => {
                handleRequestError(error);
            }).finally(() => {
                fetchingModal.value = false;
            });
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
        price: 0,
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
                    <el-input-number v-model="newProduct.price" controls-position="right" :precision="2" :min="0.01"
                        :max="9999999999.99" class="!w-fit">
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
                    <el-select v-model="newProduct.iva" value-key="id" class="w-full h-fit"
                        placeholder="Seleccionar IVA Actual" :loading="fetchingIVA" filterable remote
                        :remote-method="handleIvaSearch">
                        <el-option v-for="iva in IVAs" :key="iva.id" :label="`${iva.name} (${iva.value}%)`"
                            :value="iva" />
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