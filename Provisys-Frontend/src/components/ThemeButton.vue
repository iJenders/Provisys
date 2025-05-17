<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    enabled: {
        type: Boolean,
        default: true
    },
    routerLink: {
        type: String,
        default: ''
    }
})
</script>

<template>
    <button type="button" v-if="!routerLink"
        class="flex justify-center items-center gap-2 px-4 py-2 border-2 transition ease-linear duration-100"
        :class="[enabled ? 'cursor-pointer' : 'cursor-not-allowed brightness-80']">
        <slot />
    </button>
    <RouterLink v-else :to="routerLink"
        class="flex justify-center items-center gap-2 px-4 py-2 border-2 transition ease-linear duration-100"
        :class="[enabled ? 'cursor-pointer' : 'cursor-not-allowed brightness-80']">
        <slot />
    </RouterLink>
</template>