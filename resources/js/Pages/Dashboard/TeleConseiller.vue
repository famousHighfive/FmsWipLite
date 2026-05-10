<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Clock, 
    Calendar, 
    CheckCircle, 
    AlertCircle,
    User,
    ArrowUpRight,
    TrendingUp
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    },
    recentTimesheets: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Mon Dashboard - Téléconseiller" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-slate-800">Bonjour, {{ $page.props.auth.user.name }}</h2>
                    <p class="text-slate-500 font-medium">Bienvenue sur votre espace de travail</p>
                </div>
                <div class="flex items-center gap-3 p-3 bg-white rounded-2xl shadow-sm border border-slate-100">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                        <User class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Poste Actuel</p>
                        <p class="text-xs font-bold text-slate-700">{{ stats.activeAssignment?.campaign?.name || 'Aucune affectation' }}</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <Clock class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures du Mois</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ Math.round(stats.hoursWorkedMonth) }}h</h3>
                    <div class="mt-4 flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50 w-fit px-2 py-1 rounded-lg">
                        <TrendingUp class="w-3 h-3" />
                        En progression
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                            <AlertCircle class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">En attente de validation</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ stats.pendingValidation }} feuilles</h3>
                    <p class="text-sm text-slate-400 mt-2 font-medium">Soumises au superviseur</p>
                </div>

                <div class="bg-emerald-600 p-6 rounded-[2rem] shadow-lg text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl">
                            <Calendar class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-emerald-100 uppercase tracking-widest">Mon Planning</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold">Consulter mon planning</h3>
                        <Link href="#" class="p-2 bg-white/20 rounded-xl hover:bg-white/30 transition-colors">
                            <ArrowUpRight class="w-5 h-5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-slate-800">Mes dernières feuilles de temps</h3>
                        <Link href="#" class="text-indigo-600 text-xs font-bold hover:underline">Voir tout</Link>
                    </div>
                    
                    <div class="overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left">
                                    <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Période</th>
                                    <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Statut</th>
                                    <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="ts in recentTimesheets" :key="ts.id" class="group">
                                    <td class="py-4">
                                        <p class="text-sm font-bold text-slate-700">Du {{ new Date(ts.period_start).toLocaleDateString() }} au {{ new Date(ts.period_end).toLocaleDateString() }}</p>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span :class="{
                                            'bg-amber-50 text-amber-600': ts.status === 'soumis',
                                            'bg-emerald-50 text-emerald-600': ts.status === 'validé',
                                            'bg-slate-50 text-slate-400': ts.status === 'brouillon'
                                        }" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter">
                                            {{ ts.status }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <Link href="#" class="text-indigo-600 text-xs font-bold group-hover:underline">Détails</Link>
                                    </td>
                                </tr>
                                <tr v-if="recentTimesheets.length === 0">
                                    <td colspan="3" class="py-10 text-center text-slate-400 font-medium italic">
                                        Aucune feuille de temps enregistrée pour le moment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6">Actions Rapides</h3>
                    <div class="space-y-4">
                        <button class="w-full flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-100 hover:border-indigo-600 transition-all group">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <Calendar class="w-5 h-5" />
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-slate-800">Saisir mes heures</p>
                                <p class="text-[10px] text-slate-400 font-medium">Pour la période en cours</p>
                            </div>
                        </button>

                        <button class="w-full flex items-center gap-4 p-4 bg-white rounded-2xl shadow-sm border border-slate-100 hover:border-emerald-600 transition-all group">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <CheckCircle class="w-5 h-5" />
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-slate-800">Demander validation</p>
                                <p class="text-[10px] text-slate-400 font-medium">Envoyer au superviseur</p>
                            </div>
                        </button>
                    </div>

                    <div class="mt-8 p-6 bg-indigo-900 rounded-3xl text-white relative overflow-hidden">
                        <p class="text-xs font-black text-indigo-300 uppercase tracking-widest mb-2">Support</p>
                        <p class="text-sm font-bold leading-tight relative z-10">Besoin d'aide pour vos plannings ?</p>
                        <button class="mt-4 text-xs font-black bg-white text-indigo-900 px-4 py-2 rounded-xl relative z-10">Contacter RH</button>
                        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
