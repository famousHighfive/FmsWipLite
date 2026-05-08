<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Briefcase, 
    Users, 
    ClipboardList, 
    Clock, 
    TrendingUp, 
    AlertTriangle,
    CheckCircle,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    },
    campaigns: {
        type: Array,
        default: () => []
    },
    recentAlerts: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Dashboard - Chef de Plateau" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-slate-800">Dashboard Chef de Plateau</h2>
                    <p class="text-slate-500 font-medium">Pilotage opérationnel et supervision des campagnes</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('planning.assignments.index')" class="flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-md">
                        <ClipboardList class="w-4 h-4" />
                        Gérer les Plannings
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <!-- KPI Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                            <Briefcase class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">Total</span>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Campagnes</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalCampaigns }}</h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                            <Users class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">En ligne</span>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Agents Actifs</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.activeAgents }}</h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                            <AlertTriangle class="w-6 h-6" />
                        </div>
                        <span v-if="stats.pendingPlannings > 0" class="animate-pulse text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Action requise</span>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Plannings à Valider</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.pendingPlannings }}</h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <Clock class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">Cette semaine</span>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Produites</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ Math.round(stats.workedHoursWeek) }}h</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Campaigns Performance -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-slate-800">Suivi des Campagnes</h3>
                        <Link :href="route('planning.models.index')" class="text-indigo-600 text-xs font-bold hover:underline">Voir les modèles</Link>
                    </div>
                    <div class="space-y-6">
                        <div v-for="campaign in campaigns" :key="campaign.id" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-slate-200 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm font-black text-slate-400 group-hover:text-indigo-600 transition-colors">
                                    {{ campaign.name.substring(0,2).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ campaign.name }}</p>
                                    <p class="text-xs text-slate-400 font-medium">{{ campaign.assignments_count }} agents affectés</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-black text-slate-700">{{ campaign.status }}</p>
                                    <div class="w-20 h-1.5 bg-slate-200 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full" :style="{ width: '75%' }"></div>
                                    </div>
                                </div>
                                <ArrowRight class="w-5 h-5 text-slate-300 group-hover:text-indigo-600 transition-colors" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity/Alerts -->
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">
                    <h3 class="text-xl font-black mb-8">Journal d'Activité Récent</h3>
                    <div class="space-y-6">
                        <div v-for="log in recentAlerts" :key="log.id" class="flex gap-4">
                            <div class="mt-1">
                                <div v-if="log.action === 'created'" class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                                <div v-else-if="log.action === 'updated'" class="w-2 h-2 rounded-full bg-amber-50 shadow-[0_0_8px_rgba(245,158,11,0.6)]"></div>
                                <div v-else class="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-100">{{ log.description }}</p>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest">{{ log.model_type ? log.model_type.split('\\').pop() : 'Système' }}</p>
                                    <p class="text-[10px] text-slate-500 font-medium">{{ new Date(log.created_at).toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="recentAlerts.length === 0" class="text-center py-12">
                            <p class="text-slate-500 text-sm font-medium italic">Aucune activité récente</p>
                        </div>
                    </div>
                    
                    <div class="mt-10 p-6 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-sm">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-emerald-500/20 text-emerald-400 rounded-2xl">
                                <CheckCircle class="w-6 h-6" />
                            </div>
                            <div>
                                <p class="font-bold">Système Stable</p>
                                <p class="text-xs text-slate-400 mt-1">Tous les services opérationnels. Aucun incident majeur détecté.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
