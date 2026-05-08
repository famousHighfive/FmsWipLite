<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Megaphone, CheckCircle2, XCircle, BarChart3 } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

defineProps({
    campaigns: Array,
    stats: Object
});
</script>

<template>
    <Head title="Rapport Campagnes" />

    <AppLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rapport Campagnes</h2>
                <p class="text-slate-500 font-medium">Analyse des performances et de l'état de vos campagnes.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <Megaphone class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Campagnes</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.total }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-600 mb-4">
                        <CheckCircle2 class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Actives</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.active }}</h1>
                </div>

                <div class="bg-white/50 backdrop-blur-sm p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-600 mb-4">
                        <XCircle class="w-6 h-6" />
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Clôturées</p>
                    <h1 class="text-3xl font-black text-slate-800 mt-1">{{ stats.closed }}</h1>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <BarChart3 class="w-5 h-5 text-blue-600" />
                    Détail par campagne
                </h3>
                
                <DataTable :value="campaigns" class="p-datatable-sm" paginator :rows="10">
                    <Column field="name" header="Campagne" sortable />
                    <Column field="status" header="Statut" sortable>
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="data.status === 'active' ? 'success' : 'secondary'" />
                        </template>
                    </Column>
                    <Column field="assignments_count" header="Ressources" sortable>
                        <template #body="{ data }">
                            <span class="font-bold">{{ data.assignments_count }}</span>
                        </template>
                    </Column>
                    <Column header="Activité">
                        <template #body="{ data }">
                            <div class="w-32 h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600" :style="{ width: (data.assignments_count > 0 ? 100 : 0) + '%' }"></div>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
