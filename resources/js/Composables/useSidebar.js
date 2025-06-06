// resources/js/composables/useSidebar.js
import { ref } from 'vue'

const sidebarExpandido = ref(false) // o false según estado inicial

export function useSidebar() {
  const toggleSidebar = () => {
    sidebarExpandido.value = !sidebarExpandido.value
  }

  return {
    sidebarExpandido,
    toggleSidebar
  }
}
