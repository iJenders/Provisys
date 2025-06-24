import { computed, onMounted, onUnmounted, ref } from "vue";

export const useFullScreenModals = () => {
  const screen = ref(window.innerWidth);
  const fullScreenModals = computed(() => screen.value < 768);

  onMounted(() => {
    window.addEventListener("resize", () => {
      screen.value = window.innerWidth;
    });
  });

  onUnmounted(() => {
    window.removeEventListener("resize", () => {
      screen.value = window.innerWidth;
    });
  });

  return {
    fullScreenModals,
    screen,
  };
};
