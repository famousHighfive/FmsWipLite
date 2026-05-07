<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import {
    LayoutDashboard, Users, Megaphone, Calendar, Clock,
    BarChart3, Settings, UserPlus, LogOut
} from "lucide-vue-next";

const page = usePage();
const activeMainMenu = ref("dashboard");
const isHoveringSidebar = ref(false); // Gère l'état visuel

const menuConfig = {
    admin: {
        main: [
            { id: "dashboard", label: "Tableau de bord", icon: LayoutDashboard },
            { id: "users", label: "Utilisateurs", icon: Users },
            { id: "employees", label: "Employés", icon: UserPlus },
            { id: "campaigns", label: "Campagnes", icon: Megaphone },
            { id: "planning", label: "Planning", icon: Calendar },
            { id: "timesheets", label: "Feuilles de temps", icon: Clock },
            { id: "reports", label: "Rapports", icon: BarChart3 },
        ],
        sub: {
            dashboard: [{ label: "Vue générale", href: "/dashboard/admin" }],
            users: [
                { label: "Liste des utilisateurs", href: "/users" },
                { label: "Ajouter un utilisateur", href: "/users/create" },
            ],
            employees: [
                { label: "Liste des employés", href: "/employees" },
                { label: "Ajouter un employé", href: "/employees/create" },
            ],
            campaigns: [
                { label: "Liste des campagnes", href: "/campaigns" },
                { label: "Ajouter une campagne", href: "/campaigns/create" },
            ],
            planning: [
                { label: "Modèles de planning", href: "/planning/models" },
                { label: "Affectations", href: "/planning/assignments" },
                { label: "Historique", href: "/planning/history" },
            ],
            timesheets: [
                { label: "Validation", href: "/timesheets/validate" },
                { label: "Historique", href: "/timesheets/history" },
            ],
            reports: [
                { label: "Présence", href: "/reports/attendance" },
                { label: "Productivité", href: "/reports/productivity" },
            ],
        },
    },
    // ... tes autres rôles (cp, sup, tc) restent inchangés
};

const currentMenu = computed(() => {
    const role = page.props.auth.role;
    return menuConfig[role] || menuConfig.tc;
});

const currentSubMenu = computed(() => {
    return currentMenu.value.sub[activeMainMenu.value] || [];
});

// LOGIQUE DE PLIAGE :
// Si la souris n'est pas dessus, on force w-20 (plié).
// Si la souris est dessus, on affiche w-64 (déplié).
const sidebarWidth = computed(() => {
    return isHoveringSidebar.value ? "w-64" : "w-20";
});
</script>

<template>
    <div class="flex h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">

        <!-- Sidebar principale -->
        <aside
            :class="[
                'bg-white/70 backdrop-blur-xl border-r border-blue-100/50 text-slate-700 flex flex-col transition-all duration-300 shadow-lg z-50',
                sidebarWidth,
            ]"
            @mouseenter="isHoveringSidebar = true"
            @mouseleave="isHoveringSidebar = false"
        >
            <div class="p-4 border-b border-blue-100/50 flex items-center bg-gradient-to-r from-blue-600 to-indigo-600 h-20 shrink-0">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <ApplicationLogo class="h-8 w-auto fill-current text-white shrink-0" />
                    <span
                        v-show="isHoveringSidebar"
                        class="text-xl font-bold text-white whitespace-nowrap transition-opacity duration-300"
                    >WipLite</span>
                </Link>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <button
                    v-for="item in currentMenu.main"
                    :key="item.id"
                    @click="activeMainMenu = item.id"
                    :class="[
                        'w-full flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative',
                        activeMainMenu === item.id
                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/30'
                            : 'text-slate-600 hover:bg-white/80 hover:shadow-md hover:border hover:border-blue-100',
                    ]"
                >
                    <component :is="item.icon" class="w-6 h-6 flex-shrink-0" />
                    <span
                        v-show="isHoveringSidebar"
                        class="font-medium whitespace-nowrap transition-opacity duration-300"
                    >{{ item.label }}</span>

                    <!-- Tooltip si plié -->
                    <div v-if="!isHoveringSidebar" class="absolute left-full ml-4 px-3 py-2 bg-slate-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-[60]">
                        {{ item.label }}
                    </div>
                </button>
            </nav>

            <!-- Profil / Logout -->
            <div class="p-4 border-t border-blue-100/50 bg-blue-50/50">
                <Dropdown align="left" width="48">
                    <template #trigger>
                        <button class="w-full flex items-center gap-3 px-2 py-3 rounded-2xl text-slate-600 hover:bg-white/80 hover:shadow-md transition-all duration-300">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center flex-shrink-0 text-white font-bold shadow-md">
                                {{ page.props.auth.user?.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div v-show="isHoveringSidebar" class="text-left flex-1 min-w-0">
                                <div class="font-medium text-sm text-slate-800 truncate">{{ page.props.auth.user?.name }}</div>
                                <div class="text-xs text-blue-600 font-semibold uppercase">{{ page.props.auth.role }}</div>
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

        <!-- Sidebar secondaire (Sous-menu) -->
        <aside
            class="bg-white/80 backdrop-blur-lg border-r border-blue-100/50 flex flex-col transition-all duration-300 shadow-sm z-40"
            :class="[currentSubMenu.length > 0 ? 'w-64' : 'w-0 opacity-0 overflow-hidden']"
        >
            <div class="p-4 border-b border-blue-100/50 bg-gradient-to-r from-blue-50 to-indigo-50 h-20 flex items-center">
                <h3 class="font-bold text-blue-800 uppercase text-xs tracking-wider">
                    {{ currentMenu.main.find((m) => m.id === activeMainMenu)?.label }}
                </h3>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <Link
                    v-for="item in currentSubMenu"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'block px-4 py-3 rounded-2xl text-slate-700 font-medium transition-all duration-200',
                        page.url.startsWith(item.href)
                            ? 'bg-blue-600 text-white shadow-md'
                            : 'hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-800'
                    ]"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="flex-1 flex flex-col overflow-hidden bg-transparent">
            <header class="bg-white/60 backdrop-blur-lg border-b border-blue-100/50 px-8 h-20 flex items-center shadow-sm shrink-0">
                <slot name="header" />
            </header>
            <div class="flex-1 overflow-auto p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
