<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Users, Clock, Calendar, AlertCircle } from 'lucide-vue-next';

defineProps({ stats: Object });
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                Tableau de bord Superviseur
            </h2>
        </template>

        <div class="space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Users class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Équipe Directe</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.teamSize }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 mb-4">
                        <Clock class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Équipe</p>
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
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-4">
                        <AlertCircle class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Retards/Écarts</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.gapsCount }}</h1>
                </div>
            </div>

            <!-- Team Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                    <h3 class="text-lg font-black text-slate-800 mb-4 tracking-tight">Gestion de l'équipe</h3>
                    <p class="text-slate-500 text-sm mb-6">Accédez rapidement aux outils de management pour votre équipe.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <button class="p-4 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-2xl text-left transition-all group">
                            <Clock class="w-5 h-5 text-blue-600 mb-2" />
                            <p class="font-bold text-slate-700 group-hover:text-blue-600">Saisie Heures</p>
                        </button>
                        <button class="p-4 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-2xl text-left transition-all group">
                            <Calendar class="w-5 h-5 text-blue-600 mb-2" />
                            <p class="font-bold text-slate-700 group-hover:text-blue-600">Plannings</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
