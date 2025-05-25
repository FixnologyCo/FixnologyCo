<template>
     <div>

    <Head title="Configuraciones" />

    <div class="flex">
      <SidebarSuperAdmin :auth="auth" :cantidad-apps="cantidadApps" :cantidad-clientes-rol1="cantidadClientesRol1"
        :usuarios-rol4="usuariosRol4" />

      <div class="w-full">
        <Header :auth="auth" />

        <div class="contenido px-4">
          <p class="text-[25px] my-3"> {{ user.nombres_ct }}</p>

        </div>
      </div>
    </div>
  </div>
</template>