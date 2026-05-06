<template>
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Sidebar (Reprise de l'image) -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col">
      <div class="p-6">
        <h1 class="text-2xl font-bold text-indigo-600 tracking-tight">WIPIL</h1>
        <p class="text-xs text-slate-400 uppercase font-semibold mt-1">Administrateur</p>
      </div>

      <nav class="flex-1 px-4 space-y-2">
        <a href="#" class="flex items-center space-x-3 bg-indigo-600 text-white p-3 rounded-xl shadow-lg shadow-indigo-100">
          <LayoutDashboardIcon class="w-5 h-5" />
          <span class="font-medium">Tableau de bord</span>
        </a>
        <a href="/employees" class="flex items-center space-x-3 text-slate-600 hover:bg-slate-100 p-3 rounded-xl transition-all">
          <UsersIcon class="w-5 h-5" />
          <span class="font-medium">Utilisateurs</span>
        </a>
        <a href="#" class="flex items-center space-x-3 text-slate-600 hover:bg-slate-100 p-3 rounded-xl transition-all">
          <MegaphoneIcon class="w-5 h-5" />
          <span class="font-medium">Campagnes</span>
        </a>
        <a href="#" class="flex items-center space-x-3 text-slate-600 hover:bg-slate-100 p-3 rounded-xl transition-all">
          <NetworkIcon class="w-5 h-5" />
          <span class="font-medium">Affectations</span>
        </a>
        <a href="#" class="flex items-center space-x-3 text-slate-600 hover:bg-slate-100 p-3 rounded-xl transition-all">
          <SettingsIcon class="w-5 h-5" />
          <span class="font-medium">Paramètres</span>
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
      <header class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800">Tableau de bord Administrateur</h2>
        <p class="text-slate-500 mt-1">Vue d'ensemble de la plateforme WIPIL</p>
      </header>

      <!-- Stats Cards Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div v-for="stat in stats" :key="stat.title" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-slate-500 mb-1">{{ stat.title }}</p>
              <h3 class="text-3xl font-bold text-slate-800">{{ stat.value }}</h3>
              <p :class="['text-sm mt-2 font-semibold', stat.trendUp ? 'text-emerald-500' : 'text-rose-500']">
                {{ stat.trendUp ? '↑' : '↓' }} {{ stat.trend }}
              </p>
            </div>
            <div :class="`p-3 rounded-xl ${stat.bgColor}`">
              <component :is="stat.icon" :class="`w-6 h-6 ${stat.iconColor}`" />
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Activités Récentes (Col 1 & 2) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
          <h4 class="text-lg font-bold text-slate-800 mb-6">Activités Récentes</h4>
          <div class="space-y-6">
            <div v-for="(activity, index) in activities" :key="index" class="flex items-start space-x-4">
              <div :class="`mt-1.5 w-3 h-3 rounded-full shrink-0 ${activity.dotColor}`"></div>
              <div class="flex-1 border-b border-slate-50 pb-4">
                <p class="font-bold text-slate-700">{{ activity.title }}</p>
                <p class="text-slate-500 text-sm">{{ activity.subtitle }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ activity.time }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Répartition par Rôle (Col 3) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
          <h4 class="text-lg font-bold text-slate-800 mb-6">Répartition par Rôle</h4>
          <div class="space-y-6">
            <div v-for="role in roles" :key="role.name">
              <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-semibold text-slate-600">{{ role.name }}</span>
                <span class="text-sm font-bold text-slate-800">{{ role.count }}</span>
              </div>
              <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                <div
                  :class="`h-full rounded-full ${role.color}`"
                  :style="{ width: (role.count / 250 * 100) + '%' }">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import {
  LayoutDashboardIcon, UsersIcon, MegaphoneIcon,
  NetworkIcon, SettingsIcon, UserCheckIcon,
  BriefcaseIcon, TrendingUpIcon
} from 'lucide-vue-next';

const stats = [
  { title: 'Total Utilisateurs', value: '248', trend: '+12 ce mois', trendUp: true, icon: UsersIcon, iconColor: 'text-indigo-600', bgColor: 'bg-indigo-50' },
  { title: 'Campagnes Actives', value: '15', trend: '+3 ce mois', trendUp: true, icon: BriefcaseIcon, iconColor: 'text-emerald-600', bgColor: 'bg-emerald-50' },
  { title: 'Chefs de Plateau', value: '8', trend: 'Stable', trendUp: true, icon: UserCheckIcon, iconColor: 'text-amber-600', bgColor: 'bg-amber-50' },
  { title: "Taux d'Activité", value: '94%', trend: '+2%', trendUp: true, icon: TrendingUpIcon, iconColor: 'text-rose-600', bgColor: 'bg-rose-50' },
];

const activities = [
  { title: 'Nouvel utilisateur créé', subtitle: 'Jean Dupont (TC)', time: 'Il y a 5 min', dotColor: 'bg-indigo-500' },
  { title: 'Campagne activée', subtitle: 'Campagne Assurance Auto', time: 'Il y a 15 min', dotColor: 'bg-emerald-500' },
  { title: 'Affectation modifiée', subtitle: 'Marie Martin → SUP Campagne Banque', time: 'Il y a 1h', dotColor: 'bg-amber-500' },
  { title: 'Rôle modifié', subtitle: 'Pierre Dubois → Chef de Plateau', time: 'Il y a 2h', dotColor: 'bg-rose-500' },
];

const roles = [
  { name: 'Téléconseillers', count: 180, color: 'bg-indigo-500' },
  { name: 'Superviseurs', count: 45, color: 'bg-emerald-500' },
  { name: 'Chefs de Plateau', count: 8, color: 'bg-amber-500' },
  { name: 'Administrateurs', count: 15, color: 'bg-slate-700' },
];
</script>

<style scoped>
/* Optionnel : Personnalisation de la scrollbar */
main::-webkit-scrollbar {
  width: 6px;
}
main::-webkit-scrollbar-thumb {
  background-color: #e2e8f0;
  border-radius: 10px;
}
</style>
