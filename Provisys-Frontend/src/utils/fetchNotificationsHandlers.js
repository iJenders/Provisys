import { ElNotification } from "element-plus";

const handleRequestError = (error) => {
  let message = "";

  if (error.status === 400) {
    message += "Ha ocurrido un error de validación:";
  } else if (error.status === 401) {
    message =
      "No autorizado. Por favor, inicie sesión de nuevo o verifique sus credenciales.";
  } else if (error.status === 404) {
    message = "No se encontró el recurso solicitado.";
  } else if (error.status === 500) {
    message =
      "Error interno del servidor. Por favor, inténtelo de nuevo más tarde.</br>Si el problema persiste, contacte con el administrador del sistema.";
  } else {
    message = "Error no clasificado:";
  }

  if (error.status !== 500) {
    message +=
      '<div class="max-h-[240px] p-2 border-s-6 bg-stone-100 border-red-300 overflow-y-scroll">';
    message += error.response.data.response.errors
      .map((e) => {
        return `<li class="pl-4">${e}</li>`;
      })
      .join("");
    message += "</div>";
  }

  ElNotification({
    title: "Error",
    message,
    type: "error",
    duration: 3000,
    offset: 80,
    zIndex: 10000,
    dangerouslyUseHTMLString: true,
  });
};

const handleRequestSuccess = (response) => {
  ElNotification({
    title: "Éxito",
    message: response,
    type: "success",
    duration: 3000,
    offset: 80,
    zIndex: 10000,
  });
};

export { handleRequestError, handleRequestSuccess };
