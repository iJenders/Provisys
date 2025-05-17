<script setup>
import { defineProps } from 'vue'
import { X } from 'lucide-vue-next'
import { useShoppingCartStore } from '@/stores/shoppingCart.js'

const shoppingCartStore = useShoppingCartStore()

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const handleRemoveItem = () => {
    shoppingCartStore.removeProduct(props.item)
}

/*
    * Shopping Cart item example:

    {
        product: {
            image: 'https://images.freeimages.com/slides/4f53a54c6e114a76a99b9df9573a8259.webp',
            name: 'Producto de Prueba',
            description: 'Este es un producto de prueba para el carrito de compras.',
            price: 19.99,
        }
        quantity: 1,
    },

*/
</script>

<template>
    <div class="flex gap-6 justify-between items-center">
        <img :src="item.product.image" alt="Product Image"
            class="w-24 h-24 object-cover rounded-2xl drop-shadow-lg drop-shadow-stone-300" />
        <div class="flex flex-col gap-2">
            <h3 class="text-lg font-normal text-gray-800 text-shadow-lg text-shadow-stone-300">{{ item.product.name }}
            </h3>
            <div class="flex gap-2 items-center">
                <p class="text-gray-600">{{ item.quantity }}</p>
                <p class="text-gray-600">x</p>
                <p class="text-green-600 font-normal text-shadow-lg text-shadow-stone-300">${{ item.product.price }}</p>
            </div>
        </div>
        <div class="flex justify-center items-center">
            <button
                class="bg-gray-500 hover:bg-red-500 transition ease-linear duration-200 rounded-full p-1 cursor-pointer">
                <X size="16" class="text-white" @click="handleRemoveItem(item.product)" />
            </button>
        </div>
    </div>
</template>