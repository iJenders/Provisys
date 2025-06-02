import axios from "axios";
import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useRouter } from "vue-router";

export const useAuthStore = defineStore("auth", () => {
  const router = useRouter();

  const token = ref(null);
  const user = ref(null);
  const userPermissions = ref([]);

  const isAuthenticated = computed(() => !!user.value);

  const logout = (pushToRoot = true) => {
    localStorage.removeItem("token");
    token.value = null;
    user.value = null;
    userPermissions.value = [];

    // Redirige al usuario a la página de inicio
    if (pushToRoot) router.push("/");
  };

  const refreshUser = () => {
    // Carga los datos y permisos del usuario en base al token almacenado

    let gettingPermissions = false;

    axios
      .post(
        import.meta.env.VITE_API_URL + "/auth/user",
        {},
        {
          headers: {
            Authorization: token.value,
          },
        }
      )
      .then((response) => {
        user.value = response.data.response.user;
        gettingPermissions = true;
      })
      .catch((error) => {
        logout();
      });
  };

  const checkPermission = (permission) => {
    if (
      userPermissions.value.includes(permission) ||
      user.value.roleId === "1"
    ) {
      return true;
    } else {
      return false;
    }
  };

  return {
    token,
    user,
    userPermissions,
    isAuthenticated,
    logout,
    refreshUser,
    checkPermission,
  };
});
