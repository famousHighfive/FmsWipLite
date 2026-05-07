<template>
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Sidebar Persistante -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full">
      <div class="p-6">
        <h1 class="text-2xl font-bold text-indigo-600 tracking-tight">WIPIL</h1>
        <p class="text-xs text-slate-400 uppercase font-semibold mt-1">Administrateur</p>
      </div>

      <nav class="flex-1 px-4 space-y-2">
        <Link href="/dashboard/admin" :class="[route().current('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-600 hover:bg-slate-100', 'flex items-center space-x-3 p-3 rounded-xl transition-all']">
          <LayoutDashboardIcon class="w-5 h-5" />
          <span class="font-medium">Tableau de bord</span>
        </Link>

        <Link href="/admin/users" :class="[route().current('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-600 hover:bg-slate-100', 'flex items-center space-x-3 p-3 rounded-xl transition-all']">
          <UsersIcon class="w-5 h-5" />
          <span class="font-medium">Utilisateurs</span>
        </Link>
        <!-- Autres liens ici -->
      </nav>
    </aside>

    <!-- Zone de contenu dynamique -->
    <main class="flex-1 ml-64 p-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { LayoutDashboardIcon, UsersIcon } from 'lucide-vue-next';
</script>
