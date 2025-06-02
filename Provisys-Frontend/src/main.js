import { createApp } from "vue";
import { createPinia } from "pinia";
import ElementPlus from "element-plus";
import es from "element-plus/es/locale/lang/es";

import "element-plus/dist/index.css";
import "leaflet/dist/leaflet.css";
import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(ElementPlus, {
  locale: es,
});

app.mount("#app");
