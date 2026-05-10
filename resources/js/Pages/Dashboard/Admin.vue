<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
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
    stats: {
        type: Object,
        default: () => ({})
    },
    campaignStats: {
        type: Array,
        default: () => []
    },
    employeeStats: {
        type: Array,
        default: () => []
    },
    planningGaps: {
        type: Object,
        default: () => ({})
    },
    monthlyEvolution: {
        type: Array,
        default: () => []
    }
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
                labels: {
                    color: '#64748b'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#64748b'
                },
                grid: {
                    color: '#f1f5f9'
                }
            },
            y: {
                ticks: {
                    color: '#64748b'
                },
                grid: {
                    color: '#f1f5f9'
                }
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
                    backgroundColor: [
                        '#6366f1',
                        '#8b5cf6',
                        '#ec4899',
                        '#f43f5e',
                        '#f59e0b'
                    ],
                    borderRadius: 8
                }
            ]
        };
    }

    campaignChartOptions.value = {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f1f5f9'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
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
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">
                        Tableau de Bord Décisionnel
                    </h2>

                    <p class="text-slate-500 font-medium mt-1">
                        Reporting et analyse des performances
                    </p>
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

                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Effectif Actif
                    </p>

                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-3xl font-black text-slate-800">
                            {{ stats.activeEmployees || 0 }}
                        </h3>

                        <span class="text-slate-400 font-medium">
                            / {{ stats.employees || 0 }}
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                            <Briefcase class="w-6 h-6" />
                        </div>

                        <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-full">
                            Actives
                        </span>
                    </div>

                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Campagnes
                    </p>

                    <h3 class="text-3xl font-black text-slate-800 mt-1">
                        {{ stats.campaigns || 0 }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                            <ClipboardCheck class="w-6 h-6" />
                        </div>

                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                            {{ stats.pendingTimesheets || 0 }} en attente
                        </span>
                    </div>

                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Affectations
                    </p>

                    <h3 class="text-3xl font-black text-slate-800 mt-1">
                        {{ stats.assignments || 0 }}
                    </h3>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                            <Clock class="w-6 h-6" />
                        </div>

                        <div
                            v-if="stats.overtimeHours > 0"
                            class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-full"
                        >
                            {{ stats.overtimeHours }}h sup.
                        </div>
                    </div>

                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Heures Total
                    </p>

                    <h3 class="text-3xl font-black text-slate-800 mt-1">
                        {{ Math.round(stats.workedHours || 0) }}
                        <span class="text-lg ml-1 text-slate-400">h</span>
                    </h3>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Evolution Chart -->
                <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
                            <TrendingUp class="w-6 h-6 text-indigo-600" />
                            Évolution de l'activité
                        </h3>
                    </div>
                    <div class="h-80">
                        <Chart type="line" :data="evolutionChartData" :options="evolutionChartOptions" class="h-full" />
                    </div>
                </div>

                <!-- Planning Gaps -->
                <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl text-white flex flex-col justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black mb-2">Écart de Planification</h3>
                        <p class="text-indigo-100 text-sm font-medium">Différence entre prévu et réalisé</p>
                    </div>

                    <div class="relative z-10 my-8">
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-5xl font-black">{{ Math.round(planningGaps.gap || 0) }}</h2>
                            <span class="text-xl font-bold text-indigo-200">h</span>
                        </div>
                        <p class="text-indigo-100 text-sm mt-2 font-bold uppercase tracking-wider">Total des écarts</p>
                    </div>

                    <div class="relative z-10 space-y-4">
                        <div class="flex justify-between text-xs font-black uppercase tracking-widest text-indigo-200">
                            <span>Réalisation</span>
                            <span>{{ Math.round((planningGaps.worked / (planningGaps.planned || 1)) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden border border-white/10">
                            <div 
                                class="bg-white h-full rounded-full transition-all duration-1000" 
                                :style="{ width: Math.min((planningGaps.worked / (planningGaps.planned || 1)) * 100, 100) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Background Decoration -->
                    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Campaigns -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <Briefcase class="w-6 h-6 text-rose-600" />
                        Top Campagnes par Agents
                    </h3>
                    <div class="h-80">
                        <Chart type="bar" :data="campaignChartData" :options="campaignChartOptions" class="h-full" />
                    </div>
                </div>

                <!-- Top Employees -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <UserCheck class="w-6 h-6 text-emerald-600" />
                        Top Performeurs (Volume)
                    </h3>
                    
                    <div class="space-y-6">
                        <div v-for="emp in employeeStats" :key="emp.id" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center font-black text-slate-600 border border-slate-100 group-hover:scale-110 transition-transform">
                                    {{ emp.first_name?.charAt(0) }}{{ emp.last_name?.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ emp.first_name }} {{ emp.last_name }}</p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ emp.position?.name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-slate-800">{{ Math.round(emp.timesheet_entries_sum_total_hours || 0) }}h</p>
                                <p class="text-[10px] font-bold text-emerald-500 uppercase">Productif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>