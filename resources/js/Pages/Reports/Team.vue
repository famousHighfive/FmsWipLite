<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Users, Clock, User, Briefcase } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

defineProps({
    team: Array,
    stats: Object
});
</script>

<template>
    <Head title="Mon Équipe" />

    <AppLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rapport d'Équipe</h2>
                <p class="text-slate-500 font-medium">Vue d'ensemble de vos collaborateurs directs et de leur activité.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Users class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Membres d'Équipe</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalMembers }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 mb-4">
                        <Clock class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Heures Équipe</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalHours }}h</h1>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <User class="w-5 h-5 text-blue-600" />
                    Liste des collaborateurs
                </h3>
                
                <DataTable :value="team" class="p-datatable-sm">
                    <Column header="Nom">
                        <template #body="{ data }">
                            <span class="font-bold text-slate-700">{{ data.user?.name }}</span>
                        </template>
                    </Column>
                    <Column field="matricule" header="Matricule" />
                    <Column header="Poste">
                        <template #body="{ data }">
                            <Tag :value="data.position?.name" severity="info" class="!text-[10px]" />
                        </template>
                    </Column>
                    <Column field="status" header="Statut">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="data.status === 'actif' ? 'success' : 'secondary'" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
