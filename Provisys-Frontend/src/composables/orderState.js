import { onMounted, onUnmounted, ref, computed } from "vue";

export const useOrderState = () => {
  const selectedOrder = ref(null);
  const isSelectedOrder = ref(false);
  const fullScreenModals = ref(window.innerWidth < 768);
  const fetchingOrders = ref(false);
  const fetchingModal = ref(false);

  const ordersFilter = ref({
    date: {
      from: null,
      to: null,
    },
    client: null,
    value: {
      from: null,
      to: null,
    },
    searcher: null,
  });

  // ¡¡¡ WARNING !!!
  // Filled with test data
  // Must be empty array when fetching from backend
  const orders = ref([
    {
      id: 1,
      date: "2025-03-01",
      status: 0,
      client: {
        username: "juanperez01",
        registerDate: "2023-01-01",
        names: "Juan Alberto",
        lastnames: "Pérez Segura",
        email: "juanperez01@gmail.com",
        phone: "3123456789",
        secondaryPhone: null,
        address: "Av. Vargas entre calles 123 y 124",
        roleId: 0,
      },
      payment: {
        id: 0,
        mount: 25.0,
        verified: false,
        paymentReference: "012345678900112233445566778899000",
        method: {
          id: 0,
          name: "Pago Móvil",
        },
      },
      products: [
        {
          id: 101,
          quantity: 5,
          price: 4.31,
          iva: 16,
        },
      ],
    },
    {
      id: 2,
      date: "2025-03-05",
      status: 0,
      client: {
        username: "marialopez",
        registerDate: "2023-02-15",
        names: "María Fernanda",
        lastnames: "López García",
        email: "marialopez@gmail.com",
        phone: "3129876543",
        secondaryPhone: "3121122334",
        address: "Calle El Sol, Urb. Las Acacias",
        roleId: 0,
      },
      payment: {
        id: 1,
        mount: 150.0,
        verified: false,
        paymentReference: "987654321099887766554433221100999",
        method: {
          id: 1,
          name: "Transferencia Bancaria",
        },
      },
      products: [
        {
          id: 201,
          quantity: 2,
          price: 50.0,
          iva: 16,
        },
        {
          id: 202,
          quantity: 1,
          price: 25.0,
          iva: 16,
        },
      ],
    },
    {
      id: 3,
      date: "2025-03-10",
      status: 0,
      client: {
        username: "carlossanchez",
        registerDate: "2023-03-20",
        names: "Carlos Daniel",
        lastnames: "Sánchez Rodríguez",
        email: "carlossanchez@gmail.com",
        phone: "3125554433",
        secondaryPhone: null,
        address: "Carrera 5 con calle 8, Centro",
        roleId: 0,
      },
      payment: {
        id: 2,
        mount: 75.5,
        verified: false,
        paymentReference: "112233445566778899001122334455666",
        method: {
          id: 0,
          name: "Pago Móvil",
        },
      },
      products: [
        {
          id: 301,
          quantity: 3,
          price: 20.0,
          iva: 16,
        },
        {
          id: 302,
          quantity: 1,
          price: 10.0,
          iva: 16,
        },
      ],
    },
    {
      id: 4,
      date: "2025-03-15",
      status: 0,
      client: {
        username: "anagonzalez",
        registerDate: "2023-04-01",
        names: "Ana Carolina",
        lastnames: "González Vargas",
        email: "anagonzalez@gmail.com",
        phone: "3127778899",
        secondaryPhone: null,
        address: "Avenida Principal, Edif. Los Pinos",
        roleId: 0,
      },
      payment: {
        id: 3,
        mount: 120.0,
        verified: false,
        paymentReference: "554433221100998877665544332211000",
        method: {
          id: 1,
          name: "Transferencia Bancaria",
        },
      },
      products: [
        {
          id: 401,
          quantity: 1,
          price: 100.0,
          iva: 16,
        },
        {
          id: 402,
          quantity: 2,
          price: 8.62,
          iva: 16,
        },
      ],
    },
  ]);

  // ¡¡¡ WARNING !!!
  // Filled with test data
  // Must be empty array when fetching from backend
  const ordersDetails = ref([
    {
      id: 1,
      date: "2025-03-01",
      status: 0,
      client: {
        username: "juanperez01",
        registerDate: "2023-01-01",
        names: "Juan Alberto",
        lastnames: "Pérez Segura",
        email: "juanperez01@gmail.com",
        phone: "3123456789",
        secondaryPhone: null,
        address: "Av. Vargas entre calles 123 y 124",
        roleId: 0,
      },
      payment: {
        id: 0,
        mount: 25.0,
        verified: false,
        paymentReference: "012345678900112233445566778899000",
        method: {
          id: 0,
          name: "Pago Móvil",
        },
      },
      products: [
        {
          quantity: 5,
          price: 4.31,
          iva: 16,
          productDetails: {
            id: 101,
            name: "Laptop HP Pavilion",
            description: "Laptop ideal para trabajo y estudio.",
            currentPrice: 850.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 1,
              name: "Tech Solutions",
              phone: "4121234567",
              email: "info@techsolutions.com",
              address: "Calle 10, Local 5",
            },
          },
        },
      ],
    },
    {
      id: 2,
      date: "2025-03-05",
      status: 0,
      client: {
        username: "marialopez",
        registerDate: "2023-02-15",
        names: "María Fernanda",
        lastnames: "López García",
        email: "marialopez@gmail.com",
        phone: "3129876543",
        secondaryPhone: "3121122334",
        address: "Calle El Sol, Urb. Las Acacias",
        roleId: 0,
      },
      payment: {
        id: 1,
        mount: 150.0,
        verified: false,
        paymentReference: "987654321099887766554433221100999",
        method: {
          id: 1,
          name: "Transferencia Bancaria",
        },
      },
      products: [
        {
          quantity: 2,
          price: 50.0,
          iva: 16,
          productDetails: {
            id: 201,
            name: "Teclado Mecánico RGB",
            description: "Teclado gaming con luces RGB personalizables.",
            currentPrice: 75.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 2,
              name: "Gamer Zone",
              phone: "4249876543",
              email: "contacto@gamerzone.com",
              address: "Av. Libertador, Centro Comercial",
            },
          },
        },
        {
          quantity: 1,
          price: 25.0,
          iva: 16,
          productDetails: {
            id: 202,
            name: "Mouse Ergonómico Inalámbrico",
            description: "Mouse cómodo para largas jornadas de uso.",
            currentPrice: 30.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 2,
              name: "Gamer Zone",
              phone: "4249876543",
              email: "contacto@gamerzone.com",
              address: "Av. Libertador, Centro Comercial",
            },
          },
        },
      ],
    },
    {
      id: 3,
      date: "2025-03-10",
      status: 0,
      client: {
        username: "carlossanchez",
        registerDate: "2023-03-20",
        names: "Carlos Daniel",
        lastnames: "Sánchez Rodríguez",
        email: "carlossanchez@gmail.com",
        phone: "3125554433",
        secondaryPhone: null,
        address: "Carrera 5 con calle 8, Centro",
        roleId: 0,
      },
      payment: {
        id: 2,
        mount: 75.5,
        verified: false,
        paymentReference: "112233445566778899001122334455666",
        method: {
          id: 0,
          name: "Pago Móvil",
        },
      },
      products: [
        {
          quantity: 3,
          price: 20.0,
          iva: 16,
          productDetails: {
            id: 301,
            name: "Audífonos Bluetooth Noise Cancelling",
            description:
              "Audífonos con cancelación de ruido para una experiencia inmersiva.",
            currentPrice: 60.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 3,
              name: "Sound Masters",
              phone: "4165551122",
              email: "ventas@soundmasters.com",
              address: "Calle Principal, Local 3",
            },
          },
        },
        {
          quantity: 1,
          price: 10.0,
          iva: 16,
          productDetails: {
            id: 302,
            name: "Cable USB-C a USB-C",
            description: "Cable de carga rápida y transferencia de datos.",
            currentPrice: 15.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 3,
              name: "Sound Masters",
              phone: "4165551122",
              email: "ventas@soundmasters.com",
              address: "Calle Principal, Local 3",
            },
          },
        },
      ],
    },
    {
      id: 4,
      date: "2025-03-15",
      status: 0,
      client: {
        username: "anagonzalez",
        registerDate: "2023-04-01",
        names: "Ana Carolina",
        lastnames: "González Vargas",
        email: "anagonzalez@gmail.com",
        phone: "3127778899",
        secondaryPhone: null,
        address: "Avenida Principal, Edif. Los Pinos",
        roleId: 0,
      },
      payment: {
        id: 3,
        mount: 120.0,
        verified: false,
        paymentReference: "554433221100998877665544332211000",
        method: {
          id: 1,
          name: "Transferencia Bancaria",
        },
      },
      products: [
        {
          quantity: 1,
          price: 100.0,
          iva: 16,
          productDetails: {
            id: 401,
            name: "Monitor Curvo 27 pulgadas",
            description:
              "Monitor de alta resolución para una experiencia visual inmersiva.",
            currentPrice: 250.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 1,
              name: "Tech Solutions",
              phone: "4121234567",
              email: "info@techsolutions.com",
              address: "Calle 10, Local 5",
            },
          },
        },
        {
          quantity: 2,
          price: 8.62,
          iva: 16,
          productDetails: {
            id: 402,
            name: "Webcam Full HD",
            description: "Webcam con micrófono integrado para videollamadas.",
            currentPrice: 40.0,
            category: {
              id: 1,
              name: "Electrónica",
              description: "Dispositivos electrónicos",
            },
            currentIva: { id: 1, name: "IVA General", iva: 16 },
            provider: {
              id: 1,
              name: "Tech Solutions",
              phone: "4121234567",
              email: "info@techsolutions.com",
              address: "Calle 10, Local 5",
            },
          },
        },
      ],
    },
  ]);

  const computedOrders = computed(() => {
    let ordersWithProductsCalculated = JSON.parse(JSON.stringify(orders.value));

    ordersWithProductsCalculated.forEach((order) => {
      let subTotal = 0;
      let totalProducts = 0;
      order.products.forEach((product) => {
        let productSubTotal = product.price * product.quantity;
        subTotal += productSubTotal + (productSubTotal * product.iva) / 100;

        totalProducts += product.quantity;
      });

      order.subTotal = subTotal.toFixed(2);
      order.totalProducts = totalProducts;
      order.client.fullName = order.client.names + " " + order.client.lastnames;
    });

    return ordersWithProductsCalculated;
  });

  const paginationConfig = ref({
    page: 1,
    rowsPerPage: 1,
    totalRows: computedOrders.value.length,
  });

  const handleOrderClick = (e) => {
    let orderDetail = ordersDetails.value.find((order) => order.id === e.id);
    selectedOrder.value = JSON.parse(JSON.stringify(orderDetail));
    isSelectedOrder.value = true;
  };

  const handlePageChange = (e) => {
    console.log(e);
    fetchingOrders.value = true;
    setTimeout(() => {
      fetchingOrders.value = false;
    }, 1000);
  };

  onMounted(() => {
    window.addEventListener("resize", () => {
      fullScreenModals.value = window.innerWidth < 768;
    });
  });

  onUnmounted(() => {
    window.removeEventListener("resize", () => {
      fullScreenModals.value = window.innerWidth < 768;
    });
  });

  return {
    fetchingOrders,
    fetchingModal,
    ordersFilter,
    orders,
    computedOrders,
    paginationConfig,
    selectedOrder,
    isSelectedOrder,
    fullScreenModals,
    handleOrderClick,
    handlePageChange,
  };
};
