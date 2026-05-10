<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Chart from 'primevue/chart';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { 
    Clock, 
    TrendingUp, 
    Users, 
    Briefcase,
    Zap,
    Target,
    Award
} from 'lucide-vue-next';

const props = defineProps({
    summary: Object,
    campaigns: Array,
    employees: Array,
    dailyPerformance: Array
});

const performanceChartData = ref(null);
const performanceChartOptions = ref(null);

onMounted(() => {
    initChart();
});

const initChart = () => {
    performanceChartData.value = {
        labels: props.dailyPerformance.map(d => new Date(d.date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })),
        datasets: [
            {
                label: 'Heures Travaillées',
                data: props.dailyPerformance.map(d => d.worked),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Objectif (Prévu)',
                data: props.dailyPerformance.map(d => d.planned),
                borderColor: '#94a3b8',
                borderDash: [5, 5],
                fill: false,
                tension: 0.4
            }
        ]
    };

    performanceChartOptions.value = {
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    };
};
</script>

<template>
    <Head title="Statistiques Avancées" />
    
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-slate-800">Analytique Globale</h2>
                    <p class="text-slate-500 font-medium">Vue d'ensemble de la performance opérationnelle</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-2xl">
                    <TrendingUp class="w-8 h-8 text-indigo-600" />
                </div>
            </div>
        </template>

        <div class="py-8 space-y-8">
            <!-- Top Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                            <Clock class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Totales</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ Math.round(summary.total_worked) }}h</h3>
                    <p class="text-sm text-slate-500 mt-2 font-medium">Sur {{ Math.round(summary.total_planned) }}h prévues</p>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                            <Zap class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Efficacité Moy.</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ Math.round(summary.avg_efficiency) }}%</h3>
                    <div class="w-full bg-slate-100 h-2 rounded-full mt-4">
                        <div class="bg-emerald-500 h-full rounded-full" :style="{ width: Math.min(summary.avg_efficiency, 100) + '%' }"></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                            <TrendingUp class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Heures Sup.</p>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ Math.round(summary.total_overtime) }}h</h3>
                    <p class="text-sm text-rose-500 mt-2 font-bold">Volume à surveiller</p>
                </div>

                <div class="bg-indigo-600 p-6 rounded-[2rem] shadow-lg text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl">
                            <Target class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-black text-indigo-100 uppercase tracking-widest">Objectif Atteint</p>
                    </div>
                    <h3 class="text-3xl font-black">{{ Math.round((summary.total_worked / summary.total_planned) * 100) || 0 }}%</h3>
                    <p class="text-sm text-indigo-100 mt-2 font-medium">Tendance positive</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Performance Chart -->
                <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-6">Performance Quotidienne (30 jours)</h3>
                    <div class="h-80">
                        <Chart type="line" :data="performanceChartData" :options="performanceChartOptions" class="h-full" />
                    </div>
                </div>

                <!-- Campaign Leaderboard -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-2 mb-6">
                        <Briefcase class="w-5 h-5 text-indigo-600" />
                        <h3 class="text-xl font-black text-slate-800">Volume par Campagne</h3>
                    </div>
                    <div class="space-y-6">
                        <div v-for="campaign in campaigns" :key="campaign.id" class="group">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-slate-700 text-sm group-hover:text-indigo-600 transition-colors">{{ campaign.name }}</span>
                                <span class="text-xs font-black text-slate-400">{{ Math.round(campaign.assignments_employee_timesheets_entries_sum_total_hours || 0) }}h</span>
                            </div>
                            <div class="w-full bg-slate-50 h-3 rounded-xl overflow-hidden border border-slate-100">
                                <div 
                                    class="bg-indigo-500 h-full rounded-xl transition-all duration-1000" 
                                    :style="{ width: Math.min((campaign.assignments_employee_timesheets_entries_sum_total_hours / summary.total_worked) * 100, 100) + '%' }"
                                ></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 font-bold">{{ campaign.assignments_count }} agents actifs</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Performance Table -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <Award class="w-6 h-6 text-amber-500" />
                        <h3 class="text-xl font-black text-slate-800">Classement des Agents par Volume d'Heures</h3>
                    </div>
                </div>

                <DataTable :value="employees" class="p-datatable-sm" :rows="10" paginator>
                    <Column field="full_name" header="Agent" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center font-black text-slate-600 border border-slate-200">
                                    {{ data.first_name.charAt(0) }}{{ data.last_name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ data.first_name }} {{ data.last_name }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-black">{{ data.position?.name || 'Agent' }}</p>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="timesheet_entries_sum_total_hours" header="Total Heures" sortable>
                        <template #body="{ data }">
                            <span class="font-black text-slate-700">{{ Math.round(data.timesheet_entries_sum_total_hours || 0) }}h</span>
                        </template>
                    </Column>
                    <Column field="timesheet_entries_sum_overtime_hours" header="Heures Sup." sortable>
                        <template #body="{ data }">
                            <span :class="data.timesheet_entries_sum_overtime_hours > 0 ? 'text-rose-600 font-bold' : 'text-slate-400'">
                                {{ Math.round(data.timesheet_entries_sum_overtime_hours || 0) }}h
                            </span>
                        </template>
                    </Column>
                    <Column header="Productivité">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full" :style="{ width: '85%' }"></div>
                                </div>
                                <span class="text-[10px] font-black text-emerald-600">85%</span>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-datatable-thead > tr > th) {
    background: transparent;
    color: #64748b;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #f1f5f9;
}

:deep(.p-datatable-tbody > tr) {
    border-bottom: 1px solid #f8fafc;
    transition: background 0.2s;
}

:deep(.p-datatable-tbody > tr:hover) {
    background: #f8fafc;
}
</style>
