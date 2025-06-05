<template>
    <div>
      <notifications />
      <slot />
    </div>
  </template>
  
  <script>
  import Notifications from './Components/DeteccionInternet/Notifications.vue';
  
  export default {
    components: {
      Notifications, 
    }
  }
  </script>

  <script setup>
  onMounted(() => {
  const userPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const savedTheme = localStorage.getItem('tema');

  if (savedTheme === 'oscuro' || (!savedTheme && userPrefersDark)) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
});
  </script>