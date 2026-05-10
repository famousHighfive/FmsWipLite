<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Chart from 'primevue/chart';
import { 
    Users, 
    Briefcase, 
    ClipboardCheck, 
    Clock, 
    TrendingUp, 
    AlertCircle,
    FileDown,
    Download,
    ChevronRight,
    UserCheck
} from 'lucide-vue-next';

const props = defineProps({ 
    stats: { type: Object, default: () => ({}) }, 
    campaignStats: { type: Array, default: () => [] }, 
    employeeStats: { type: Array, default: () => [] },
    planningGaps: { type: Object, default: () => ({}) },
    monthlyEvolution: { type: Array, default: () => [] }
});

// Chart Data & Options
const evolutionChartData = ref(null);
const evolutionChartOptions = ref(null);

const campaignChartData = ref(null);
const campaignChartOptions = ref(null);

onMounted(() => {
    initCharts();
});

const initCharts = () => {
    if (props.monthlyEvolution?.length) {
        evolutionChartData.value = {
            labels: props.monthlyEvolution.map(item => item.month),
            datasets: [
                {
                    label: 'Heures Prévues',
                    data: props.monthlyEvolution.map(item => item.total_planned),
                    fill: false,
                    borderColor: '#94a3b8',
                    tension: 0.4
                },
                {
                    label: 'Heures Travaillées',
                    data: props.monthlyEvolution.map(item => item.total_worked),
                    fill: true,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4
                }
            ]
        };
    }

    evolutionChartOptions.value = {
        maintainAspectRatio: false,
        aspectRatio: 0.6,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#64748b' }
            }
        },
        scales: {
            x: {
                ticks: { color: '#64748b' },
                grid: { color: '#f1f5f9' }
            },
            y: {
                ticks: { color: '#64748b' },
                grid: { color: '#f1f5f9' }
            }
        }
    };

    if (props.campaignStats?.length) {
        campaignChartData.value = {
            labels: props.campaignStats.map(c => c.name),
            datasets: [
                {
                    label: 'Agents Actifs',
                    data: props.campaignStats.map(c => c.assignments_count),
                    backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b'],
                    borderRadius: 8
                }
            ]
        };
    }

    campaignChartOptions.value = {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    };
};

const exportExcel = () => {
    window.location.href = route('reports.export.excel');
};

const exportPdf = () => {
    window.location.href = route('reports.export.pdf');
};
</script>

