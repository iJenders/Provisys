<template>
  <div class="about-container bg-white min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-12 md:py-16">
      <!-- Company Header -->
      <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">
          Comercializadora De Alimentos Lara C.A.
        </h1>
        <h2 class="text-xl md:text-2xl font-medium text-gray-600">CODALCA</h2>
      </div>
      
      <!-- Company Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Location Card -->
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
          <div class="flex items-start mb-4">
            <div class="bg-emerald-100 p-3 rounded-full mr-4">
              <MapPin class="h-6 w-6 text-emerald-600" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Ubicación</h3>
              <p class="text-gray-600">
                Avenida Intercomunal Barquisimeto-Acarigua, Sector el Troncal.
              </p>
            </div>
          </div>
        </div>
        
        <!-- Activity Card -->
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm">
          <div class="flex items-start mb-4">
            <div class="bg-emerald-100 p-3 rounded-full mr-4">
              <ShoppingBag class="h-6 w-6 text-emerald-600" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Actividad Principal</h3>
              <p class="text-gray-600">
                Comercialización de alimentos para consumo masivo a empresas Venezolanas, 
                con especial enfoque en el estado Lara, precisamente en las ciudades de 
                Barquisimeto y Cabudare.
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Mission -->
      <div class="bg-emerald-50 rounded-lg p-8 shadow-sm border border-emerald-100">
        <div class="flex items-start mb-6">
          <div class="bg-emerald-100 p-3 rounded-full mr-4">
            <Target class="h-6 w-6 text-emerald-600" />
          </div>
          <h3 class="text-xl font-semibold text-gray-800">Nuestra Misión</h3>
        </div>
        <blockquote class="pl-4 border-l-4 border-emerald-300 italic text-gray-700">
          "Distribuimos y comercializamos productos alimenticios de consumo masivo e higiene 
          personal a empresas venezolanas con una clara visión de crecimiento y desarrollo. 
          Gracias al compromiso de nuestro equipo, proveedores, aliados y clientes, garantizamos 
          la presencia de marcas de alta calidad en los anaqueles, lo que genera presencia en el 
          mercado y un crecimiento sostenido del negocio."
        </blockquote>
      </div>
      
      <!-- Company Values -->
      <div class="mt-12">
        <h3 class="text-xl font-semibold text-gray-800 mb-6 text-center">Nuestros Valores</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-gray-50 p-4 rounded-lg text-center">
            <div class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
              <Award class="h-6 w-6 text-emerald-600" />
            </div>
            <h4 class="font-medium text-gray-800 mb-1">Calidad</h4>
            <p class="text-sm text-gray-600">Productos de alta calidad para nuestros clientes</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg text-center">
            <div class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
              <Users class="h-6 w-6 text-emerald-600" />
            </div>
            <h4 class="font-medium text-gray-800 mb-1">Compromiso</h4>
            <p class="text-sm text-gray-600">Dedicación con nuestros clientes y proveedores</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg text-center">
            <div class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
              <TrendingUp class="h-6 w-6 text-emerald-600" />
            </div>
            <h4 class="font-medium text-gray-800 mb-1">Crecimiento</h4>
            <p class="text-sm text-gray-600">Visión clara de desarrollo y expansión</p>
          </div>
          <div class="bg-gray-50 p-4 rounded-lg text-center">
            <div class="bg-emerald-100 p-3 rounded-full mx-auto mb-3 w-14 h-14 flex items-center justify-center">
              <Truck class="h-6 w-6 text-emerald-600" />
            </div>
            <h4 class="font-medium text-gray-800 mb-1">Distribución</h4>
            <p class="text-sm text-gray-600">Presencia efectiva en el mercado venezolano</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { MapPin, ShoppingBag, Target, Award, Users, TrendingUp, Truck } from 'lucide-vue-next';
</script>

<style scoped>
/* Additional custom styles can be added here if needed */
</style>