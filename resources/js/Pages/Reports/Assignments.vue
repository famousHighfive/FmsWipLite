<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { GitBranch, User, Megaphone, Calendar } from 'lucide-vue-next';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { ref } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

defineProps({
    assignments: Array
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
</script>

<template>
    <Head title="Rapport Affectations" />

    <AppLayout>
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rapport Affectations</h2>
                <p class="text-slate-500 font-medium">Historique et état actuel des ressources par campagne.</p>
            </div>

            <!-- Table Section -->
            <div class="bg-white/50 backdrop-blur-sm p-8 rounded-3xl border border-white shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <GitBranch class="w-5 h-5 text-blue-600" />
                    Liste des affectations
                </h3>
                
                <DataTable 
                    :value="assignments" 
                    v-model:filters="filters"
                    :globalFilterFields="['employee.user.name', 'campaign.name', 'position.name', 'manager.user.name', 'status']"
                    class="p-datatable-sm" 
                    paginator :rows="15"
                >
                    <template #header>
                        <div class="flex justify-end mb-4">
                            <IconField iconPosition="left">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Filtrage automatique..." class="!rounded-xl !bg-slate-50/50" />
                            </IconField>
                        </div>
                    </template>
                    <Column header="Employé" sortable field="employee.user.name">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                    <User class="w-4 h-4" />
                                </div>
                                <span class="font-bold text-slate-700">{{ data.employee?.user?.name }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Campagne" sortable field="campaign.name">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2 text-blue-600 font-semibold">
                                <Megaphone class="w-3 h-3" />
                                {{ data.campaign?.name }}
                            </div>
                        </template>
                    </Column>
                    <Column header="Poste" sortable field="position.name">
                        <template #body="{ data }">
                            <Tag :value="data.position?.name" severity="info" class="!text-[10px]" />
                        </template>
                    </Column>
                    <Column header="Manager" sortable field="manager.user.name">
                        <template #body="{ data }">
                            <span class="text-sm text-slate-500">{{ data.manager?.user?.name || '—' }}</span>
                        </template>
                    </Column>
                    <Column header="Période" sortable field="start_date">
                        <template #body="{ data }">
                            <div class="text-[10px] font-black text-slate-400 uppercase">
                                {{ new Date(data.start_date).toLocaleDateString() }}
                                <span v-if="data.end_date"> → {{ new Date(data.end_date).toLocaleDateString() }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="status" header="Statut" sortable>
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="data.status === 'actif' ? 'success' : 'secondary'" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
