import { onMounted, onUnmounted, ref } from "vue";

export const useFullScreenModals = () => {
  const fullScreenModals = ref(window.innerWidth < 768);

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
    fullScreenModals,
  };
};
