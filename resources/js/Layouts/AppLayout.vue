<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import {
  LayoutDashboard,
  Users,
  Megaphone,
  Calendar,
  Clock,
  BarChart3,
  Settings,
  UserPlus,
  Briefcase,
  CheckSquare
} from 'lucide-vue-next';

const page = usePage();
const activeMainMenu = ref('dashboard');
const sidebarCollapsed = ref(false);

const menuConfig = {
  admin: {
    main: [
      { id: 'dashboard', label: 'Tableau de bord', icon: LayoutDashboard },
      { id: 'employees', label: 'Employés', icon: Users },
      { id: 'campaigns', label: 'Campagnes', icon: Megaphone },
      { id: 'planning', label: 'Planning', icon: Calendar },
      { id: 'timesheets', label: 'Feuilles de temps', icon: Clock },
      { id: 'reports', label: 'Rapports', icon: BarChart3 },
    ],
    sub: {
      dashboard: [
        { label: 'Vue générale', href: '/dashboard/admin' },
      ],
      employees: [
        { label: 'Liste des employés', href: '/employees' },
        { label: 'Ajouter un employé', href: '/employees/create' },
      ],
      campaigns: [
        { label: 'Liste des campagnes', href: '/campaigns' },
        { label: 'Ajouter une campagne', href: '/campaigns/create' },
      ],
      planning: [
        { label: 'Modèles de planning', href: '/planning/models' },
        { label: 'Affectations', href: '/planning/assignments' },
      ],
      timesheets: [
        { label: 'Validation', href: '/timesheets/validate' },
        { label: 'Historique', href: '/timesheets/history' },
      ],
      reports: [
        { label: 'Présence', href: '/reports/attendance' },
        { label: 'Productivité', href: '/reports/productivity' },
      ],
    },
  },
  cp: {
    main: [
      { id: 'dashboard', label: 'Tableau de bord', icon: LayoutDashboard },
      { id: 'employees', label: 'Équipe', icon: Users },
      { id: 'campaigns', label: 'Campagnes', icon: Megaphone },
      { id: 'planning', label: 'Planning', icon: Calendar },
      { id: 'timesheets', label: 'Feuilles de temps', icon: Clock },
    ],
    sub: {
      dashboard: [
        { label: 'Vue générale', href: '/dashboard/cp' },
      ],
      employees: [
        { label: 'Mon équipe', href: '/employees' },
      ],
      campaigns: [
        { label: 'Campagnes actives', href: '/campaigns' },
      ],
      planning: [
        { label: 'Gérer le planning', href: '/planning' },
      ],
      timesheets: [
        { label: 'Valider les feuilles', href: '/timesheets/validate' },
      ],
    },
  },
  sup: {
    main: [
      { id: 'dashboard', label: 'Tableau de bord', icon: LayoutDashboard },
      { id: 'team', label: 'Mon équipe', icon: Users },
      { id: 'timesheets', label: 'Feuilles de temps', icon: Clock },
    ],
    sub: {
      dashboard: [
        { label: 'Vue générale', href: '/dashboard/sup' },
      ],
      team: [
        { label: 'Liste des agents', href: '/employees' },
      ],
      timesheets: [
        { label: 'Valider', href: '/timesheets/validate' },
      ],
    },
  },
  tc: {
    main: [
      { id: 'dashboard', label: 'Tableau de bord', icon: LayoutDashboard },
      { id: 'timesheets', label: 'Ma feuille de temps', icon: Clock },
      { id: 'planning', label: 'Mon planning', icon: Calendar },
    ],
    sub: {
      dashboard: [
        { label: 'Vue générale', href: '/dashboard/tc' },
      ],
      timesheets: [
        { label: 'Saisir mes heures', href: '/timesheets/create' },
        { label: 'Historique', href: '/timesheets' },
      ],
      planning: [
        { label: 'Voir mon planning', href: '/planning' },
      ],
    },
  },
};

const currentMenu = computed(() => {
  const role = page.props.auth.role;
  return menuConfig[role] || menuConfig.tc;
});

const currentSubMenu = computed(() => {
  return currentMenu.value.sub[activeMainMenu.value] || [];
});

const handleMainMenuClick = (itemId) => {
  activeMainMenu.value = itemId;
  sidebarCollapsed.value = currentSubMenu.value.length > 0;
};
</script>

<template>
  <div class="flex h-screen bg-emerald-50">
    <!-- Sidebar principale -->
    <aside
      :class="[
        'bg-emerald-900 text-white flex flex-col transition-all duration-300',
        sidebarCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <div class="p-4 border-b border-emerald-800 flex items-center">
        <Link :href="route('dashboard')" class="flex items-center gap-3">
          <ApplicationLogo class="h-8 w-auto fill-current text-emerald-300" />
          <span v-if="!sidebarCollapsed" class="text-xl font-bold text-emerald-100">WipLite</span>
        </Link>
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <button
          v-for="item in currentMenu.main"
          :key="item.id"
          @click="handleMainMenuClick(item.id)"
          :class="[
            'w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200',
            activeMainMenu === item.id
              ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50'
              : 'text-emerald-200 hover:bg-emerald-800 hover:text-white'
          ]"
        >
          <component :is="item.icon" class="w-6 h-6 flex-shrink-0" />
          <span v-if="!sidebarCollapsed" class="font-medium">{{ item.label }}</span>
        </button>
      </nav>
      <div class="p-4 border-t border-emerald-800">
        <Dropdown align="left" width="48">
          <template #trigger>
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-200 hover:bg-emerald-800 hover:text-white transition-all duration-200">
              <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center flex-shrink-0">
                {{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </div>
              <div v-if="!sidebarCollapsed" class="text-left flex-1">
                <div class="font-medium text-sm">{{ page.props.auth.user?.name }}</div>
                <div class="text-xs text-emerald-400">{{ page.props.auth.role }}</div>
              </div>
            </button>
          </template>
          <template #content>
            <DropdownLink :href="route('profile.edit')">
              Profile
            </DropdownLink>
            <DropdownLink :href="route('logout')" method="post" as="button">
              Log Out
            </DropdownLink>
          </template>
        </Dropdown>
      </div>
    </aside>

    <!-- Sidebar secondaire -->
    <aside
      class="bg-white border-r border-emerald-100 flex flex-col transition-all duration-300"
      :class="[
        currentSubMenu.length > 0 ? 'w-64' : 'w-0 opacity-0 overflow-hidden'
      ]"
    >
      <div class="p-4 border-b border-emerald-100 bg-emerald-50">
        <h3 class="font-bold text-emerald-800">
          {{ currentMenu.main.find(m => m.id === activeMainMenu)?.label }}
        </h3>
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <Link
          v-for="item in currentSubMenu"
          :key="item.href"
          :href="item.href"
          class="block px-4 py-3 rounded-xl text-emerald-700 hover:bg-emerald-100 hover:text-emerald-900 font-medium transition-all duration-200"
        >
          {{ item.label }}
        </Link>
      </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="flex-1 flex flex-col overflow-hidden bg-emerald-50">
      <header class="bg-white border-b border-emerald-100 px-8 py-5 shadow-sm">
        <slot name="header" />
      </header>
      <div class="flex-1 overflow-auto p-8">
        <slot />
      </div>
    </main>
  </div>
</template>
