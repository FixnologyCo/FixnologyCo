<script setup>
import { watch } from "vue";
import { useInfoUser } from "@/stores/infoUsers";

const props = defineProps({
  auth: Object,
});

const authStore = useInfoUser();

watch(
  () => props.auth.user,
  (newUser) => {
    authStore.setUser(newUser);
  },
  {
    immediate: true,
  }
);
</script>

<template>
  <div>
    <main>
      <slot />
    </main>
  </div>
</template>
