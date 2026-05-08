<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Clock, Calendar, AlertCircle, TrendingUp } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

defineProps({
    stats: Object,
    weeklyStats: Array
});
</script>

<template>
    <Head title="Rapport Heures" />

    <AppLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rapport Heures Travaillées</h2>
                <p class="text-slate-500 font-medium">Analyse des temps de production et des écarts planning.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Clock class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Réalisé</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalHours }}h</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-4">
                        <Calendar class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Prévu</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalPlanned }}h</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                        <TrendingUp class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Sup.</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalOvertime }}h</h1>
                </div>
            </div>

            <!-- Weekly Evolution -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <TrendingUp class="w-5 h-5 text-blue-600" />
                    Évolution hebdomadaire
                </h3>
                
                <DataTable :value="weeklyStats" class="p-datatable-sm">
                    <Column field="week" header="Semaine" />
                    <Column field="hours" header="Heures réalisées">
                        <template #body="{ data }">
                            <span class="font-bold text-slate-700">{{ data.hours }}h</span>
                        </template>
                    </Column>
                    <Column field="planned" header="Heures prévues">
                        <template #body="{ data }">
                            <span class="text-slate-500">{{ data.planned }}h</span>
                        </template>
                    </Column>
                    <Column header="Écart">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <span :class="['font-black', (data.hours - data.planned) < 0 ? 'text-red-500' : 'text-green-500']">
                                    {{ (data.hours - data.planned) > 0 ? '+' : '' }}{{ (data.hours - data.planned).toFixed(1) }}h
                                </span>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
