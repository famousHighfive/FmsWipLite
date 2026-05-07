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
  UserPlus,
  GitBranch,
  Shield,
  Bell,
  User,
  TreePine,
} from 'lucide-vue-next';

const page = usePage();
const activeMainMenu = ref('dashboard');
const isHoveringSidebar = ref(false);

const menuConfig = {

  // ─── ADMIN ───────────────────────────────────────────────
  admin: {
    main: [
      { id: 'dashboard',    label: 'Dashboard',              icon: LayoutDashboard },
      { id: 'employees',    label: 'Gestion des employés',   icon: UserPlus },
      { id: 'campaigns',    label: 'Gestion des campagnes',  icon: Megaphone },
      { id: 'assignments',  label: 'Gestion des affectations', icon: GitBranch },
      { id: 'planning',     label: 'Gestion des plannings',  icon: Calendar },
      { id: 'timesheets',   label: 'Gestion des heures',     icon: Clock },
      { id: 'security',     label: 'Utilisateurs & Sécurité',icon: Shield },
      { id: 'reports',      label: 'Rapports',               icon: BarChart3 },
      { id: 'account',      label: 'Mon compte',             icon: User },
    ],
    sub: {
      dashboard: [
        { label: 'Tableau de bord',        href: '/dashboard/admin' },
        { label: 'Statistiques générales', href: '/dashboard/admin/stats' },
        { label: 'Alertes & notifications',href: '/dashboard/admin/alerts' },
      ],
      employees: [
        { label: 'Liste des employés',     href: '/employees' },
        { label: 'Ajouter un employé',     href: '/employees/create' },
        { label: 'Employés affectés',      href: '/employees/assigned' },
        { label: 'Employés non affectés',  href: '/employees/unassigned' },
        { label: 'Historique des employés',href: '/employees/history' },
      ],
      campaigns: [
        { label: 'Liste des campagnes',    href: '/campaigns' },
        { label: 'Créer une campagne',     href: '/campaigns/create' },
        { label: 'Campagnes actives',      href: '/campaigns/active' },
        { label: 'Campagnes terminées',    href: '/campaigns/closed' },
      ],
      assignments: [
        { label: 'Affectation CP → Campagne', href: '/assignments/cp' },
        { label: 'Affectation SUP → CP',      href: '/assignments/sup' },
        { label: 'Affectation TC → SUP',      href: '/assignments/tc' },
        { label: 'Vue hiérarchique',          href: '/assignments/hierarchy' },
        { label: 'Réaffectations',            href: '/assignments/reassign' },
        { label: 'Historique des affectations',href: '/assignments/history' },
      ],
      planning: [
        { label: 'Modèles de planning',    href: '/planning/models' },
        { label: 'Créer un planning',      href: '/planning/create' },
        { label: 'Affectation des plannings', href: '/planning/assignments' },
        { label: 'Validation des plannings',  href: '/planning/validate' },
        { label: 'Historique des plannings',  href: '/planning/history' },
      ],
      timesheets: [
        { label: 'Saisie des heures',      href: '/timesheets/entry' },
        { label: 'Validation des heures',  href: '/timesheets/validate' },
        { label: 'Historique des heures',  href: '/timesheets/history' },
        { label: 'Rapport des écarts',     href: '/timesheets/gaps' },
      ],
      security: [
        { label: 'Comptes utilisateurs',   href: '/users' },
        { label: 'Rôles & permissions',    href: '/users/roles' },
        { label: 'Journal d\'activité',    href: '/activity-logs' },
        { label: 'Paramètres système',     href: '/settings' },
      ],
      reports: [
        { label: 'Rapport RH',             href: '/reports/hr' },
        { label: 'Rapport campagnes',      href: '/reports/campaigns' },
        { label: 'Rapport affectations',   href: '/reports/assignments' },
        { label: 'Rapport heures travaillées', href: '/reports/timesheets' },
      ],
      account: [
        { label: 'Profil',                 href: '/profile' },
        { label: 'Modifier mot de passe',  href: '/profile/password' },
      ],
    },
  },

  // ─── CHEF DE PLATEAU ─────────────────────────────────────
  cp: {
    main: [
      { id: 'dashboard',    label: 'Dashboard',                  icon: LayoutDashboard },
      { id: 'campaigns',    label: 'Mes campagnes',              icon: Megaphone },
      { id: 'supervisors',  label: 'Gestion des superviseurs',   icon: Users },
      { id: 'teleconseillers', label: 'Gestion des téléconseillers', icon: UserPlus },
      { id: 'hierarchy',    label: 'Vue hiérarchique',           icon: TreePine },
      { id: 'planning',     label: 'Gestion des plannings',      icon: Calendar },
      { id: 'timesheets',   label: 'Gestion des heures',         icon: Clock },
      { id: 'reports',      label: 'Rapports',                   icon: BarChart3 },
      { id: 'account',      label: 'Mon compte',                 icon: User },
    ],
    sub: {
      dashboard: [
        { label: 'Tableau de bord',  href: '/dashboard/cp' },
        { label: 'Notifications',    href: '/notifications' },
      ],
      campaigns: [
        { label: 'Campagnes assignées',  href: '/campaigns' },
        { label: 'Détails des campagnes',href: '/campaigns/details' },
      ],
      supervisors: [
        { label: 'Liste des superviseurs', href: '/supervisors' },
        { label: 'Affecter un superviseur',href: '/assignments/sup' },
        { label: 'Libérer un superviseur', href: '/assignments/sup/release' },
      ],
      teleconseillers: [
        { label: 'Liste des téléconseillers',  href: '/teleconseillers' },
        { label: 'Affecter un téléconseiller', href: '/assignments/tc' },
        { label: 'Réaffecter un téléconseiller',href: '/assignments/tc/reassign' },
      ],
      hierarchy: [
        { label: 'Organisation des équipes', href: '/assignments/hierarchy' },
        { label: 'Vue arborescente',          href: '/assignments/tree' },
      ],
      planning: [
        { label: 'Modèles de planning',      href: '/planning/models' },
        { label: 'Créer un modèle',          href: '/planning/create' },
        { label: 'Affecter un planning',     href: '/planning/assignments' },
        { label: 'Validation des plannings', href: '/planning/validate' },
        { label: 'Historique des plannings', href: '/planning/history' },
      ],
      timesheets: [
        { label: 'Saisie des heures superviseurs', href: '/timesheets/entry' },
        { label: 'Validation des heures',          href: '/timesheets/validate' },
        { label: 'Historique des heures',          href: '/timesheets/history' },
        { label: 'Écarts planning/réel',           href: '/timesheets/gaps' },
      ],
      reports: [
        { label: 'Rapport équipe',       href: '/reports/team' },
        { label: 'Rapport productivité', href: '/reports/productivity' },
        { label: 'Rapport heures',       href: '/reports/timesheets' },
      ],
      account: [
        { label: 'Mon profil',            href: '/profile' },
        { label: 'Modifier mot de passe', href: '/profile/password' },
      ],
    },
  },

  // ─── SUPERVISEUR ─────────────────────────────────────────
  sup: {
    main: [
      { id: 'dashboard',  label: 'Dashboard',          icon: LayoutDashboard },
      { id: 'team',       label: 'Mon équipe',         icon: Users },
      { id: 'planning',   label: 'Planning',           icon: Calendar },
      { id: 'timesheets', label: 'Gestion des heures', icon: Clock },
      { id: 'reports',    label: 'Rapports',           icon: BarChart3 },
      { id: 'account',    label: 'Mon compte',         icon: User },
    ],
    sub: {
      dashboard: [
        { label: 'Tableau de bord', href: '/dashboard/sup' },
        { label: 'Notifications',   href: '/notifications' },
      ],
      team: [
        { label: 'Liste des téléconseillers',  href: '/teleconseillers' },
        { label: 'Détails des téléconseillers',href: '/teleconseillers/details' },
      ],
      planning: [
        { label: 'Mon planning',          href: '/planning/mine' },
        { label: 'Planning de l\'équipe', href: '/planning/team' },
      ],
      timesheets: [
        { label: 'Saisie des heures TC',  href: '/timesheets/entry' },
        { label: 'Historique des heures', href: '/timesheets/history' },
        { label: 'Écarts planning/réel',  href: '/timesheets/gaps' },
      ],
      reports: [
        { label: 'Rapport équipe', href: '/reports/team' },
        { label: 'Rapport heures', href: '/reports/timesheets' },
      ],
      account: [
        { label: 'Mon profil',            href: '/profile' },
        { label: 'Modifier mot de passe', href: '/profile/password' },
      ],
    },
  },

  // ─── TÉLÉCONSEILLER ──────────────────────────────────────
  tc: {
    main: [
      { id: 'dashboard',  label: 'Dashboard',     icon: LayoutDashboard },
      { id: 'planning',   label: 'Mon planning',  icon: Calendar },
      { id: 'timesheets', label: 'Mes heures',    icon: Clock },
      { id: 'account',    label: 'Mon profil',    icon: User },
      { id: 'notifications', label: 'Notifications', icon: Bell },
    ],
    sub: {
      dashboard: [
        { label: 'Tableau de bord', href: '/dashboard/tc' },
      ],
      planning: [
        { label: 'Planning actuel',          href: '/planning/mine' },
        { label: 'Historique des plannings', href: '/planning/history' },
      ],
      timesheets: [
        { label: 'Heures validées',       href: '/timesheets' },
        { label: 'Historique des heures', href: '/timesheets/history' },
        { label: 'Heures supplémentaires',href: '/timesheets/overtime' },
      ],
      account: [
        { label: 'Informations personnelles',    href: '/profile' },
        { label: 'Modifier certaines informations', href: '/profile/edit' },
        { label: 'Modifier mot de passe',        href: '/profile/password' },
      ],
      notifications: [
        { label: 'Mes notifications', href: '/notifications' },
      ],
    },
  },
};

