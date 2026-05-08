<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Users, Megaphone, GitBranch, Clock, AlertCircle, TrendingUp } from 'lucide-vue-next';

defineProps({ stats: Object, campaignStats: Array, planningGaps: Object });
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                Tableau de bord Administrateur
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                            <Users class="w-6 h-6" />
                        </div>
                        <TrendingUp class="w-4 h-4 text-green-500" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Employés actifs</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.activeEmployees }} / {{ stats.employees }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <Megaphone class="w-6 h-6" />
                        </div>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Campagnes actives</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.campaigns }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600">
                            <GitBranch class="w-6 h-6" />
                        </div>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Affectations</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.assignments }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                            <Clock class="w-6 h-6" />
                        </div>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures travaillées</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.workedHours }}h</h1>
                </div>
            </div>

            <!-- Detailed Stats Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                        <TrendingUp class="w-5 h-5 text-blue-600" />
                        Dernières campagnes
                    </h3>
                    <div class="space-y-4">
                        <div v-for="camp in campaignStats" :key="camp.id" class="flex items-center justify-between p-4 bg-white/80 rounded-2xl border border-slate-50">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold">
                                    {{ camp.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ camp.name }}</p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ camp.status }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-blue-600">{{ camp.assignments_count }} affectations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-3xl text-white shadow-xl shadow-blue-500/20">
                        <h4 class="text-lg font-black mb-2 flex items-center gap-2">
                            <AlertCircle class="w-5 h-5" />
                            Alertes plannings
                        </h4>
                        <div class="mt-6 space-y-4">
                            <div class="flex justify-between items-center pb-4 border-b border-white/10">
                                <span class="text-blue-100 text-sm font-medium">Prévues</span>
                                <span class="font-black">{{ planningGaps.planned }}h</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-white/10">
                                <span class="text-blue-100 text-sm font-medium">Réalisées</span>
                                <span class="font-black">{{ planningGaps.worked }}h</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-100 text-sm font-medium">Écart</span>
                                <span :class="['font-black', planningGaps.gap < 0 ? 'text-red-300' : 'text-green-300']">
                                    {{ planningGaps.gap > 0 ? '+' : '' }}{{ planningGaps.gap }}h
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
