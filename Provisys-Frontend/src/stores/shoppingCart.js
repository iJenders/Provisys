import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useShoppingCartStore = defineStore("shoppingCart", () => {
  const products = ref([]);
  const addProduct = (product, quantity) => {
    products.value.push({
      product: JSON.parse(JSON.stringify(product)),
      quantity,
    });
  };
  const removeProduct = (product) => {
    products.value = products.value.filter((p) => p.id !== product.id);
  };
  const clearCart = () => {
    products.value = [];
  };

  return { products, addProduct, removeProduct, clearCart };
});
