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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

defineProps({
    roles: Array
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
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
                <DataTable 
                    :value="roles" 
                    v-model:filters="filters"
                    :globalFilterFields="['name']"
                    class="p-datatable-sm"
                    stripedRows
                >
                    <template #header>
                        <div class="px-8 py-6 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-slate-800">Liste des rôles</h3>
                                <p class="text-sm text-slate-500 mt-1">Tous les rôles disponibles dans le système</p>
                            </div>
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Filtrage automatique..." class="!rounded-2xl !bg-slate-50/50" />
                            </IconField>
                        </div>
                    </template>

                    <Column field="name" header="Rôle" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                                    <ShieldCheck class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-800 capitalize">{{ data.name }}</h4>
                                    <p class="text-sm text-slate-500 mt-1">Rôle système</p>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <Column field="users_count" header="Utilisateurs" sortable>
                        <template #body="{ data }">
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-100">
                                <Users class="w-4 h-4 text-slate-500" />
                                <span class="font-black text-slate-700">{{ data.users_count }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Statut" class="text-center">
                        <template #body>
                            <span class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-sm font-black">
                                Actif
                            </span>
                        </template>
                    </Column>
                </DataTable>
            </div>

        </div>
    </AppLayout>
</template>