const currentMenu = computed(() => {
  const role = page.props.auth?.role;
  return menuConfig[role] ?? menuConfig.tc;
});

const currentSubMenu = computed(() => {
  return currentMenu.value.sub[activeMainMenu.value] ?? [];
});

const hasSubMenu = computed(() => currentSubMenu.value.length > 0);

const sidebarWidth = computed(() => {
  return (isHoveringSidebar.value || !hasSubMenu.value) ? 'w-64' : 'w-20';
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
      <!-- Logo -->
      <div class="p-4 border-b border-blue-100/50 flex items-center bg-gradient-to-r from-blue-600 to-indigo-600">
        <Link :href="route('dashboard')" class="flex items-center gap-3">
          <ApplicationLogo class="h-8 w-auto fill-current text-white flex-shrink-0" />
          <span v-if="isHoveringSidebar || !hasSubMenu" class="text-xl font-bold text-white whitespace-nowrap">WipLite</span>
        </Link>
      </div>

      <!-- Navigation principale -->
      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        <button
          v-for="item in currentMenu.main"
          :key="item.id"
          @click="activeMainMenu = item.id"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
            activeMainMenu === item.id
              ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
              : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700'
          ]"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
          <span v-if="isHoveringSidebar || !hasSubMenu" class="font-medium text-sm whitespace-nowrap">{{ item.label }}</span>
        </button>
      </nav>

      <!-- Profil utilisateur -->
      <div class="p-3 border-t border-blue-100/50">
        <Dropdown align="left" width="48">
          <template #trigger>
            <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-blue-50 transition-all duration-200">
              <div class="w-9 h-9 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow">
                {{ page.props.auth?.user?.name?.charAt(0)?.toUpperCase() ?? 'U' }}
              </div>
              <div v-if="isHoveringSidebar || !hasSubMenu" class="text-left flex-1 min-w-0">
                <div class="font-medium text-sm text-slate-800 truncate">{{ page.props.auth?.user?.name }}</div>
                <div class="text-xs text-blue-600 font-semibold uppercase tracking-wide">{{ page.props.auth?.role }}</div>
              </div>
            </button>
          </template>
          <template #content>
            <DropdownLink :href="route('profile.edit')">Profil</DropdownLink>
            <DropdownLink :href="route('logout')" method="post" as="button">Déconnexion</DropdownLink>
          </template>
        </Dropdown>
      </div>
    </aside>

    <!-- Sidebar secondaire -->
    <aside
      class="bg-white/90 backdrop-blur-lg border-r border-blue-100/50 flex flex-col transition-all duration-300 overflow-hidden"
      :class="hasSubMenu ? 'w-56' : 'w-0 opacity-0'"
    >
      <!-- Titre de la section -->
      <div class="px-4 py-4 border-b border-blue-100/50 bg-gradient-to-r from-blue-50 to-indigo-50">
        <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider mb-0.5">Section</p>
        <h3 class="font-bold text-blue-900 text-sm leading-tight">
          {{ currentMenu.main.find(m => m.id === activeMainMenu)?.label }}
        </h3>
      </div>

      <!-- Liens de la section -->
      <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
        <Link
          v-for="item in currentSubMenu"
          :key="item.href"
          :href="item.href"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-600 hover:bg-blue-50 hover:text-blue-800 text-sm font-medium transition-all duration-150 group"
        >
          <span class="w-1.5 h-1.5 rounded-full bg-blue-200 group-hover:bg-blue-500 flex-shrink-0 transition-colors"></span>
          {{ item.label }}
        </Link>
      </nav>
    </aside>

    <!-- Contenu principal -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white/70 backdrop-blur-lg border-b border-blue-100/50 px-8 py-4 shadow-sm">
        <slot name="header" />
      </header>
      <div class="flex-1 overflow-auto p-8">
        <slot />
      </div>
    </main>

  </div>
</template>
