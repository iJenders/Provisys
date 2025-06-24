<template>
    <div class="w-full gap-4 flex flex-col text-xl">
        <h1 class="text-stone-700 font-medium">Registrar una compra</h1>
        <Line class="bg-stone-200" orientation="horizontal" />
        <div class="w-full flex flex-col gap-4" v-loading="loading">
            <!-- Provider Selection -->
            <div class="flex gap-4 flex-wrap">
                <div class="w-full flex flex-col gap-2 min-w-[250px] flex-1">
                    <p class="text-stone-700 text-sm font-medium">Proveedor</p>
                    <el-select v-model="purchase.providerId" placeholder="Seleccionar Proveedor" style="width: 100%;"
                        filterable remote :remote-method="getProviders" :loading="fetchingProviders">
                        <el-option v-for="provider in providers" :key="provider.id" :label="provider.name"
                            :value="provider.id" format="YYYY/MM/DD" />
                    </el-select>
                </div>

                <!-- Date Picker -->

                <div class="w-full flex flex-col gap-2 min-w-[250px] flex-1">
                    <p class="text-stone-700 text-sm font-medium">Fecha</p>
                    <el-date-picker v-model="purchase.date" type="date" placeholder="Seleccionar Fecha"
                        style="width: 100%;" :disabled-date="(date) => { return date >= Date.now() }" />
                </div>
            </div>

            <!-- Products -->
            <div class="w-full flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <p class="text-stone-700 text-sm font-medium">Productos</p>
                    <ThemeButton @click="addProduct"
                        class="!py-1 text-sm border-green-600 text-green-600 hover:bg-green-600 hover:text-white">
                        Agregar Producto
                    </ThemeButton>
                </div>
                <TransitionGroup name="list">
                    <div v-for="(product, index) in purchase.products" :key="index" class="w-full flex gap-2 items-end">
                        <!-- Product -->
                        <div class="flex-grow">
                            <el-select v-model="product.id" placeholder="Seleccionar Producto" style="width: 100%;"
                                filterable remote :remote-method="getProducts"
                                @change="onProductChange(product, index)">
                                <el-option v-for="item in products" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                        </div>

                        <!-- Warehouse -->
                        <div class="w-[200px]">
                            <p class="text-stone-700 text-xs font-medium">Almacén</p>
                            <el-select v-model="product.warehouseId" placeholder="Seleccionar Almacén"
                                style="width: 100%;" filterable remote :remote-method="getWarehouses(product)">
                                <el-option v-for="warehouse in warehouses" :key="warehouse.id" :label="warehouse.name"
                                    :value="warehouse.id" :loading="fetchingWarehouses" />
                            </el-select>
                        </div>

                        <!-- Quantity -->
                        <div class="w-[150px]">
                            <p class="text-stone-700 text-xs font-medium">Cantidad</p>
                            <el-input-number v-model="product.quantity" :min="1" controls-position="right"
                                style="width: 100%;" />
                        </div>

                        <!-- Unit Price -->
                        <div class="w-[150px]">
                            <p class="text-stone-700 text-xs font-medium">Precio Unitario</p>
                            <el-input-number v-model="product.unitPrice" :min="0.01" :precision="2"
                                controls-position="right" style="width: 100%;">
                                <template #prefix>$</template>
                            </el-input-number>
                        </div>

                        <!-- IVA -->
                        <div class="w-[150px]">
                            <p class="text-stone-700 text-xs font-medium">IVA</p>
                            <el-input-number v-model="product.iva" :min="0" :precision="2" controls-position="right"
                                style="width: 100%;">
                                <el-button type="danger" circle @click="removeProduct(index)">
                                    <el-icon>
                                        <Delete />
                                    </el-icon>
                                </el-button>
                            </el-input-number>
                        </div>

                        <!-- Subtotal -->
                        <div class="w-[150px]">
                            <p class="text-stone-700 text-xs font-medium">Subtotal</p>
                            <p class="text-stone-700 text-lg font-medium">
                                $ {{ (product.quantity * product.unitPrice + (product.quantity * product.unitPrice *
                                    product.iva /
                                    100)).toFixed(2) }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="w-[50px] flex items-end">
                            <el-button type="danger" circle @click="removeProduct(index)">
                                <el-icon>
                                    <X />
                                </el-icon>
                            </el-button>
                        </div>
                    </div>
                </TransitionGroup>
            </div>

            <!-- Total -->
            <div class="w-full flex justify-end">
                <p class="text-stone-700 text-lg font-medium">Total: ${{ total }}</p>
            </div>

            <!-- Buttons -->
            <div class="w-full flex justify-end gap-4">
                <ThemeButton @click="cancel"
                    class="!py-2 border-stone-700 text-stone-700 hover:bg-stone-700 hover:text-white">
                    Cancelar
                </ThemeButton>
                <ThemeButton @click="savePurchase"
                    class="!py-2 border-green-600 text-green-600 hover:bg-green-600 hover:text-white">
                    Guardar Compra
                </ThemeButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Delete } from '@element-plus/icons-vue';
import Line from '@/components/Line.vue';
import ThemeButton from '@/components/ThemeButton.vue';
import { successNotification, errorNotification } from '@/utils/feedback';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { ElMessageBox, ElNotification } from 'element-plus';
import { X } from 'lucide-vue-next';
import { handleRequestError } from '@/utils/fetchNotificationsHandlers';

const allowRepeatedProducts = ref(false);

