<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Users, UserCheck, UserX, UserMinus, BarChart3 } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

defineProps({
    stats: Object,
    positionDistribution: Array
});
</script>

<template>
    <Head title="Rapport RH" />

    <AppLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rapport Ressources Humaines</h2>
                <p class="text-slate-500 font-medium">Vue d'ensemble de vos effectifs et de leur répartition.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Users class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Employés</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.total }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 mb-4">
                        <UserCheck class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Actifs</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.active }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                        <UserMinus class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Suspendus</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.suspended }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center text-red-600 mb-4">
                        <UserX class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Inactifs</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.inactive }}</h1>
                </div>
            </div>

            <!-- Distribution Section -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <BarChart3 class="w-5 h-5 text-blue-600" />
                    Répartition par poste
                </h3>
                
                <DataTable :value="positionDistribution" class="p-datatable-sm">
                    <Column field="name" header="Poste" />
                    <Column field="count" header="Nombre d'employés">
                        <template #body="{ data }">
                            <span class="font-bold">{{ data.count }}</span>
                        </template>
                    </Column>
                    <Column header="Pourcentage">
                        <template #body="{ data }">
                            <div class="flex items-center gap-4">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600" :style="{ width: (data.count / stats.total * 100) + '%' }"></div>
                                </div>
                                <span class="text-xs font-black text-slate-400 w-10">
                                    {{ Math.round(data.count / stats.total * 100) }}%
                                </span>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
