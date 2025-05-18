<script setup>
import { ref, reactive, onMounted } from 'vue';
import {
    MapPin,
    Phone,
    Mail,
    Clock,
    Facebook,
    Instagram,
    Twitter,
    Linkedin,
    CheckCircle,
    Loader
} from 'lucide-vue-next';
import L from 'leaflet';

const form = reactive({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const errors = reactive({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const isSubmitting = ref(false);
const formSubmitted = ref(false);

const validateForm = () => {
    let isValid = true;

    errors.name = '';
    errors.email = '';
    errors.subject = '';
    errors.message = '';

    if (!form.name.trim()) {
        errors.name = 'El nombre es requerido';
        isValid = false;
    }

    if (!form.email.trim()) {
        errors.email = 'El correo electrónico es requerido';
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Por favor ingresa un correo electrónico válido';
        isValid = false;
    }

    if (!form.subject.trim()) {
        errors.subject = 'El asunto es requerido';
        isValid = false;
    }

    if (!form.message.trim()) {
        errors.message = 'El mensaje es requerido';
        isValid = false;
    } else if (form.message.trim().length < 10) {
        errors.message = 'El mensaje debe tener al menos 10 caracteres';
        isValid = false;
    }

    return isValid;
};

const submitForm = async () => {
    if (!validateForm()) {
        return;
    }

    isSubmitting.value = true;

    try {
        await new Promise(resolve => setTimeout(resolve, 1500));

        form.name = '';
        form.email = '';
        form.subject = '';
        form.message = '';

        formSubmitted.value = true;

        setTimeout(() => {
            formSubmitted.value = false;
        }, 5000);
    } catch (error) {
        console.error('Error submitting form:', error);
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(() => {
    let mapOptions = {
        center: [10.022526, -69.243098],
        zoom: 18
    }
    let map = new L.map('map', mapOptions);

    let layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
    map.addLayer(layer);

    let marker = new L.Marker([10.02235, -69.2431]);
    marker.addTo(map);
})
</script>

<template>
    <div class="contact-container bg-white min-h-screen">
        <div class="max-w-5xl mx-auto px-4 py-12 md:py-16">
            <!-- Contact Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Contáctanos</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Estamos aquí para responder a tus preguntas y ayudarte con tus necesidades de distribución de
                    alimentos.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Información de Contacto</h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-emerald-100 p-3 rounded-full mr-4">
                                <MapPin class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 mb-1">Dirección</h3>
                                <p class="text-gray-600">
                                    Avenida Intercomunal Barquisimeto-Acarigua, Sector el Troncal.
                                    <br>Barquisimeto, Estado Lara, Venezuela
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-emerald-100 p-3 rounded-full mr-4">
                                <Phone class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 mb-1">Teléfono</h3>
                                <p class="text-gray-600">+58 (251) 123-4567</p>
                                <p class="text-gray-600">+58 (414) 987-6543</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-emerald-100 p-3 rounded-full mr-4">
                                <Mail class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 mb-1">Correo Electrónico</h3>
                                <p class="text-gray-600">info@codalca.com</p>
                                <p class="text-gray-600">ventas@codalca.com</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-emerald-100 p-3 rounded-full mr-4">
                                <Clock class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-800 mb-1">Horario de Atención</h3>
                                <p class="text-gray-600">Lunes a Viernes: 8:00 AM - 5:00 PM</p>
                                <p class="text-gray-600">Sábado: 8:00 AM - 12:00 PM</p>
                                <p class="text-gray-600">Domingo: Cerrado</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Síguenos</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-emerald-100 p-3 rounded-full hover:bg-emerald-200 transition-colors">
                                <Facebook class="h-5 w-5 text-emerald-600" />
                            </a>
                            <a href="#" class="bg-emerald-100 p-3 rounded-full hover:bg-emerald-200 transition-colors">
                                <Instagram class="h-5 w-5 text-emerald-600" />
                            </a>
                            <a href="#" class="bg-emerald-100 p-3 rounded-full hover:bg-emerald-200 transition-colors">
                                <Twitter class="h-5 w-5 text-emerald-600" />
                            </a>
                            <a href="#" class="bg-emerald-100 p-3 rounded-full hover:bg-emerald-200 transition-colors">
                                <Linkedin class="h-5 w-5 text-emerald-600" />
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Envíanos un Mensaje</h2>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div v-if="formSubmitted"
                            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                            <div class="flex">
                                <CheckCircle class="h-5 w-5 mr-2" />
                                <span>¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.</span>
                            </div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre
                                Completo</label>
                            <input type="text" id="name" v-model="form.name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                :class="{ 'border-red-500': errors.name }" placeholder="Tu nombre" required />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo
                                Electrónico</label>
                            <input type="email" id="email" v-model="form.email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                :class="{ 'border-red-500': errors.email }" placeholder="tu@email.com" required />
                            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                            <input type="text" id="subject" v-model="form.subject"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                :class="{ 'border-red-500': errors.subject }" placeholder="Asunto de tu mensaje"
                                required />
                            <p v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</p>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                            <textarea id="message" v-model="form.message" rows="5"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                :class="{ 'border-red-500': errors.message }" placeholder="Escribe tu mensaje aquí..."
                                required></textarea>
                            <p v-if="errors.message" class="mt-1 text-sm text-red-600">{{ errors.message }}</p>
                        </div>

                        <div>
                            <button type="submit"
                                class="w-full bg-emerald-600 text-white py-3 px-6 rounded-lg hover:bg-emerald-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                :disabled="isSubmitting">
                                <div class="flex items-center justify-center">
                                    <Loader v-if="isSubmitting" class="animate-spin mr-2 h-5 w-5" />
                                    <span>{{ isSubmitting ? 'Enviando...' : 'Enviar Mensaje' }}</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-16">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Nuestra Ubicación</h2>
                <div class="h-80" id="map">

                </div>
            </div>
        </div>
    </div>
</template>