const router = useRouter();
const loading = ref(false);

const providers = ref([]);
const fetchingProviders = ref(false);
const products = ref([]);
const fetchingProducts = ref(false);
const warehouses = ref([]);
const fetchingWarehouses = ref(false);

const purchase = ref({
    providerId: null,
    products: [],
    date: null,
});

const total = computed(() => {
    return purchase.value.products.reduce((acc, curr) => {
        return acc + (curr.quantity * curr.unitPrice + (curr.quantity * curr.unitPrice * curr.iva / 100));
    }, 0).toFixed(2);
});

const addProduct = () => {
    purchase.value.products.push({
        id: null,
        quantity: 1,
        unitPrice: 0.01,
        iva: 0,
        warehouseId: null,
    });
};

const removeProduct = (index) => {
    purchase.value.products.splice(index, 1);
};

const cancel = () => {
    ElMessageBox.confirm(
        '¿Estás seguro de que quieres cancelar?',
        'Cancelar Compra',
        {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning',
        }).then(() => {
            purchase.value = {
                providerId: null,
                products: []
            };
        }).catch(() => { })
};

const getProviders = (str) => {
    fetchingProviders.value = true;

    let data = {
        filters: {
            deleted: 0
        },
        search: str ? str : null
    }

    axios.post(import.meta.env.VITE_API_URL + '/providers', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        providers.value = response.data.response.providers.map(provider => {
            return {
                id: provider.id,
                idType: provider.id.substring(2, provider.id.length - 1),
                name: provider.name,
                phone: provider.phone,
                secondaryPhone: provider.secondaryPhone,
                email: provider.email,
                address: provider.address,
                disabled: provider.deleted
            }
        });
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProviders.value = false;
    });
}

const getProducts = (str) => {
    fetchingProducts.value = true;

    let data = {
        filters: {
            deleted: 0
        },
        search: str ? str : null
    }

    axios.post(import.meta.env.VITE_API_URL + '/products', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        products.value = response.data.response.products.map(product => {
            return {
                id: product.id,
                name: product.name,
                description: product.description,
                price: product.actualPrice,
                iva: product.actualIva.value
            }
        });
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingProducts.value = false;
    });
}

const getWarehouses = (product) => {
    if (product.id === null) {
        warehouses.value = [];
        return;
    }

    fetchingWarehouses.value = true;

    let data = {
        // Id del producto
        id: product.id,
        filters: {}
    }

    axios.post(import.meta.env.VITE_API_URL + '/products/getCompatibleStorages', data, {
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    }).then(response => {
        warehouses.value = response.data.response.storages
    }).catch(error => {
        handleRequestError(error);
    }).finally(() => {
        fetchingWarehouses.value = false;
    });
}

const savePurchase = async () => {
    // Validar que todos los campos estén llenos
    if (!purchase.value.providerId) {
        errorNotification('Debe seleccionar un proveedor');
        return;
    }

    if (purchase.value.products.length === 0) {
        errorNotification('Debe agregar al menos un producto');
        return;
    }

    if (purchase.value.products.some(p => p.id == null)) {
        errorNotification('Debe seleccionar un producto');
        return;
    }

    if (purchase.value.date == null || purchase.value.date > new Date()) {
        errorNotification('Debe seleccionar una fecha válida');
        return;
    }

    if (purchase.value.products.some(p => p.quantity <= 0)) {
        errorNotification('La cantidad de unidades en cada producto debe ser mayor a 0');
        return;
    }

    if (purchase.value.products.some(p => p.warehouseId == null)) {
        errorNotification('Debe seleccionar un almacen para cada producto. Si algún producto no tiene almacen, dirijase a la sección de productos para configurar uno.');
        return;
    }

    ElMessageBox.confirm(
        '¿Estás seguro de que quieres guardar la compra?',
        'Guardar Compra',
        {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning',
        }).then(() => {
            loading.value = true;

            let data = {
                providerId: purchase.value.providerId,
                products: purchase.value.products.map(p => {
                    return {
                        id: p.id,
                        quantity: p.quantity,
                        unitPrice: p.unitPrice,
                        iva: p.iva,
                        warehouseId: p.warehouseId
                    }
                }),
                // Date in YYYY-MM-DD format
                date: purchase.value.date.toISOString().split('T')[0]
            }

            axios.post(import.meta.env.VITE_API_URL + '/purchases/create', data, {
                headers: {
                    'Authorization': localStorage.getItem('token')
                }
            }).then(response => {
                successNotification('Compra registrada correctamente');
                purchase.value = {
                    providerId: null,
                    date: null,
                    products: []
                };
            }).catch(error => {
                handleRequestError(error);
            }).finally(() => {
                loading.value = false;
            });
        }).catch(() => {

        }).finally(() => {
            loading.value = false;
        });
};

const onProductChange = (product, index) => {
    // Vaciar el valor de wareouseId
    product.warehouseId = null;

    // Buscar repetidos y mostrar notificación

    if (allowRepeatedProducts.value) return;
    const repeatedProduct = purchase.value.products.find((p, i) => i !== index && p.id === product.id);
    if (repeatedProduct) {
        ElNotification({
            title: 'Producto repetido',
            message: 'El producto ya se encuentra en la lista de compras',
            type: 'warning',
            offset: 80,
            zIndex: 10000
        })
        purchase.value.products[index] = {
            id: null,
            quantity: 1,
            iva: 0,
            unitPrice: 0.01
        };
        return;
    }
}
</script>