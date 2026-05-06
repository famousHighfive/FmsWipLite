<template>
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Sidebar (Identique au Dashboard pour la cohérence) -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
      <div class="p-6">
        <h1 class="text-2xl font-bold text-indigo-600 tracking-tight">WIPIL</h1>
        <p class="text-xs text-slate-400 uppercase font-semibold mt-1">Administrateur</p>
      </div>

      <nav class="flex-1 px-4 space-y-2">
        <a href="/dashboard/admin" class="flex items-center space-x-3 text-slate-600 hover:bg-slate-100 p-3 rounded-xl transition-all">
          <LayoutDashboardIcon class="w-5 h-5" />
          <span class="font-medium">Tableau de bord</span>
        </a>
        <a href="#" class="flex items-center space-x-3 bg-indigo-600 text-white p-3 rounded-xl shadow-lg shadow-indigo-100">
          <UsersIcon class="w-5 h-5" />
          <span class="font-medium">Utilisateurs</span>
        </a>
        <!-- ... autres liens ... -->
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
      <!-- Header -->
      <header class="flex justify-between items-center mb-8">
        <div>
          <h2 class="text-3xl font-bold text-slate-800">Gestion des Utilisateurs</h2>
          <p class="text-slate-500 mt-1">{{ users.length }} utilisateurs au total</p>
        </div>
        <button class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-indigo-100 transition-all">
          <UserPlusIcon class="w-5 h-5" />
          <span>Nouvel Utilisateur</span>
        </button>
      </header>

      <!-- Table Section -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-white">
          <h3 class="font-bold text-slate-700">Liste des Utilisateurs</h3>

          <!-- Search Bar -->
          <div class="relative w-80">
            <SearchIcon class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              type="text"
              placeholder="Rechercher par nom ou matricule..."
              class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all"
            />
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50/50">
              <tr>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Matricule</th>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Nom</th>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Rôle</th>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Statut</th>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Campagne</th>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Créé le</th>
                <th class="px-6 py-4 text-xs uppercase font-bold text-slate-500 tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="user in users" :key="user.matricule" class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-6 py-4">
                  <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded text-sm">
                    {{ user.matricule }}
                  </span>
                </td>
                <td class="px-6 py-4 font-semibold text-slate-700">{{ user.nom }}</td>
                <td class="px-6 py-4">
                  <span :class="['px-3 py-1 rounded-full text-xs font-bold', getRoleClass(user.role)]">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="['px-2.5 py-0.5 rounded text-xs font-bold ring-1 ring-inset', user.statut === 'Actif' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-rose-600/20']">
                    {{ user.statut }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ user.campagne }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ user.cree_le }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="p-1.5 hover:bg-white hover:shadow-sm rounded-lg text-slate-400 hover:text-indigo-600 transition-all">
                      <Edit3Icon class="w-4 h-4" />
                    </button>
                    <button class="p-1.5 hover:bg-white hover:shadow-sm rounded-lg text-slate-400 hover:text-rose-600 transition-all">
                      <Trash2Icon class="w-4 h-4" />
                    </button>
                    <button class="p-1.5 hover:bg-white hover:shadow-sm rounded-lg text-slate-400 hover:text-slate-600 transition-all">
                      <MoreVerticalIcon class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import {
  LayoutDashboardIcon, UsersIcon, UserPlusIcon,
  SearchIcon, Edit3Icon, Trash2Icon, MoreVerticalIcon
} from 'lucide-vue-next';

// Données en dur conformes à l'image
const users = [
  { matricule: 'ADM-001', nom: 'Jean Dupont', role: 'Admin', statut: 'Actif', campagne: '-', cree_le: '2024-01-15' },
  { matricule: 'CP-003', nom: 'Marie Martin', role: 'Chef de Plateau', statut: 'Actif', campagne: 'Assurance Auto', cree_le: '2024-02-10' },
  { matricule: 'SUP-012', nom: 'Pierre Dubois', role: 'Superviseur', statut: 'Actif', campagne: 'Crédit Conso', cree_le: '2024-03-05' },
  { matricule: 'TC-045', nom: 'Alice Durand', role: 'Téléconseiller', statut: 'Actif', campagne: 'Assurance Auto', cree_le: '2024-04-12' },
  { matricule: 'TC-087', nom: 'Bob Mercier', role: 'Téléconseiller', statut: 'Inactif', campagne: 'Mutuelle Santé', cree_le: '2024-01-20' },
  { matricule: 'SUP-018', nom: 'Sophie Laurent', role: 'Superviseur', statut: 'Actif', campagne: 'Assurance Auto', cree_le: '2024-02-28' },
];

const getRoleClass = (role) => {
  switch (role) {
    case 'Admin': return 'bg-slate-100 text-slate-700';
    case 'Chef de Plateau': return 'bg-indigo-100 text-indigo-700';
    case 'Superviseur': return 'bg-emerald-100 text-emerald-700';
    case 'Téléconseiller': return 'bg-amber-100 text-amber-700';
    default: return 'bg-slate-100 text-slate-700';
  }
};
</script>