<template>
    <Head title="Tableau de bord - Administrateur" />
    
    <AppLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Tableau de Bord Décisionnel</h2>
                    <p class="text-slate-500 font-medium mt-1">Reporting et analyse des performances</p>
                </div>
                <div class="flex gap-3">
                    <button 
                        @click="exportExcel"
                        class="flex items-center gap-2 bg-white border border-slate-200 px-5 py-2.5 rounded-2xl text-sm font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-95"
                    >
                        <Download class="w-4 h-4 text-emerald-600" />
                        Exporter Excel
                    </button>
                    <button 
                        @click="exportPdf"
                        class="flex items-center gap-2 bg-slate-900 px-5 py-2.5 rounded-2xl text-sm font-bold text-white hover:bg-slate-800 transition-all shadow-md active:scale-95"
                    >
                        <FileDown class="w-4 h-4 text-indigo-400" />
                        Rapport PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <Users class="w-6 h-6" />
                        </div>
                        <div class="flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                            <TrendingUp class="w-3 h-3" />
                            +{{ Math.round((stats.activeEmployees / (stats.employees || 1)) * 100) }}%
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Effectif Actif</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-3xl font-black text-slate-800">{{ stats.activeEmployees || 0 }}</h3>
                        <span class="text-slate-400 font-medium">/ {{ stats.employees || 0 }}</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                            <Briefcase class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">Actives</span>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Campagnes</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.campaigns || 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                            <ClipboardCheck class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">{{ stats.pendingTimesheets || 0 }} en attente</span>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Affectations</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.assignments || 0 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                            <Clock class="w-6 h-6" />
                        </div>
                        <div v-if="stats.overtimeHours > 0" class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full">
                            {{ stats.overtimeHours }}h sup.
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Heures Total</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ Math.round(stats.workedHours || 0) }}<span class="text-lg ml-1 text-slate-400">h</span></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Planning vs Realized Chart -->
                <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800">Évolution de la Productivité</h3>
                            <p class="text-sm text-slate-500 font-medium">Heures prévues vs Heures réalisées</p>
                        </div>
                        <div class="flex gap-4 text-xs font-bold">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                                <span class="text-slate-500">Prévu</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                                <span class="text-slate-800">Réalisé</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="h-80">
                        <Chart type="line" :data="evolutionChartData" :options="evolutionChartOptions" class="h-full" />
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-5 bg-slate-50 rounded-[1.5rem]">
                            <p class="text-xs text-slate-400 uppercase font-black mb-1">Taux de réalisation</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-2xl font-black text-slate-800">
                                    {{ Math.round(((stats.workedHours || 0) / (planningGaps.planned || 1)) * 100) }}%
                                </p>
                                <span class="text-xs font-bold text-slate-400">Objectif: 95%</span>
                            </div>
                        </div>
                        <div class="p-5 bg-slate-50 rounded-[1.5rem]">
                            <p class="text-xs text-slate-400 uppercase font-black mb-1">Écart Volume</p>
                            <p class="text-2xl font-black" :class="(planningGaps.gap || 0) >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                                {{ (planningGaps.gap || 0) > 0 ? '+' : '' }}{{ Math.round(planningGaps.gap || 0) }}h
                            </p>
                        </div>
                        <div class="p-5 bg-indigo-600 rounded-[1.5rem] text-white shadow-lg shadow-indigo-200">
                            <p class="text-xs text-indigo-200 uppercase font-black mb-1">Status Global</p>
                            <p class="text-lg font-bold">
                                {{ (planningGaps.gap || 0) >= 0 ? 'Optimisé' : 'Sous-effectif' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Campaign Stats -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 flex flex-col">
                    <h3 class="text-xl font-black text-slate-800 mb-2">Performance Campagnes</h3>
                    <p class="text-sm text-slate-500 font-medium mb-8">Répartition des agents actifs</p>
                    
                    <div class="h-64 mb-8">
                        <Chart type="bar" :data="campaignChartData" :options="campaignChartOptions" class="h-full" />
                    </div>

                    <div class="space-y-4 flex-1 overflow-y-auto max-h-60 custom-scrollbar pr-2">
                        <div v-for="campaign in campaignStats" :key="campaign.id" 
                            class="group flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black text-sm group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    {{ campaign.name.substring(0, 2).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ campaign.name }}</p>
                                    <p class="text-xs text-slate-400 font-medium">{{ campaign.assignments_count }} agents</p>
                                </div>
                            </div>
                            <ChevronRight class="w-4 h-4 text-slate-300 group-hover:text-indigo-600 transition-colors" />
                        </div>

                        <div v-if="campaignStats.length === 0" class="text-center py-12">
                            <AlertCircle class="w-12 h-12 text-slate-100 mx-auto mb-4" />
                            <p class="text-slate-400 text-sm font-medium">Aucune campagne active</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Employee Performance & Recent Alerts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Employees -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-slate-800">Top Performance Agents</h3>
                        <button class="text-indigo-600 text-xs font-bold hover:underline">Voir tout</button>
                    </div>
                    
                    <div class="space-y-6">
                        <div v-for="emp in employeeStats" :key="emp.id" class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center font-black text-slate-600 border-2 border-white shadow-sm">
                                        {{ emp.first_name.charAt(0) }}{{ emp.last_name.charAt(0) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full flex items-center justify-center">
                                        <UserCheck class="w-3 h-3 text-white" />
                                    </div>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ emp.first_name }} {{ emp.last_name }}</p>
                                    <p class="text-xs text-slate-400 font-medium">{{ emp.position?.name || 'Agent' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-800">{{ Math.round(emp.timesheet_entries_sum_total_hours || 0) }}h</p>
                                <div class="flex items-center gap-1">
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full bg-emerald-500 rounded-full" 
                                            :style="{ width: Math.min(((emp.timesheet_entries_sum_total_hours || 0) / (emp.timesheet_entries_sum_planned_hours || 1)) * 100, 100) + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">
                                        {{ Math.round(((emp.timesheet_entries_sum_total_hours || 0) / (emp.timesheet_entries_sum_planned_hours || 1)) * 100) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary & Quick Actions -->
                <div class="bg-indigo-900 p-8 rounded-[2.5rem] shadow-xl text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black mb-2">Résumé Décisionnel</h3>
                        <p class="text-indigo-200 text-sm font-medium mb-8">Analyse automatique du plateau</p>
                        
                        <div class="space-y-6">
                            <div class="flex gap-4 p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                                <div class="p-3 bg-amber-400 text-amber-900 rounded-xl h-fit">
                                    <AlertCircle class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="font-bold text-white">Alertes Planning</p>
                                    <p class="text-xs text-indigo-200 mt-1">
                                        {{ stats.pendingTimesheets || 0 }} feuilles de temps en attente de validation. Impact possible sur la facturation.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                                <div class="p-3 bg-emerald-400 text-emerald-900 rounded-xl h-fit">
                                    <TrendingUp class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="font-bold text-white">Tendance Hebdomadaire</p>
                                    <p class="text-xs text-indigo-200 mt-1">
                                        La productivité est en hausse par rapport à la période précédente.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-4">
                            <button class="flex-1 bg-white text-indigo-900 py-3 rounded-2xl font-black text-sm hover:bg-indigo-50 transition-colors shadow-lg">
                                Générer Rapport Complet
                            </button>
                        </div>
                    </div>

                    <!-- Decorative elements -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-rose-500 rounded-full blur-3xl opacity-10"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<<<<<<< HEAD
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
=======
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Employés</p>
<h1 class="text-4xl font-black">{{ stats.employees }}</h1>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Campagnes</p>
<h1 class="text-4xl font-black">{{ stats.campaigns }}</h1>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Affectations</p>
<h1 class="text-4xl font-black">{{ stats.assignments }}</h1>
</div>

<div class="bg-white p-6 rounded-3xl shadow-sm">
<p>Heures</p>
<h1 class="text-4xl font-black">{{ stats.workedHours }}</h1>
</div>

</div>

</AppLayout>
</template>
>>>>>>> c36fb09ec3e2707d3dc351ba2461e88d4d276556
