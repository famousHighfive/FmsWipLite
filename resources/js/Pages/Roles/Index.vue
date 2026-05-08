<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    ShieldCheck,
    Users,
    Pencil,
    Trash2,
    Plus
} from 'lucide-vue-next';

defineProps({
    roles: Array
});
</script>

<template>
    <Head title="Rôles & Permissions" />

    <AppLayout>
        <template #header>
            <div class="flex flex-col">
                <h2 class="text-3xl font-black tracking-tight text-slate-800">
                    Rôles & Permissions
                </h2>

                <p class="text-sm text-slate-500 mt-1 font-medium">
                    Gérez les accès et les autorisations du système
                </p>
            </div>
        </template>

        <div class="space-y-8">

            <!-- HEADER CARD -->
            <div
                class="relative overflow-hidden rounded-[32px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 p-8 shadow-xl"
            >
                <div
                    class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl"
                ></div>

                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-16 h-16 rounded-3xl bg-white/15 backdrop-blur flex items-center justify-center"
                        >
                            <ShieldCheck class="w-8 h-8 text-white" />
                        </div>

                        <div>
                            <h1 class="text-3xl font-black text-white tracking-tight">
                                Gestion des rôles
                            </h1>

                            <p class="text-blue-100 mt-1 font-medium">
                                Administration complète des permissions utilisateurs
                            </p>
                        </div>
                    </div>

                    <button
                        class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-white text-blue-700 font-black shadow-lg hover:scale-105 transition-all duration-200"
                    >
                        <Plus class="w-5 h-5" />
                        Nouveau rôle
                    </button>
                </div>
            </div>

            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div
                    class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase">
                                Total rôles
                            </p>

                            <h3 class="text-4xl font-black text-slate-800 mt-2">
                                {{ roles.length }}
                            </h3>
                        </div>

                        <div
                            class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center"
                        >
                            <ShieldCheck class="w-7 h-7 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase">
                                Utilisateurs actifs
                            </p>

                            <h3 class="text-4xl font-black text-slate-800 mt-2">
                                {{
                                    roles.reduce((sum, role) => sum + role.users_count, 0)
                                }}
                            </h3>
                        </div>

                        <div
                            class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center"
                        >
                            <Users class="w-7 h-7 text-indigo-600" />
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-400 uppercase">
                                Sécurité
                            </p>

                            <h3 class="text-2xl font-black text-emerald-600 mt-3">
                                Stable
                            </h3>
                        </div>

                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center"
                        >
                            <ShieldCheck class="w-7 h-7 text-emerald-600" />
                        </div>
                    </div>
                </div>

            </div>

            <!-- TABLE -->
            <div
                class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden"
            >

                <!-- TOP -->
                <div
                    class="px-8 py-6 border-b border-slate-100 flex items-center justify-between"
                >
                    <div>
                        <h3 class="text-xl font-black text-slate-800">
                            Liste des rôles
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Tous les rôles disponibles dans le système
                        </p>
                    </div>

                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Rechercher un rôle..."
                            class="w-72 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50">
                            <tr>

                                <th
                                    class="px-8 py-5 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Rôle
                                </th>

                                <th
                                    class="px-8 py-5 text-left text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Utilisateurs
                                </th>

                                <th
                                    class="px-8 py-5 text-center text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Statut
                                </th>

                                <!-- <th
                                    class="px-8 py-5 text-right text-xs font-black uppercase tracking-wider text-slate-500"
                                >
                                    Actions
                                </th> -->

                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="role in roles"
                                :key="role.id"
                                class="border-b border-slate-100 hover:bg-blue-50/40 transition-all duration-200"
                            >

                                <!-- ROLE -->
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">

                                        <div
                                            class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-md"
                                        >
                                            <ShieldCheck class="w-6 h-6 text-white" />
                                        </div>

                                        <div>
                                            <h4 class="font-black text-slate-800 capitalize">
                                                {{ role.name }}
                                            </h4>

                                            <p class="text-sm text-slate-500 mt-1">
                                                Rôle système
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                <!-- USERS -->
                                <td class="px-8 py-6">
                                    <div
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-100"
                                    >
                                        <Users class="w-4 h-4 text-slate-500" />

                                        <span class="font-black text-slate-700">
                                            {{ role.users_count }}
                                        </span>
                                    </div>
                                </td>

                                <!-- STATUS -->
                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-sm font-black"
                                    >
                                        Actif
                                    </span>
                                </td>

                                <!-- ACTIONS -->
                                <!-- <td class="px-8 py-6">
                                    <div class="flex items-center justify-end gap-3">

                                        <button
                                            class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center hover:scale-105 transition"
                                        >
                                            <Pencil class="w-5 h-5" />
                                        </button>

                                        <button
                                            class="w-11 h-11 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center hover:scale-105 transition"
                                        >
                                            <Trash2 class="w-5 h-5" />
                                        </button>

                                    </div>
                                </td> -->

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </AppLayout>
</template>
