import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
    },
    {
      path: "/shop",
      name: "shop",
      component: () => import("../views/ShopView.vue"),
    },
    {
      path: "/about",
      name: "about",
      component: () => import("../views/AboutView.vue"),
    },
    {
      path: "/contact",
      name: "contact",
      component: () => import("../views/ContactView.vue"),
    },
    {
      path: "/register",
      name: "register",
      component: () => import("../views/RegisterView.vue"),
    },
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
    },
    {
      path: "/logout",
      name: "logout",
      component: () => import("../views/LogoutView.vue"),
    },
    {
      path: "/profile",
      name: "profile",
      component: () => import("../views/ProfileView.vue"),
    },
    {
      path: "/system",
      name: "system",
      component: () => import("../views/SystemView.vue"),
      children: [
        {
          path: "summary",
          name: "system-summary",
          component: () => import("../views/System/SummaryView.vue"),
        },
        {
          path: "orders",
          name: "system-orders",
          component: () => import("../views/System/OrdersView.vue"),
        },
        {
          path: "products",
          name: "system-products",
          component: () => import("../views/System/ProductsView.vue"),
        },
        {
          path: "inventory",
          name: "system-inventory",
          component: () => import("../views/System/InventoryView.vue"),
        },
        {
          path: "customers",
          name: "system-customers",
          component: () => import("../views/System/CustomersView.vue"),
        },
        {
          path: "reports",
          name: "system-reports",
          component: () => import("../views/System/ReportsView.vue"),
        },
      ],
    },
  ],
});

export default router;
