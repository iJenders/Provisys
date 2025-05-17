<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import { Package, ShoppingCart } from 'lucide-vue-next'
import { ElDialog, ElInputNumber } from 'element-plus'
import { useShoppingCartStore } from '@/stores/shoppingCart.js'
import ThemeButton from '@/components/ThemeButton.vue'

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})

const shoppingCartStore = useShoppingCartStore();

const emit = defineEmits(['addToCart'])

const showButtons = ref(false);
const showAddToCartModal = ref(false);
const quantity = ref(1);

const handleAddToCart = (e) => {
    e.preventDefault();
    shoppingCartStore.addProduct(props.product, quantity.value);
    emit('addToCart', props.product, quantity.value);
    showAddToCartModal.value = false;
}

/*
    * Product Example

    {
        id: 1,
        name: 'Producto 1',
        price: 10.99,
        stock: 5,
        description: 'Descripción del producto 1.',
        image: 'https://images.freeimages.com/slides/4f53a54c6e114a76a99b9df9573a8259.webp',
        provider: "Parmalat"
        }

*/
</script>

<template>
    <div class="relative shadow-lg w-[285px]" @mouseenter="showButtons = true" @mouseleave="showButtons = false">
        <img :src="product.image" alt="" class="w-full h-auto">
        <div class="flex flex-col p-4 gap-2">
            <h3 class="text-xl font-bold text-stone-800">{{ product.name }}</h3>
            <div>
                <p class="text-sm text-stone-600">{{ product.description }}</p>
                <p class="text-sm text-stone-600">Proveedor:
                    <span class=" font-medium">{{ product.provider }}</span>
                </p>
                <div class="flex flex-row items-center gap-1"
                    :class="product.stock > 0 ? 'text-green-700' : 'text-red-500 opacity-50'">
                    <Package :size="16" />
                    <p class="text-sm inline-block">{{
                        `${product.stock > 0 ?
                            product.stock + " en stock" :
                            "Agotado"}`
                    }}</p>
                </div>
            </div>
            <p class="text-stone-700 font-bold mt-2">$ {{ product.price.toFixed(2) }}</p>
        </div>
        <Transition>
            <div v-if="showButtons"
                class="flex group-hover:opacity-100 justify-center items-center absolute p-4 w-full h-full top-0 left-0 bg-neutral-950/30">
                <ThemeButton v-if="product.stock > 0"
                    class="border-transparent bg-white text-green-600 hover:bg-green-600 hover:text-white bold"
                    @click="(e) => { e.preventDefault(); showAddToCartModal = true; }">
                    <ShoppingCart :size="24" />
                    Añadir al carrito
                </ThemeButton>
                <ThemeButton v-else
                    class="border-transparent bg-white text-red-500 hover:bg-red-500 hover:text-white bold"
                    :enabled="false">
                    <ShoppingCart :size="24" />
                    Agotado
                </ThemeButton>
            </div>
        </Transition>
    </div>

    <!-- Add to Cart Modal -->
    <ElDialog v-model="showAddToCartModal" title="Añadir al carrito" width="70%" @close="showAddToCartModal = false">
        <div class="flex flex-col gap-4 p-2">
            <div class="flex items-center gap-4">
                <img :src="product.image" alt="" class="w-20 h-20 object-cover rounded shadow" />
                <div>
                    <h4 class="text-lg font-bold text-stone-800">{{ product.name }}</h4>
                    <p class="text-stone-600 text-sm">{{ product.description }}</p>
                    <p class="text-stone-700 font-semibold mt-1">$ {{ product.price.toFixed(2) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <label for="quantity" class="text-stone-700 font-medium">Cantidad:</label>
                <ElInputNumber id="quantity" :min="1" :max="product.stock" v-model="quantity" class="w-24" />
                <span class="text-xs text-stone-500">(Stock: {{ product.stock }})</span>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <ThemeButton class="bg-stone-200 text-stone-700 hover:bg-stone-300" @click="showAddToCartModal = false">
                    Cancelar
                </ThemeButton>
                <ThemeButton class="bg-green-600 text-white hover:bg-green-700"
                    :disabled="quantity < 1 || quantity > product.stock" @click="handleAddToCart">
                    <ShoppingCart :size="20" class="mr-1" />
                    Añadir
                </ThemeButton>
            </div>
        </div>
    </ElDialog>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: 0.15s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>