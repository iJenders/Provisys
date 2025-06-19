import { ref, computed, onMounted } from "vue";
import { defineStore } from "pinia";
import { ElNotification } from "element-plus";

export const useShoppingCartStore = defineStore("shoppingCart", () => {
  const products = ref([]);
  const addProduct = (product, quantity) => {
    // Check if the product is already in the cart
    let index = products.value.findIndex((p) => p.product.id === product.id);
    if (index !== -1) {
      // If the product is already in the cart, show an alert
      ElNotification.error({
        title: "Error",
        message:
          "El producto ya se encuentra en el carrito. Si desea añadir una cantidad diferente, elimine el producto y agreguelo nuevamente.",
        type: "error",
        duration: 3000,
        offset: 80,
        zIndex: 10000,
      });
      return;
    }

    // Add the product to the cart
    products.value.push({
      product,
      quantity,
    });

    // Success Mesage
    ElNotification.success({
      title: "Éxito",
      message: "Producto agregado al carrito: " + product.name,
      type: "success",
      duration: 3000,
      offset: 80,
      zIndex: 10000,
    });

    // Save the cart to localStorage
    saveCartToLocalStorage();
  };

  const removeProduct = (product) => {
    // Find the index of the product in the cart
    let index = products.value.findIndex((p) => p.product.id === product.id);

    if (index === -1) {
      // If the product is not in the cart, show an alert
      ElNotification.error({
        title: "Error",
        message: "El producto no se encuentra en el carrito.",
        type: "error",
        duration: 3000,
        offset: 80,
        zIndex: 10000,
      });
      return;
    }

    // Remove the product from the cart
    products.value.splice(index, 1);

    // Save the cart to localStorage
    saveCartToLocalStorage();
  };

  const clearCart = () => {
    products.value = [];
    saveCartToLocalStorage();
  };

  const saveCartToLocalStorage = () => {
    localStorage.setItem("cart", JSON.stringify(products.value));
  };

  const getCartFromLocalStorage = () => {
    const cart = localStorage.getItem("cart");
    if (cart) {
      products.value = JSON.parse(cart);
    }
  };

  onMounted(() => {
    try {
      getCartFromLocalStorage();
    } catch (error) {
      console.error("Error al cargar el carrito desde localStorage:", error);
    }
  });

  return { products, addProduct, removeProduct, clearCart };
});
