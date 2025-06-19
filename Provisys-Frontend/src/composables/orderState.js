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

  const orders = ref([]);

  const paginationConfig = ref({
    page: 1,
    rowsPerPage: 10,
    totalRows: 0,
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
    paginationConfig,
    selectedOrder,
    isSelectedOrder,
    fullScreenModals,
    handleOrderClick,
    handlePageChange,
  };
};
