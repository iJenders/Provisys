import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useShoppingCartStore = defineStore("shoppingCart", () => {
  const products = ref([]);
  const addProduct = (product, quantity) => {
    products.value.push({
      product,
      quantity,
    });
  };
  const removeProduct = (product) => {
    // Find the index of the product in the cart
    let index = products.value.findIndex((p) => p.product.id === product.id);

    // Remove the product from the cart
    products.value.splice(index, 1);
  };
  const clearCart = () => {
    products.value = [];
  };

  return { products, addProduct, removeProduct, clearCart };
});
