<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlus, Edit, Trash2, Search } from 'lucide-vue-next';

const props = defineProps({
  users: Object,
});

const handleDelete = (user) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')) {
    router.delete(route('users.destroy', user.id), {
      onSuccess: () => {
        // Success handled by Inertia
      }
    });
  }
};
</script>

<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-3xl font-bold text-slate-800">Gestion des Utilisateurs</h2>
          <p class="text-slate-500 mt-1">Gérez les comptes utilisateurs du système</p>
        </div>
        <Link
          :href="route('users.create')"
          class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all"
        >
          <UserPlus class="w-5 h-5" />
          <span>Nouvel Utilisateur</span>
        </Link>
      </div>
    </template>

    <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-blue-100/50 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
            <tr>
              <th class="px-6 py-4 text-xs uppercase font-bold text-slate-600 tracking-wider">ID</th>
              <th class="px-6 py-4 text-xs uppercase font-bold text-slate-600 tracking-wider">Nom</th>
              <th class="px-6 py-4 text-xs uppercase font-bold text-slate-600 tracking-wider">Email</th>
              <th class="px-6 py-4 text-xs uppercase font-bold text-slate-600 tracking-wider">Rôle</th>
              <th class="px-6 py-4 text-xs uppercase font-bold text-slate-600 tracking-wider">Créé le</th>
              <th class="px-6 py-4 text-xs uppercase font-bold text-slate-600 tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-blue-100/50">
            <tr v-for="user in users.data" :key="user.id" class="hover:bg-blue-50/50 transition-colors">
              <td class="px-6 py-4">
                <span class="font-mono font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded text-sm">
                  #{{ user.id }}
                </span>
              </td>
              <td class="px-6 py-4 font-semibold text-slate-800">{{ user.name }}</td>
              <td class="px-6 py-4 text-slate-600">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-bold',
                    user.role?.name === 'admin' ? 'bg-slate-100 text-slate-700' :
                    user.role?.name === 'cp' ? 'bg-blue-100 text-blue-700' :
                    user.role?.name === 'sup' ? 'bg-emerald-100 text-emerald-700' :
                    'bg-amber-100 text-amber-700'
                  ]"
                >
                  {{ user.role?.name }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-slate-500">
                {{ new Date(user.created_at).toLocaleDateString('fr-FR') }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <Link
                    :href="route('users.edit', user.id)"
                    class="p-2 hover:bg-white hover:shadow-md rounded-xl text-slate-500 hover:text-blue-600 transition-all"
                  >
                    <Edit class="w-4 h-4" />
                  </Link>
                  <button
                    @click="handleDelete(user)"
                    class="p-2 hover:bg-white hover:shadow-md rounded-xl text-slate-500 hover:text-red-600 transition-all"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="users.links && users.links.length > 3" class="px-6 py-4 border-t border-blue-100/50 bg-white/50">
        <div class="flex justify-between items-center">
          <div class="text-sm text-slate-500">
            Affichage de {{ users.from }} à {{ users.to }} sur {{ users.total }} résultats
          </div>
          <div class="flex gap-2">
            <template v-for="link in users.links" :key="link.url || Math.random()">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'px-3 py-1 rounded-lg text-sm font-medium transition-all',
                  link.active
                    ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md'
                    : 'text-slate-700 hover:bg-blue-50'
                ]"
                v-html="link.label"
              />
              <span
                v-else
                class="px-3 py-1 rounded-lg text-sm font-medium text-slate-400 cursor-not-allowed"
                v-html="link.label"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
