import { ElMessageBox, ElNotification } from "element-plus";

const confirmation = (title, message, successCallback, cancelCallback) => {
  ElMessageBox.confirm(message, title, {
    confirmButtonText: "Sí",
    cancelButtonText: "Cancelar",
    type: "warning",
    zIndex: 10100,
  })
    .then(() => {
      if (successCallback !== undefined) {
        successCallback();
      }
    })
    .catch(() => {
      if (cancelCallback !== undefined) {
        cancelCallback();
      }
    });
};

const errorNotification = (message) => {
  ElNotification({
    title: "Error",
    message: message,
    type: "error",
    duration: 5000,
    offset: 90,
    zIndex: 10100,
  });
};

const successNotification = (message) => {
  ElNotification({
    title: "Éxito",
    message: message,
    type: "success",
    duration: 5000,
    offset: 90,
    zIndex: 10100,
  });
};

const infoNotification = (message) => {
  ElNotification({
    title: "Información",
    message: message,
    type: "info",
    duration: 5000,
    offset: 90,
    zIndex: 10100,
  });
};

export {
  confirmation,
  errorNotification,
  successNotification,
  infoNotification,
};
