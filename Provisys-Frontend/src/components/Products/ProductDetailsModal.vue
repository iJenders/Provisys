<script setup>
import { onMounted, ref } from 'vue';
import { confirmation, errorNotification, successNotification } from '@/utils/feedback';
import { useFullScreenModals } from '@/composables/fullScreenModals';
import Line from '@/components/Line.vue';
import axios from 'axios';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';
import { ElMessageBox } from 'element-plus';

const { fullScreenModals } = useFullScreenModals();

const props = defineProps([
    'selectedProduct'
]);

const emit = defineEmits(['closeModal']);

const fetchingModal = ref(false);
const fetchingProviders = ref(false);
const fetchingCategories = ref(false);
const fetchingIVA = ref(false);
const fetchingWarehouses = ref(false);

const providers = ref([])
const categories = ref([]);
const IVAs = ref([]);
const warehouses = ref([]);

const imageInput = ref(null);
const uploadingImageUrl = ref(null);

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

const handleWarehouseSearch = (value) => {
    fetchingWarehouses.value = true;

    axios.post(import.meta.env.VITE_API_URL + '/storages', {
        search: value == '' ? null : value
    }, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        warehouses.value = response.data.response.storages;
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingWarehouses.value = false;
    });
}

const handleDeleteProduct = () => {
    ElMessageBox.confirm('¿Estás seguro de que deseas eliminar este producto?', 'Eliminar Producto', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        fetchingModal.value = true;

        axios.post(import.meta.env.VITE_API_URL + '/products/delete', {
            id: props.selectedProduct.id
        }, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            successNotification("Producto eliminado correctamente.");
            handleCloseModal();
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            fetchingModal.value = false;
        });
    })
}

const handleEditProduct = (product) => {
    ElMessageBox.confirm('¿Estás seguro de que deseas editar este producto?', 'Editar Producto', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning',
    }).then(() => {
        let formData = new FormData();
        // Eliminar la propiedad "image" del objeto newProduct, ya que no se enviará en el JSON
        // (Se enviará como archivo separado)

        let productData = {
            id: props.selectedProduct.id,
            name: props.selectedProduct.name,
            description: props.selectedProduct.description,
            actualPrice: props.selectedProduct.actualPrice,
            actualIva: props.selectedProduct.actualIva.id,
            categoria: props.selectedProduct.categoria.id,
            fabricante: props.selectedProduct.fabricante.id,
        };

        formData.append('product', JSON.stringify(productData));
        formData.append('image', imageInput.value.files[0]);

        axios.post(import.meta.env.VITE_API_URL + '/products/update', formData, {
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }).then(response => {
            successNotification("Producto editado correctamente.");
            handleCloseModal();
        }).catch(error => {
            handleRequestError(error);
        }).finally(() => {
            fetchingModal.value = false;
        });
    })
}

const handleSetImage = () => {
    imageInput.value.click();
}

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        uploadingImageUrl.value = URL.createObjectURL(file);
    }
}

const handleCloseModal = () => {
    uploadingImageUrl.value = null;
    emit('closeModal');
}

onMounted(() => {
    // Cargar proveedores, categorías, IVAs y almacenes aquí

    handleProviderSearch('');
    handleCategorySearch('');
    handleIvaSearch('');
})
</script>

<template>
    <el-dialog title="Detalles del Producto" width="80%" @close="handleCloseModal" :fullscreen="fullScreenModals"
        class="!p-6">
        <!-- Modal Content -->
        <Line orientation="horizontal" class="bg-stone-200" />
        <div v-if="selectedProduct" class="w-full flex flex-col md:flex-row gap-4 mt-4" v-loading="fetchingModal">
            <!-- Product Image -->
            <div
                class="w-full md:w-fit justify-center items-center   shrink-0 justify-center flex relative md:border-stone-200 md:border-r-[2px] md:pr-4">
                <img :src="uploadingImageUrl ? uploadingImageUrl : selectedProduct.image" alt="Product Image"
                    class="object-cover w-[300px] h-[300px] object-cover rounded-lg shadow-lg shadow-stone-300 cursor-pointer"
                    @click="handleSetImage" />
                <!-- Edit img input-->
                <input type="file" ref="imageInput" class="hidden" @change="handleFileChange" />
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
                    <el-select v-model="selectedProduct.fabricante" value-key="id" class="w-full"
                        placeholder="Seleccionar Proveedor" :loading="fetchingProviders" filterable remote
                        :remote-method="handleProviderSearch">
                        <el-option v-for="provider in providers" :key="provider.id" :label="provider.name"
                            :value="provider" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Precio Actual:</p>
                    <el-input-number v-model="selectedProduct.actualPrice" controls-position="right" :precision="2"
                        :min="0.01" :max="9999999999.99" class="!w-fit">
                        <template #prefix>
                            <span>$</span>
                        </template>
                    </el-input-number>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">Categoría:</p>
                    <el-select v-model="selectedProduct.categoria" value-key="id" class="w-full"
                        placeholder="Seleccionar Categoría" :loading="fetchingCategories" filterable remote
                        :remote-method="handleCategorySearch">
                        <el-option v-for="category in categories" :key="category.id" :label="category.name"
                            :value="category" />
                    </el-select>
                </div>
                <div class="w-full flex items-center gap-2 flex-wrap">
                    <p class="text-xl font-bold text-stone-700">IVA actual:</p>
                    <el-select v-model="selectedProduct.actualIva" value-key="id" class="w-full h-fit"
                        placeholder="Seleccionar IVA Actual" :loading="fetchingIVA" filterable remote
                        :remote-method="handleIvaSearch">
                        <el-option v-for="iva in IVAs" :key="iva.id" :label="`${iva.name} (${iva.value}%)`"
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
                <el-button class="!ml-0" type="info" :disabled="fetchingModal" @click="handleCloseModal">
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