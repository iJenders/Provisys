<script setup>
// The only one prop is the  orientation of the line

defineProps(['orientation']);
</script>

<template>
    <div :class="['Line', orientation]"></div>
</template>
<style scoped>
.Line {
    width: 100%;
    height: 2px;
}

.Line.horizontal {
    width: 100%;
    height: 2px;
}

.Line.vertical {
    width: 2px;
    height: 100%;
}
</style>