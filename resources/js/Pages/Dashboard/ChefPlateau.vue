<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Users, UserPlus, Clock, TrendingUp, AlertCircle } from 'lucide-vue-next';

defineProps({ stats: Object });
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                Tableau de bord Chef de Plateau
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Users class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Superviseurs</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.supervisors }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-4">
                        <UserPlus class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Téléconseillers</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.teleconseillers }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 mb-4">
                        <Clock class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Réalisées</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.workedHours }}h</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                        <AlertCircle class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Feuilles à valider</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.pendingTimesheets }}</h1>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[2rem] text-white shadow-xl shadow-blue-500/20">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h3 class="text-xl font-black mb-2 tracking-tight">Analyse de la production</h3>
                        <p class="text-blue-100 text-sm font-medium">Consultez les rapports détaillés pour optimiser vos campagnes.</p>
                    </div>
                    <div class="flex gap-4">
                        <button class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl font-bold transition-all">Voir les rapports</button>
                        <button class="px-6 py-3 bg-white text-blue-600 rounded-xl font-bold shadow-lg transition-all">Export Hebdomadaire</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
