import { onMounted, onUnmounted, ref, computed } from "vue";

export function useMouse() {
  const mouseCoords = ref({
    absoluteCoords: {
      x: 0,
      y: 0,
    },
    relativeCoords: {
      x: 0,
      y: 0,
    },
  });

  const getMouseRelativeX = computed(() => {
    return mouseCoords.value.relativeCoords.x;
  });

  const getMouseRelativeY = computed(() => {
    return mouseCoords.value.relativeCoords.y;
  });

  const handleMouseMove = (e) => {
    mouseCoords.value.absoluteCoords.x = e.clientX;
    mouseCoords.value.absoluteCoords.y = e.clientY;
    mouseCoords.value.relativeCoords.x = e.clientX / window.innerWidth;
    mouseCoords.value.relativeCoords.y = e.clientY / window.innerHeight;
  };

  onMounted(() => {
    window.addEventListener("mousemove", handleMouseMove);
  });

  onUnmounted(() => {
    window.removeEventListener("mousemove", handleMouseMove);
  });

  return {
    mouseCoords,
    getMouseRelativeX,
    getMouseRelativeY,
  };
}
