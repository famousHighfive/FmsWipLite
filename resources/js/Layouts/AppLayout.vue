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
const isHoveringSidebar = ref(false);

const menuConfig = {
  admin: {
    main: [
      { id: 'dashboard', label: 'Tableau de bord', icon: LayoutDashboard },
      { id: 'users', label: 'Utilisateurs', icon: Users },
      { id: 'employees', label: 'Employés', icon: UserPlus },
      { id: 'campaigns', label: 'Campagnes', icon: Megaphone },
      { id: 'planning', label: 'Planning', icon: Calendar },
      { id: 'timesheets', label: 'Feuilles de temps', icon: Clock },
      { id: 'reports', label: 'Rapports', icon: BarChart3 },
    ],
    sub: {
      dashboard: [
        { label: 'Vue générale', href: '/dashboard/admin' },
      ],
      users: [
        { label: 'Liste des utilisateurs', href: '/users' },
        { label: 'Ajouter un utilisateur', href: '/users/create' },
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

const sidebarWidth = computed(() => {
  if (isHoveringSidebar.value || !sidebarCollapsed.value) {
    return 'w-64';
  }
  return 'w-20';
});
</script>

<template>
  <div class="flex h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Sidebar principale -->
    <aside
      :class="[
        'bg-white/70 backdrop-blur-xl border-r border-blue-100/50 text-slate-700 flex flex-col transition-all duration-300 shadow-lg',
        sidebarWidth
      ]"
      @mouseenter="isHoveringSidebar = true"
      @mouseleave="isHoveringSidebar = false"
    >
      <div class="p-4 border-b border-blue-100/50 flex items-center bg-gradient-to-r from-blue-600 to-indigo-600">
        <Link :href="route('dashboard')" class="flex items-center gap-3">
          <ApplicationLogo class="h-8 w-auto fill-current text-white" />
          <span v-if="isHoveringSidebar || !sidebarCollapsed" class="text-xl font-bold text-white">WipLite</span>
        </Link>
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <button
          v-for="item in currentMenu.main"
          :key="item.id"
          @click="handleMainMenuClick(item.id)"
          :class="[
            'w-full flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group',
            activeMainMenu === item.id
              ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/30'
              : 'text-slate-600 hover:bg-white/80 hover:shadow-md hover:border hover:border-blue-100'
          ]"
        >
          <component :is="item.icon" class="w-6 h-6 flex-shrink-0" />
          <span v-if="isHoveringSidebar || !sidebarCollapsed" class="font-medium">{{ item.label }}</span>
        </button>
      </nav>
      <div class="p-4 border-t border-blue-100/50 bg-blue-50/50">
        <Dropdown align="left" width="48">
          <template #trigger>
            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-600 hover:bg-white/80 hover:shadow-md transition-all duration-300">
              <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center flex-shrink-0 text-white font-bold shadow-md">
                {{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </div>
              <div v-if="isHoveringSidebar || !sidebarCollapsed" class="text-left flex-1">
                <div class="font-medium text-sm text-slate-800">{{ page.props.auth.user?.name }}</div>
                <div class="text-xs text-blue-600 font-semibold">{{ page.props.auth.role }}</div>
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
      class="bg-white/80 backdrop-blur-lg border-r border-blue-100/50 flex flex-col transition-all duration-300 shadow-sm"
      :class="[
        currentSubMenu.length > 0 ? 'w-64' : 'w-0 opacity-0 overflow-hidden'
      ]"
    >
      <div class="p-4 border-b border-blue-100/50 bg-gradient-to-r from-blue-50 to-indigo-50">
        <h3 class="font-bold text-blue-800">
          {{ currentMenu.main.find(m => m.id === activeMainMenu)?.label }}
        </h3>
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <Link
          v-for="item in currentSubMenu"
          :key="item.href"
          :href="item.href"
          class="block px-4 py-3 rounded-2xl text-slate-700 hover:bg-gradient-to-r from-blue-50 to-indigo-50 hover:text-blue-800 hover:shadow-md font-medium transition-all duration-200"
        >
          {{ item.label }}
        </Link>
      </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="flex-1 flex flex-col overflow-hidden bg-transparent">
      <header class="bg-white/60 backdrop-blur-lg border-b border-blue-100/50 px-8 py-5 shadow-sm">
        <slot name="header" />
      </header>
      <div class="flex-1 overflow-auto p-8">
        <slot />
      </div>
    </main>
  </div>
</template>
