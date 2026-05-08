<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Clock, Calendar, CheckCircle, TrendingUp } from 'lucide-vue-next';

defineProps({ stats: Object });
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                Mon Espace Personnel
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Clock class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Semaine</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.workedHours }}h</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-4">
                        <Calendar class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Prévu Semaine</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.plannedHours }}h</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 mb-4">
                        <CheckCircle class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Assiduité</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.attendance }}%</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                        <TrendingUp class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Performance</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">N/A</h1>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2 tracking-tight">
                    <Calendar class="w-5 h-5 text-blue-600" />
                    Mon Planning de la semaine
                </h3>
                <div class="grid grid-cols-7 gap-4">
                    <div v-for="day in ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']" :key="day" class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100 text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">{{ day }}</p>
                        <p class="font-bold text-slate-700">8h</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
