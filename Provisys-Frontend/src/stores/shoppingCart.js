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
    products.value.splice(products.value.indexOf(product), 1);
  };
  const clearCart = () => {
    products.value = [];
  };

  return { products, addProduct, removeProduct, clearCart };
});
