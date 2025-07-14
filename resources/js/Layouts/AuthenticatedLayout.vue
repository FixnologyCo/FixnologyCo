<script setup>
import { watch } from 'vue'; 
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
    auth: Object,
});

const authStore = useAuthStore();

watch(
    () => props.auth.user,
    (newUser) => {
        
        authStore.setUser(newUser);
    },
    {
      
        immediate: true 
    }
);
</script>

<template>
  <div>
    <main>
      <slot /> </main>
  </div>
</template>