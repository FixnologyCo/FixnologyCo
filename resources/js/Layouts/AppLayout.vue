<template>
  <div>
    <slot /> <!-- Aquí Inertia renderiza la página -->
    <Notifications />
  </div>
</template>

<script setup>
import Notifications from '@/Components/DeteccionInternet/Notifications.vue';
</script>
