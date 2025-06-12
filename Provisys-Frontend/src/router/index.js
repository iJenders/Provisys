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
          path: "purchases",
          name: "system-purchases",
          component: () => import("../views/System/PurchasesView.vue"),
        },
        {
          path: "payments",
          name: "system-payments",
          component: () => import("../views/System/PaymentsView.vue"),
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
          path: "employees",
          name: "system-employees",
          component: () => import("../views/System/EmployeesView.vue"),
        },
        {
          path: "reports",
          name: "system-reports",
          component: () => import("../views/System/ReportsView.vue"),
        },
      ],
    },
  ],
  scrollBehavior: (to, from, savedPosition) => {
    return { top: 0 };
  },
});

export default router;
